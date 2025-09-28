<template>
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>Exams Management</h5>
          <a href="/admin/exams/create" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create Exam
          </a>
        </div>
        <div class="card-body">
          <!-- Filters -->
          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-label">Search</label>
              <input 
                type="text" 
                class="form-control" 
                v-model="filters.search"
                placeholder="Search exams..."
                @input="debouncedSearch"
              >
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select v-model="filters.status" class="form-select" @change="fetchExams">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Date From</label>
              <input 
                type="date" 
                class="form-control" 
                v-model="filters.date_from"
                @change="fetchExams"
              >
            </div>
            <div class="col-md-2">
              <label class="form-label">Date To</label>
              <input 
                type="date" 
                class="form-control" 
                v-model="filters.date_to"
                @change="fetchExams"
              >
            </div>
            <div class="col-md-1">
              <label class="form-label">Per Page</label>
              <select v-model="filters.per_page" class="form-select" @change="fetchExams">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-secondary" @click="clearFilters">
                <i class="ti ti-filter-off"></i> Clear
              </button>
            </div>
          </div>

          <!-- Data Table -->
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Academic Term</th>
                  <th>Date & Time</th>
                  <th>Location</th>
                  <th>Capacity</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center">
                    <div class="spinner-border" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="exams.length === 0">
                  <td colspan="7" class="text-center text-muted">
                    No exams found
                  </td>
                </tr>
                <tr v-else v-for="exam in exams" :key="exam.id">
                  <td>
                    <strong>{{ exam.title }}</strong>
                    <br>
                    <small class="text-muted">{{ exam.description }}</small>
                  </td>
                  <td>
                    <span v-if="exam.academic_term">
                      {{ exam.academic_term.school_year }} - {{ exam.academic_term.semester }}
                    </span>
                    <span v-else class="text-muted">No term assigned</span>
                  </td>
                  <td>
                    <strong>{{ formatDate(exam.exam_date) }}</strong>
                    <br>
                    <small class="text-muted">{{ exam.start_time }} - {{ exam.end_time }}</small>
                  </td>
                  <td>
                    <i class="ti ti-map-pin"></i> {{ exam.location }}
                  </td>
                  <td>
                    <span class="badge bg-info">{{ exam.max_capacity }}</span>
                  </td>
                  <td>
                    <span 
                      :class="{
                        'badge bg-primary': exam.status === 'scheduled',
                        'badge bg-warning': exam.status === 'ongoing',
                        'badge bg-success': exam.status === 'completed',
                        'badge bg-danger': exam.status === 'cancelled'
                      }"
                    >
                      {{ exam.status.charAt(0).toUpperCase() + exam.status.slice(1) }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <a 
                        :href="`/admin/exams/${exam.id}/edit`"
                        class="btn btn-sm btn-outline-warning" 
                        title="Edit"
                      >
                        <i class="ti ti-edit"></i>
                      </a>
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deleteExam(exam)"
                        title="Delete"
                        :disabled="exam.status === 'ongoing' || exam.status === 'completed'"
                      >
                        <i class="ti ti-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3" v-if="pagination.total > 0">
            <div>
              <small class="text-muted">
                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
              </small>
            </div>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                  <button class="page-link" @click="changePage(pagination.current_page - 1)">
                    Previous
                  </button>
                </li>
                <li 
                  class="page-item" 
                  v-for="page in visiblePages" 
                  :key="page"
                  :class="{ active: page === pagination.current_page }"
                >
                  <button class="page-link" @click="changePage(page)">
                    {{ page }}
                  </button>
                </li>
                <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                  <button class="page-link" @click="changePage(pagination.current_page + 1)">
                    Next
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExamIndex',
  data() {
    return {
      exams: [],
      loading: false,
      filters: {
        search: '',
        status: '',
        date_from: '',
        date_to: '',
        per_page: 10
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      }
    };
  },
  computed: {
    visiblePages() {
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      const delta = 2;
      const range = [];
      const rangeWithDots = [];

      for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
      }

      if (current - delta > 2) {
        rangeWithDots.push(1, '...');
      } else {
        rangeWithDots.push(1);
      }

      rangeWithDots.push(...range);

      if (current + delta < last - 1) {
        rangeWithDots.push('...', last);
      } else {
        rangeWithDots.push(last);
      }

      return rangeWithDots.filter((item, index, arr) => arr.indexOf(item) === index && item !== 1 || index === 0);
    }
  },
  watch: {
    'filters.search'() {
      this.currentPage = 1;
      this.debouncedSearch();
    },
    'filters.status'() {
      this.currentPage = 1;
      this.fetchExams();
    },
    'filters.dateFrom'() {
      this.currentPage = 1;
      this.fetchExams();
    },
    'filters.dateTo'() {
      this.currentPage = 1;
      this.fetchExams();
    }
  },
  created() {
    this.debouncedSearch = this.debounce(this.fetchExams, 300);
    this.fetchExams();
  },
  methods: {
    // Simple debounce implementation
    debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },
    async fetchExams() {
      this.loading = true;
      try {
        const params = new URLSearchParams();
        
        Object.keys(this.filters).forEach(key => {
          if (this.filters[key]) {
            params.append(key, this.filters[key]);
          }
        });
        
        params.append('page', this.pagination.current_page);

        const response = await fetch(`/admin/api/exams?${params}`);
        const data = await response.json();

        if (data.success) {
          this.exams = data.data;
          this.pagination = data.pagination;
        } else {
          this.showError('Failed to fetch exams');
        }
      } catch (error) {
        console.error('Error fetching exams:', error);
        this.showError('An error occurred while fetching exams');
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page;
        this.fetchExams();
      }
    },
    clearFilters() {
      this.filters = {
        search: '',
        status: '',
        date_from: '',
        date_to: '',
        per_page: 10
      };
      this.pagination.current_page = 1;
      this.fetchExams();
    },
    async deleteExam(exam) {
      if (!confirm(`Are you sure you want to delete the exam "${exam.title}"?`)) {
        return;
      }

      try {
        const response = await fetch(`/admin/exams/${exam.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        });

        const data = await response.json();

        if (data.success) {
          this.showSuccess(data.message);
          this.fetchExams();
        } else {
          this.showError(data.message || 'Failed to delete exam');
        }
      } catch (error) {
        console.error('Error deleting exam:', error);
        this.showError('An error occurred while deleting the exam');
      }
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    showSuccess(message) {
      // You can implement a toast notification system here
      alert(message);
    },
    showError(message) {
      // You can implement a toast notification system here
      alert(message);
    }
  }
};
</script>