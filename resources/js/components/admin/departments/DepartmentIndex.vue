<template>
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>Departments Management</h5>
          <button class="btn btn-primary" @click="openCreateModal">
            <i class="ti ti-plus"></i> Add Department
          </button>
        </div>
        <div class="card-body">
          <!-- Filters -->
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Filter by Department</label>
              <select v-model="filters.department" class="form-select">
                <option value="">All Departments</option>
                <option v-for="dept in departmentOptions" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Filter by Status</label>
              <select v-model="filters.status" class="form-select">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Archived">Archived</option>
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button class="btn btn-secondary me-2" @click="clearFilters">
                <i class="ti ti-filter-off"></i> Clear Filters
              </button>
            </div>
          </div>

          <!-- Data Table -->
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="6" class="text-center">
                    <div class="spinner-border" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="filteredDepartments.length === 0">
                  <td colspan="6" class="text-center text-muted">
                    No departments found
                  </td>
                </tr>
                <tr v-else v-for="department in filteredDepartments" :key="department.id">
                  <td>
                    <span class="badge bg-primary">{{ department.code }}</span>
                  </td>
                  <td>
                    <strong>{{ department.name }}</strong>
                  </td>
                  <td>
                    <span class="text-muted">{{ department.description || 'No description' }}</span>
                  </td>
                  <td>
                    <span 
                      :class="{
                        'badge bg-success': department.status === 'Active',
                        'badge bg-danger': department.status === 'Inactive',
                        'badge bg-secondary': department.status === 'Archived'
                      }"
                    >
                      {{ department.status }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">{{ formatDate(department.created_at) }}</small>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <button 
                        class="btn btn-sm btn-outline-warning" 
                        @click="editDepartment(department)"
                        title="Edit"
                        :disabled="department.status === 'Archived'"
                      >
                        <i class="ti ti-edit"></i>
                      </button>
                      <button 
                        v-if="department.status !== 'Archived'"
                        class="btn btn-sm btn-outline-danger" 
                        @click="archiveDepartment(department)"
                        title="Archive"
                      >
                        <i class="ti ti-archive"></i>
                      </button>
                      <button 
                        v-else
                        class="btn btn-sm btn-outline-success" 
                        @click="restoreDepartment(department)"
                        title="Restore"
                      >
                        <i class="ti ti-restore"></i>
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

  <!-- Department Modal -->
  <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="departmentModalLabel">{{ isEditMode ? 'Edit Department' : 'Create New Department' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <!-- Validation Errors -->
            <div v-if="errors.length > 0" class="alert alert-danger">
              <ul class="mb-0">
                <li v-for="error in errors" :key="error">{{ error }}</li>
              </ul>
            </div>

            <!-- Department Code -->
            <div class="mb-3">
              <label for="departmentCode" class="form-label">Department Code <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control" 
                :class="{ 'is-invalid': fieldErrors.code }"
                id="departmentCode" 
                v-model="form.code"
                placeholder="e.g., CS, ENG, BUS"
                maxlength="10"
                required
              >
              <div v-if="fieldErrors.code" class="invalid-feedback">
                {{ fieldErrors.code[0] }}
              </div>
            </div>

            <!-- Department Name -->
            <div class="mb-3">
              <label for="departmentName" class="form-label">Department Name <span class="text-danger">*</span></label>
              <input 
                type="text" 
                class="form-control" 
                :class="{ 'is-invalid': fieldErrors.name }"
                id="departmentName" 
                v-model="form.name"
                placeholder="e.g., Computer Science"
                maxlength="255"
                required
              >
              <div v-if="fieldErrors.name" class="invalid-feedback">
                {{ fieldErrors.name[0] }}
              </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label for="departmentDescription" class="form-label">Description</label>
              <textarea 
                class="form-control" 
                :class="{ 'is-invalid': fieldErrors.description }"
                id="departmentDescription" 
                v-model="form.description"
                rows="3"
                placeholder="Brief description of the department"
                maxlength="500"
              ></textarea>
              <div v-if="fieldErrors.description" class="invalid-feedback">
                {{ fieldErrors.description[0] }}
              </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label for="departmentStatus" class="form-label">Status <span class="text-danger">*</span></label>
              <select 
                class="form-select" 
                :class="{ 'is-invalid': fieldErrors.status }"
                id="departmentStatus" 
                v-model="form.status"
                required
              >
                <option value="">Select Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
              <div v-if="fieldErrors.status" class="invalid-feedback">
                {{ fieldErrors.status[0] }}
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              {{ submitting ? (isEditMode ? 'Updating...' : 'Creating...') : (isEditMode ? 'Update Department' : 'Create Department') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Archive Confirmation Modal -->
  <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="archiveModalLabel">Archive Department</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to archive <strong>{{ departmentToArchive?.name }}</strong>?</p>
          <p class="text-muted small">This action will change the department status to "Archived" and it will no longer appear in active department lists. This action can be reversed if needed.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" @click="confirmArchive" :disabled="archiving">
            <span v-if="archiving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ archiving ? 'Archiving...' : 'Archive Department' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { Modal } from 'bootstrap'

// Reactive data
const departments = ref([])
const departmentOptions = ref([])
const loading = ref(false)
const submitting = ref(false)
const isEditMode = ref(false)
const editingDepartment = ref(null)
const archiving = ref(false)
const departmentToArchive = ref(null)
const errors = ref([])
const fieldErrors = ref({})
const filters = reactive({
  department: '',
  status: ''
})

const form = reactive({
  code: '',
  name: '',
  description: '',
  status: ''
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0
})

// Computed properties
const filteredDepartments = computed(() => {
  let filtered = departments.value
  
  if (filters.department) {
    filtered = filtered.filter(dept => dept.id === parseInt(filters.department))
  }
  
  if (filters.status) {
    filtered = filtered.filter(dept => dept.status === filters.status)
  }
  
  return filtered
})

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, pagination.current_page - 2)
  const end = Math.min(pagination.last_page, pagination.current_page + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const fetchDepartments = async (page = 1) => {
  try {
    loading.value = true
    const response = await axios.get(`/api/departments?page=${page}`)
    
    departments.value = response.data.data
    Object.assign(pagination, {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    })
  } catch (error) {
    console.error('Error fetching departments:', error)
  } finally {
    loading.value = false
  }
}

const fetchDepartmentOptions = async () => {
  try {
    const response = await axios.get('/api/departments/options')
    departmentOptions.value = response.data
  } catch (error) {
    console.error('Error fetching department options:', error)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const clearFilters = () => {
  filters.department = ''
  filters.status = ''
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    fetchDepartments(page)
  }
}

const openCreateModal = () => {
  isEditMode.value = false
  editingDepartment.value = null
  resetForm()
  clearErrors()
  
  // Show modal using Bootstrap 5 modal
  const modal = new Modal(document.getElementById('departmentModal'))
  modal.show()
}

const openEditModal = (department) => {
  isEditMode.value = true
  editingDepartment.value = department
  
  // Populate form with department data
  form.code = department.code
  form.name = department.name
  form.description = department.description || ''
  form.status = department.status
  
  clearErrors()
  
  // Show modal using Bootstrap 5 modal
  const modal = new Modal(document.getElementById('departmentModal'))
  modal.show()
}

const resetForm = () => {
  form.code = ''
  form.name = ''
  form.description = ''
  form.status = ''
}

const clearErrors = () => {
  errors.value = []
  fieldErrors.value = {}
}

const submitForm = async () => {
  try {
    submitting.value = true
    clearErrors()
    
    let response
    if (isEditMode.value) {
      // Update existing department
      response = await axios.put(`/api/departments/${editingDepartment.value.id}`, form)
    } else {
      // Create new department
      response = await axios.post('/api/departments', form)
    }
    
    // Close modal
    const modal = Modal.getInstance(document.getElementById('departmentModal'))
    modal.hide()
    
    // Reset form
    resetForm()
    
    // Refresh departments list
    fetchDepartments(pagination.current_page)
    
    // Show success message
    const action = isEditMode.value ? 'updated' : 'created'
    alert(`Department ${action} successfully!`)
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Validation errors
      const validationErrors = error.response.data.errors
      fieldErrors.value = validationErrors
      
      // Collect all error messages
      const allErrors = []
      Object.keys(validationErrors).forEach(field => {
        validationErrors[field].forEach(message => {
          allErrors.push(message)
        })
      })
      errors.value = allErrors
    } else {
      // General error
      const action = isEditMode.value ? 'updating' : 'creating'
      errors.value = [`An error occurred while ${action} the department. Please try again.`]
    }
  } finally {
    submitting.value = false
  }
}

const editDepartment = (department) => {
  openEditModal(department)
}

const archiveDepartment = (department) => {
  departmentToArchive.value = department
  const modal = new Modal(document.getElementById('archiveModal'))
  modal.show()
}

const confirmArchive = async () => {
  if (!departmentToArchive.value) return
  
  try {
    archiving.value = true
    await axios.patch(`/api/departments/${departmentToArchive.value.id}/archive`)
    
    // Close modal
    const modal = Modal.getInstance(document.getElementById('archiveModal'))
    modal.hide()
    
    // Reset
    departmentToArchive.value = null
    
    // Refresh departments list
    fetchDepartments(pagination.current_page)
    
    // Show success message
    alert('Department archived successfully!')
    
  } catch (error) {
    console.error('Error archiving department:', error)
    alert('An error occurred while archiving the department. Please try again.')
  } finally {
    archiving.value = false
  }
}

const restoreDepartment = async (department) => {
  if (confirm(`Are you sure you want to restore "${department.name}"?`)) {
    try {
      await axios.patch(`/api/departments/${department.id}/restore`)
      fetchDepartments(pagination.current_page)
      alert('Department restored successfully!')
    } catch (error) {
      console.error('Error restoring department:', error)
      alert('An error occurred while restoring the department. Please try again.')
    }
  }
}

// Watchers
watch([() => filters.department, () => filters.status], () => {
  if (pagination.current_page !== 1) {
    fetchDepartments(1)
  }
})

// Lifecycle
onMounted(() => {
  fetchDepartments()
  fetchDepartmentOptions()
})
</script>

<style scoped>
.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  border-top: none;
}

.btn-group .btn {
  margin-right: 2px;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

.badge {
  font-size: 0.75rem;
}

.spinner-border {
  width: 1.5rem;
  height: 1.5rem;
}

.pagination {
  margin-bottom: 0;
}

.page-link {
  border: none;
  color: #6c757d;
  background-color: transparent;
}

.page-link:hover {
  color: #495057;
  background-color: #e9ecef;
  border-color: #dee2e6;
}

.page-item.active .page-link {
  background-color: #6c757d;
  border-color: #6c757d;
  color: #fff;
}

.page-item.disabled .page-link {
  color: #adb5bd;
  background-color: transparent;
}
</style>