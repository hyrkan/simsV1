<template>
  <div class="modal fade" id="examScheduleModal" tabindex="-1" aria-labelledby="examScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="examScheduleModalLabel">
            <i class="fas fa-calendar-alt me-2"></i>Select Exam Schedule
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Date Filter -->
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label">Filter by Date:</label>
              <select v-model="selectedDate" class="form-select" @change="filterByDate">
                <option value="">All Dates</option>
                <option v-for="date in availableDates" :key="date" :value="date">
                  {{ formatDate(date) }}
                </option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Filter by Time:</label>
              <select v-model="selectedTimeSlot" class="form-select" @change="filterByTime">
                <option value="">All Times</option>
                <option v-for="exam in exams" :key="`${exam.start_time}-${exam.end_time}`" :value="`${exam.start_time}-${exam.end_time}`">
                  {{ formatTime(exam.start_time) }} - {{ formatTime(exam.end_time) }}
                </option>
              </select>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading exam schedules...</p>
          </div>

          <!-- No Exams Available -->
          <div v-else-if="filteredExams.length === 0" class="text-center py-4">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No exam schedules available</h5>
            <p class="text-muted">
              {{ selectedDate || selectedTimeSlot ? 'Try adjusting your filters' : 'Please check back later for available exam schedules' }}
            </p>
          </div>

          <!-- Exam Schedule Grid -->
          <div v-else class="exam-schedule-grid">
            <div class="row">
              <div v-for="exam in filteredExams" :key="exam.id" class="col-lg-4 col-md-6 mb-3">
                <div 
                  class="exam-card"
                  :class="{ 'selected': selectedExam && selectedExam.id === exam.id }"
                  @click="selectExam(exam)"
                >
                  <div class="exam-card-header">
                    <div class="exam-date">
                      <i class="fas fa-calendar me-2"></i>
                      {{ formatExamDate(exam.exam_date) }}
                    </div>
                    <div class="exam-day">
                      {{ getDayOfWeek(exam.exam_date) }}
                    </div>
                  </div>
                  <div class="exam-card-body">
                    <div class="exam-time">
                      <i class="fas fa-clock me-2"></i>
                      {{ formatTime(exam.start_time) }} - {{ formatTime(exam.end_time) }}
                    </div>
                    <div class="exam-details">
                      <div class="exam-type">
                        <i class="fas fa-tag me-2"></i>
                        {{ exam.exam_type }}
                      </div>
                      <div class="exam-location">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ exam.location }}
                      </div>
                      <div class="exam-capacity">
                        <i class="fas fa-users me-2"></i>
                        {{ exam.capacity }} slots available
                      </div>
                    </div>
                  </div>
                  <div v-if="selectedExam && selectedExam.id === exam.id" class="exam-selected-indicator">
                    <i class="fas fa-check-circle"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancel
          </button>
          <button 
            type="button" 
            class="btn btn-primary" 
            :disabled="!selectedExam"
            data-bs-dismiss="modal"
            @click="confirmSelection"
          >
            <i class="fas fa-check me-2"></i>Select Schedule
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExamScheduleModal',
  props: {
    exams: {
      type: Array,
      default: () => []
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedExam: null,
      selectedDate: '',
      selectedTimeSlot: '',
      filteredExams: []
    }
  },
  computed: {
    availableDates() {
      const dates = [...new Set(this.exams.map(exam => exam.exam_date))];
      return dates.sort();
    },

  },
  methods: {
    selectExam(exam) {
      this.selectedExam = exam;
    },
    confirmSelection() {
      if (this.selectedExam) {
        this.$emit('exam-selected', this.selectedExam);
        // Move focus out of the modal before hiding to avoid aria-hidden/focus conflicts
        const activeEl = document.activeElement;
        if (activeEl && typeof activeEl.blur === 'function') {
          activeEl.blur();
        }
        const triggerBtn = document.querySelector('.exam-select-btn');
        if (triggerBtn && typeof triggerBtn.focus === 'function') {
          triggerBtn.focus();
        }
        // Try to close modal safely via Bootstrap if available; otherwise, remove any leftover backdrop
        const el = document.getElementById('examScheduleModal');
        const bs = window.bootstrap;
        if (el && bs && bs.Modal) {
          const instance = bs.Modal.getInstance(el) || new bs.Modal(el);
          instance.hide();
        } else if (el) {
          // Fallback cleanup to prevent stuck gray overlay/backdrop
          el.classList.remove('show');
          el.style.display = 'none';
          el.removeAttribute('aria-hidden');
          document.body.classList.remove('modal-open');
          document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }
      }
    },
    filterByDate() {
      this.applyFilters();
    },
    filterByTime() {
      this.applyFilters();
    },
    applyFilters() {
      let filtered = [...this.exams];
      
      if (this.selectedDate) {
        filtered = filtered.filter(exam => exam.exam_date === this.selectedDate);
      }
      
      if (this.selectedTimeSlot) {
        filtered = filtered.filter(exam => {
          const examTimeSlot = `${exam.start_time}-${exam.end_time}`;
          return examTimeSlot === this.selectedTimeSlot;
        });
      }
      
      this.filteredExams = filtered;
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    formatExamDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    },
    getDayOfWeek(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { weekday: 'long' });
    },
    formatTime(timeString) {
      if (!timeString) return '';
      
      // Handle time string format (HH:MM:SS or HH:MM)
      const timeParts = timeString.toString().split(':');
      const hours = parseInt(timeParts[0]);
      const minutes = parseInt(timeParts[1] || 0);
      
      // Create a date object for today with the specified time
      const date = new Date();
      date.setHours(hours, minutes, 0, 0);
      
      return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });
    },
    resetModal() {
      this.selectedExam = null;
      this.selectedDate = '';
      this.selectedTimeSlot = '';
      this.filteredExams = [...this.exams];
    }
  },
  watch: {
    exams: {
      handler(newExams) {
        this.filteredExams = [...newExams];
      },
      immediate: true
    }
  },
  mounted() {
    // Reset modal when it's opened
    const modalElement = document.getElementById('examScheduleModal');
    if (modalElement) {
      modalElement.addEventListener('show.bs.modal', () => {
        this.resetModal();
      });
      // Ensure proper cleanup and focus restoration when modal is hidden
      modalElement.addEventListener('hidden.bs.modal', () => {
        const triggerBtn = document.querySelector('.exam-select-btn');
        if (triggerBtn && typeof triggerBtn.focus === 'function') {
          triggerBtn.focus();
        }
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        modalElement.style.display = 'none';
      });
    }
  }
}
</script>

