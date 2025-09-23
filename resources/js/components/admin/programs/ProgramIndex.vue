<template>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="ti ti-school me-2"></i>Programs Management
            </h5>
            <button 
              class="btn btn-primary" 
              @click="openCreateModal"
              data-bs-toggle="modal" 
              :data-bs-target="'#' + modalId"
            >
              <i class="ti ti-plus me-1"></i>Add Program
            </button>
          </div>

          <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
              <div class="col-md-3">
                <label class="form-label">Filter by Department</label>
                <select class="form-select" v-model="filters.department" @change="fetchPrograms(1)">
                  <option value="">All Departments</option>
                  <option v-for="department in departments" :key="department.id" :value="department.id">
                    {{ department.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Filter by Status</label>
                <select class="form-select" v-model="filters.status" @change="fetchPrograms(1)">
                  <option value="">All Status</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Archived">Archived</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Items per page</label>
                <select class="form-select" v-model="pagination.per_page" @change="fetchPrograms(1)">
                  <option value="5">5</option>
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-secondary" @click="clearFilters">
                  <i class="ti ti-filter-off me-1"></i>Clear Filters
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>

            <!-- Programs Table -->
            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Degree Level</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="!loading && programs.length === 0">
                    <td colspan="8" class="text-center py-4 text-muted">
                      <i class="ti ti-inbox me-2"></i>No programs found
                    </td>
                  </tr>
                  <tr v-if="loading">
                    <td colspan="8" class="text-center py-4">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-for="program in programs" :key="program?.id || 'unknown'" v-show="!loading && program">
                    <td>
                      <span class="badge bg-primary">{{ program?.code || 'N/A' }}</span>
                    </td>
                    <td>{{ program?.name || 'N/A' }}</td>
                    <td>{{ program?.department?.name || 'N/A' }}</td>
                    <td>{{ program?.degree_level || 'N/A' }}</td>
                    <td>{{ program?.duration_years || 0 }} {{ (program?.duration_years || 0) === 1 ? 'year' : 'years' }}</td>
                    <td>
                      <span 
                        class="badge"
                        :class="{
                          'bg-success': program?.status === 'Active',
                          'bg-warning': program?.status === 'Inactive',
                          'bg-secondary': program?.status === 'Archived'
                        }"
                      >
                        {{ program?.status || 'Unknown' }}
                      </span>
                    </td>
                    <td>{{ formatDate(program?.created_at) }}</td>
                    <td>
                      <div class="btn-group" role="group">
                        <button 
                          @click="viewProgram(program)"
                          class="btn btn-sm btn-outline-primary" 
                          title="View Details"
                        >
                          <i class="ti ti-eye"></i>
                        </button>
                        <button 
                          class="btn btn-sm btn-outline-warning" 
                          @click="editProgram(program)"
                          title="Edit"
                          :disabled="program?.status === 'Archived'"
                        >
                          <i class="ti ti-edit"></i>
                        </button>
                        <button 
                          v-if="program?.status !== 'Archived'"
                          class="btn btn-sm btn-outline-danger" 
                          @click="archiveProgram(program)"
                          title="Archive"
                        >
                          <i class="ti ti-archive"></i>
                        </button>
                        <button 
                          v-else
                          class="btn btn-sm btn-outline-success" 
                          @click="restoreProgram(program)"
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

  <!-- Program Modal -->
  <div class="modal fade" :id="modalId" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="programModalLabel">
            {{ isEditMode ? 'Edit Program' : 'Add New Program' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="code" class="form-label">Program Code <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="code"
                    v-model="form.code"
                    placeholder="e.g., BSCS, BSA"
                    required
                  >
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Program Name <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="name"
                    v-model="form.name"
                    placeholder="e.g., BS Computer Science"
                    required
                  >
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea 
                class="form-control" 
                id="description"
                v-model="form.description"
                rows="3"
                placeholder="Optional program description"
              ></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                  <select 
                    class="form-select" 
                    id="department_id"
                    v-model="form.department_id"
                    required
                  >
                    <option value="">Select Department</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                      {{ department.name }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="degree_level" class="form-label">Degree Level <span class="text-danger">*</span></label>
                  <select 
                    class="form-select" 
                    id="degree_level"
                    v-model="form.degree_level"
                    required
                  >
                    <option value="">Select Degree Level</option>
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Certificate">Certificate</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="duration_years" class="form-label">Duration (Years) <span class="text-danger">*</span></label>
                  <input 
                    type="number" 
                    class="form-control" 
                    id="duration_years"
                    v-model="form.duration_years"
                    min="1"
                    max="10"
                    required
                  >
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                  <select 
                    class="form-select" 
                    id="status"
                    v-model="form.status"
                    required
                  >
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ isEditMode ? 'Update Program' : 'Save Program' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Archive Modal -->
  <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="archiveModalLabel">Archive Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to archive <strong>{{ programToArchive?.name }}</strong>?</p>          <p class="text-muted small">This action will make the program inactive but can be restored later.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" @click="confirmArchive" :disabled="archiving">
            <span v-if="archiving" class="spinner-border spinner-border-sm me-2" role="status"></span>
            Archive Program
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

// Reactive data
const programs = ref([])
const departments = ref([])
const loading = ref(false)
const submitting = ref(false)
const archiving = ref(false)
const isEditMode = ref(false)
const editingProgram = ref(null)
const programToArchive = ref(null)
const modalId = 'programModal'

const filters = reactive({
  department: '',
  status: ''
})

const form = reactive({
  code: '',
  name: '',
  description: '',
  department_id: '',
  degree_level: '',
  duration_years: '',
  status: 'Active'
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
const fetchPrograms = async (page = 1) => {
  console.log('fetchPrograms called with page:', page)
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.per_page,
      department: filters.department,
      status: filters.status
    }
    
    const response = await axios.get('/api/programs', { params })
    
    // Ensure we have valid data
    const responseData = response.data || {}
    programs.value = Array.isArray(responseData.data) ? responseData.data : []
    
    Object.assign(pagination, {
      current_page: responseData.current_page || 1,
      last_page: responseData.last_page || 1,
      per_page: responseData.per_page || 10,
      total: responseData.total || 0,
      from: responseData.from || 0,
      to: responseData.to || 0
    })
  } catch (error) {
    console.error('Error fetching programs:', error)
    showAlert('Error fetching programs', 'danger')
  } finally {
    loading.value = false
  }
}

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/api/departments')
    departments.value = response.data.data
  } catch (error) {
    console.error('Error fetching departments:', error)
  }
}

const openCreateModal = () => {
  isEditMode.value = false
  editingProgram.value = null
  resetForm()
}

const editProgram = (program) => {
  if (!program) return
  
  isEditMode.value = true
  editingProgram.value = program
  Object.assign(form, {
    code: program.code,
    name: program.name,
    description: program.description || '',
    department_id: program.department_id,
    degree_level: program.degree_level,
    duration_years: program.duration_years,
    status: program.status
  })
  
  const modal = new bootstrap.Modal(document.getElementById(modalId))
  modal.show()
}

const viewProgram = (program) => {
  if (!program) return
  window.location.href = `/admin/program/${program.id}`
}

const submitForm = async () => {
  submitting.value = true
  try {
    const url = isEditMode.value ? `/api/programs/${editingProgram.value.id}` : '/api/programs'
    const method = isEditMode.value ? 'put' : 'post'
    
    await axios[method](url, form)
    
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId))
    modal.hide()
    
    showAlert(
      isEditMode.value ? 'Program updated successfully!' : 'Program created successfully!',
      'success'
    )
    
    fetchPrograms(pagination.current_page)
    resetForm()
  } catch (error) {
    console.error('Error saving program:', error)
    showAlert(
      error.response?.data?.message || 'Error saving program',
      'danger'
    )
  } finally {
    submitting.value = false
  }
}

