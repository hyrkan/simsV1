<template>
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h5>Edit Exam</h5>
        </div>
        <div class="card-body">
          <form @submit.prevent="submitForm">
            <!-- Validation Errors -->
            <div v-if="errors.length > 0" class="alert alert-danger">
              <ul class="mb-0">
                <li v-for="error in errors" :key="error">{{ error }}</li>
              </ul>
            </div>

            <div class="row">
              <!-- Academic Term -->
              <div class="col-md-6 mb-3">
                <label for="academicTerm" class="form-label">Academic Term <span class="text-danger">*</span></label>
                <select 
                  class="form-select" 
                  :class="{ 'is-invalid': fieldErrors.academic_term_id }"
                  id="academicTerm" 
                  v-model="form.academic_term_id"
                  required
                >
                  <option value="">Select Academic Term</option>
                  <option v-for="term in academicTerms" :key="term.id" :value="term.id">
                    {{ term.school_year }} - {{ term.semester }}
                  </option>
                </select>
                <div v-if="fieldErrors.academic_term_id" class="invalid-feedback">
                  {{ fieldErrors.academic_term_id[0] }}
                </div>
              </div>

              <!-- Exam Title -->
              <div class="col-md-6 mb-3">
                <label for="examTitle" class="form-label">Exam Title <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.title }"
                  id="examTitle" 
                  v-model="form.title"
                  placeholder="e.g., Mathematics Entrance Exam"
                  maxlength="255"
                  required
                >
                <div v-if="fieldErrors.title" class="invalid-feedback">
                  {{ fieldErrors.title[0] }}
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label for="examDescription" class="form-label">Description</label>
              <textarea 
                class="form-control" 
                :class="{ 'is-invalid': fieldErrors.description }"
                id="examDescription" 
                v-model="form.description"
                placeholder="Brief description of the exam"
                rows="3"
                maxlength="1000"
              ></textarea>
              <div v-if="fieldErrors.description" class="invalid-feedback">
                {{ fieldErrors.description[0] }}
              </div>
            </div>

            <div class="row">
              <!-- Exam Date -->
              <div class="col-md-4 mb-3">
                <label for="examDate" class="form-label">Exam Date <span class="text-danger">*</span></label>
                <input 
                  type="date" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.exam_date }"
                  id="examDate" 
                  v-model="form.exam_date"
                  required
                >
                <div v-if="fieldErrors.exam_date" class="invalid-feedback">
                  {{ fieldErrors.exam_date[0] }}
                </div>
              </div>

              <!-- Start Time -->
              <div class="col-md-4 mb-3">
                <label for="startTime" class="form-label">Start Time <span class="text-danger">*</span></label>
                <input 
                  type="time" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.start_time }"
                  id="startTime" 
                  v-model="form.start_time"
                  required
                >
                <div v-if="fieldErrors.start_time" class="invalid-feedback">
                  {{ fieldErrors.start_time[0] }}
                </div>
              </div>

              <!-- End Time -->
              <div class="col-md-4 mb-3">
                <label for="endTime" class="form-label">End Time <span class="text-danger">*</span></label>
                <input 
                  type="time" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.end_time }"
                  id="endTime" 
                  v-model="form.end_time"
                  required
                >
                <div v-if="fieldErrors.end_time" class="invalid-feedback">
                  {{ fieldErrors.end_time[0] }}
                </div>
              </div>
            </div>

            <div class="row">
              <!-- Location -->
              <div class="col-md-8 mb-3">
                <label for="examLocation" class="form-label">Location <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.location }"
                  id="examLocation" 
                  v-model="form.location"
                  placeholder="e.g., Room 101, Academic Building"
                  maxlength="255"
                  required
                >
                <div v-if="fieldErrors.location" class="invalid-feedback">
                  {{ fieldErrors.location[0] }}
                </div>
              </div>

              <!-- Max Capacity -->
              <div class="col-md-4 mb-3">
                <label for="maxCapacity" class="form-label">Max Capacity <span class="text-danger">*</span></label>
                <input 
                  type="number" 
                  class="form-control" 
                  :class="{ 'is-invalid': fieldErrors.max_capacity }"
                  id="maxCapacity" 
                  v-model="form.max_capacity"
                  min="1"
                  max="1000"
                  required
                >
                <div v-if="fieldErrors.max_capacity" class="invalid-feedback">
                  {{ fieldErrors.max_capacity[0] }}
                </div>
              </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label for="examStatus" class="form-label">Status <span class="text-danger">*</span></label>
              <select 
                class="form-select" 
                :class="{ 'is-invalid': fieldErrors.status }"
                id="examStatus" 
                v-model="form.status"
                required
              >
                <option value="">Select Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
              <div v-if="fieldErrors.status" class="invalid-feedback">
                {{ fieldErrors.status[0] }}
              </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
              <a href="/admin/exams" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Back to Exams
              </a>
              <button type="submit" class="btn btn-primary" :disabled="submitting">
                <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                <i v-else class="ti ti-device-floppy"></i>
                {{ submitting ? 'Updating...' : 'Update Exam' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExamEdit',
  props: {
    exam: {
      type: Object,
      required: true
    },
    academicTerms: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      form: {
        academic_term_id: '',
        title: '',
        description: '',
        exam_date: '',
        start_time: '',
        end_time: '',
        location: '',
        max_capacity: 30,
        status: 'scheduled'
      },
      submitting: false,
      errors: [],
      fieldErrors: {}
    };
  },
  mounted() {
    this.populateForm();
  },
  methods: {
    populateForm() {
      this.form = {
        academic_term_id: this.exam.academic_term_id || '',
        title: this.exam.title || '',
        description: this.exam.description || '',
        exam_date: this.exam.exam_date ? this.exam.exam_date.split('T')[0] : '',
        start_time: this.exam.start_time || '',
        end_time: this.exam.end_time || '',
        location: this.exam.location || '',
        max_capacity: this.exam.max_capacity || 30,
        status: this.exam.status || 'scheduled'
      };
    },
    async submitForm() {
      this.submitting = true;
      this.errors = [];
      this.fieldErrors = {};

      try {
        const response = await fetch(`/admin/exams/${this.exam.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.form)
        });

        const data = await response.json();

        if (response.ok && data.success) {
          this.showSuccess(data.message);
          // Redirect to exams index
          window.location.href = '/admin/exams';
        } else {
          if (response.status === 422 && data.errors) {
            // Validation errors
            this.fieldErrors = data.errors;
            this.errors = Object.values(data.errors).flat();
          } else {
            this.errors = [data.message || 'An error occurred while updating the exam'];
          }
        }
      } catch (error) {
        console.error('Error updating exam:', error);
        this.errors = ['An unexpected error occurred. Please try again.'];
      } finally {
        this.submitting = false;
      }
    },
    showSuccess(message) {
      // You can implement a toast notification system here
      alert(message);
    }
  }
};
</script>