<style scoped>
.exam-schedule-grid {
  max-height: 600px;
  overflow-y: auto;
}

.exam-card {
  border: 2px solid #e9ecef;
  border-radius: 12px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  background: #fff;
}

.exam-card:hover {
  border-color: #0d6efd;
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
  transform: translateY(-2px);
}

.exam-card.selected {
  border-color: #198754;
  background: #f8fff9;
  box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
}

.exam-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e9ecef;
}

.exam-date {
  font-weight: 600;
  color: #495057;
  font-size: 0.95rem;
}

.exam-day {
  font-size: 0.85rem;
  color: #6c757d;
  background: #f8f9fa;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.exam-card-body {
  space-y: 0.5rem;
}

.exam-time {
  font-weight: 600;
  color: #0d6efd;
  font-size: 1rem;
  margin-bottom: 0.75rem;
}

.exam-details > div {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
}

.exam-details > div:last-child {
  margin-bottom: 0;
}

.exam-selected-indicator {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  color: #198754;
  font-size: 1.25rem;
}

.modal-header {
  background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
  color: white;
  border-bottom: none;
}

.modal-header .btn-close {
  filter: invert(1);
}

.modal-title {
  font-weight: 600;
}

.form-label {
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
}

.form-select {
  border: 2px solid #e9ecef;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  padding: 0.5rem 1rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Custom scrollbar for exam grid */
.exam-schedule-grid::-webkit-scrollbar {
  width: 6px;
}

.exam-schedule-grid::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.exam-schedule-grid::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.exam-schedule-grid::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

@media (max-width: 768px) {
  .modal-dialog {
    margin: 0.5rem;
  }
  
  .exam-card {
    padding: 0.75rem;
  }
  
  .exam-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .exam-schedule-grid {
    max-height: 450px;
  }
}
</style>