const archiveProgram = (program) => {
  programToArchive.value = program
  const modal = new bootstrap.Modal(document.getElementById('archiveModal'))
  modal.show()
}

const confirmArchive = async () => {
  if (!programToArchive.value) return
  
  archiving.value = true
  try {
    await axios.patch(`/api/programs/${programToArchive.value.id}/archive`)
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('archiveModal'))
    modal.hide()
    
    showAlert('Program archived successfully!', 'success')
    fetchPrograms(pagination.current_page)
    programToArchive.value = null
  } catch (error) {
    console.error('Error archiving program:', error)
    showAlert('Error archiving program', 'danger')
  } finally {
    archiving.value = false
  }
}

const restoreProgram = async (program) => {
  if (!program || !confirm(`Are you sure you want to restore ${program.name}?`)) return
  
  try {
      await axios.patch(`/api/programs/${program.id}/restore`)
      showAlert('Program restored successfully!', 'success')
      fetchPrograms(pagination.current_page)
    } catch (error) {
      console.error('Error restoring program:', error)
      showAlert('Error restoring program', 'danger')
    }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    fetchPrograms(page)
  }
}

const clearFilters = () => {
  filters.department = ''
  filters.status = ''
  pagination.per_page = 10
  fetchPrograms(1)
}

const resetForm = () => {
  Object.assign(form, {
    code: '',
    name: '',
    description: '',
    department_id: '',
    degree_level: '',
    duration_years: '',
    status: 'Active'
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const showAlert = (message, type) => {
  // You can implement a toast notification system here
  alert(message)
}

// Lifecycle
onMounted(() => {
  console.log('ProgramIndex component mounted')
  fetchPrograms()
  fetchDepartments()
})
</script>

<style scoped>
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
