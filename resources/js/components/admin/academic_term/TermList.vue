<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title">Academic Terms</h4>
      <div class="btn-group">
        <button 
          class="btn btn-primary btn-sm" 
          @click="showCreateModal = true"
        >
          <i class="fas fa-plus me-1"></i>
          Add New Term
        </button>
        <button 
          class="btn btn-success btn-sm" 
          @click="activateSelected"
          :disabled="!selectedTerm || selectedTerm.status === 'active'"
        >
          <i class="fas fa-check me-1"></i>
          Activate
        </button>
        <button 
          class="btn btn-warning btn-sm" 
          @click="archiveSelected"
          :disabled="!selectedTerm || selectedTerm.status === 'archived'"
        >
          <i class="fas fa-archive me-1"></i>
          Archive
        </button>
        <a 
          href="/admin/academic-terms-archive" 
          class="btn btn-secondary btn-sm"
        >
          <i class="fas fa-folder-open me-1"></i>
          View Archive
        </a>
      </div>
    </div>
    
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th width="50">
                <input 
                  type="radio" 
                  name="selectTerm" 
                  @change="selectedTerm = null"
                  class="form-check-input"
                >
              </th>
              <th>Academic Term</th>
              <th>Status</th>
              <th>Created Date</th>
              <th width="100">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="term in terms" :key="term.id" class="align-middle">
              <td>
                <input 
                  type="radio" 
                  name="selectTerm" 
                  :value="term"
                  v-model="selectedTerm"
                  class="form-check-input"
                >
              </td>
              <td>
                <strong>{{ term.school_year }}, {{ term.semester }}</strong>
              </td>
              <td>
                <span 
                  class="badge" 
                  :class="getStatusBadgeClass(term.status)"
                >
                  <i :class="getStatusIcon(term.status)" class="me-1"></i>
                  {{ getStatusText(term.status) }}
                </span>
              </td>
              <td>
                {{ formatDate(term.created_at) }}
              </td>
              <td>
                <div class="dropdown">
                  <button 
                    class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                    type="button" 
                    :id="'dropdown-' + term.id"
                    data-bs-toggle="dropdown"
                  >
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="dropdown-item" href="#" @click="editTermAction(term)">
                        <i class="fas fa-edit me-2"></i>Edit
                      </a>
                    </li>
                    <li>
                      <a 
                        class="dropdown-item" 
                        href="#" 
                        @click="deleteTermAction(term)"
                        v-if="term.status !== 'active'"
                      >
                        <i class="fas fa-trash me-2"></i>Delete
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
            <tr v-if="terms.length === 0">
              <td colspan="5" class="text-center text-muted py-4">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                <br>
                No academic terms found. Click "Add New Term" to create one.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-4" v-if="pagination.total > 0">
        <div>
          <small class="text-muted">
            Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} entries
          </small>
        </div>
        <nav aria-label="Academic terms pagination" v-if="pagination.last_page > 1">
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
              <a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page - 1)" tabindex="-1">Previous</a>
            </li>
            <li 
              v-for="page in getPageNumbers()" 
              :key="page" 
              class="page-item" 
              :class="{ active: page === pagination.current_page }"
            >
              <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
              <a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page + 1)">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Create/Edit Modal -->
  <div 
    class="modal fade" 
    id="termModal" 
    tabindex="-1" 
    v-if="showCreateModal || showEditModal"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ showEditModal ? 'Edit Academic Term' : 'Add New Academic Term' }}
          </h5>
          <button 
            type="button" 
            class="btn-close" 
            @click="closeModal"
          ></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveTerm">
            <div class="mb-3">
              <label for="school_year" class="form-label">School Year</label>
              <input 
                type="text" 
                class="form-control" 
                id="school_year"
                v-model="termForm.school_year"
                placeholder="e.g., SY 2024-2025"
                required
              >
            </div>
            <div class="mb-3">
              <label for="semester" class="form-label">Semester</label>
              <select 
                class="form-select" 
                id="semester"
                v-model="termForm.semester"
                required
              >
                <option value="">Select Semester</option>
                <option value="1st Sem">1st Semester</option>
                <option value="2nd Sem">2nd Semester</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    id="start_date"
                    v-model="termForm.start_date"
                    required
                  >
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="end_date" class="form-label">End Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    id="end_date"
                    v-model="termForm.end_date"
                    required
                  >
                </div>
              </div>
            </div>
            <div class="mb-3" v-if="showEditModal">
              <label for="status" class="form-label">Status</label>
              <select 
                class="form-select" 
                id="status"
                v-model="termForm.status"
                required
              >
                <option value="inactive">Inactive</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button 
            type="button" 
            class="btn btn-secondary" 
            @click="closeModal"
          >
            Cancel
          </button>
          <button 
            type="button" 
            class="btn btn-primary" 
            @click="saveTerm"
            :disabled="!termForm.school_year || !termForm.semester"
          >
            {{ showEditModal ? 'Update' : 'Create' }} Term
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, nextTick, getCurrentInstance, onMounted } from 'vue'
import axios from 'axios'

// Get the current instance to access $toast
const { proxy } = getCurrentInstance()

// Props
const props = defineProps({
  initialTerms: {
    type: [Array, Object],
    default: () => []
  }
})

// Reactive data
const terms = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null
})

const selectedTerm = ref(null)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingTerm = ref(null)

const termForm = reactive({
  school_year: '',
  semester: '',
  start_date: '',
  end_date: '',
  status: 'inactive'
})

// API Methods
const fetchTerms = async (page = 1, perPage = 10) => {
  try {
    loading.value = true
    const response = await axios.get('/api/academic-terms', {
      params: {
        page: page,
        per_page: perPage
      }
    })
    if (response.data.success) {
      terms.value = response.data.data
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Error fetching terms:', error)
    proxy?.$toast?.error('Failed to load academic terms')
  } finally {
    loading.value = false
  }
}

const createTerm = async (termData) => {
  try {
    loading.value = true
    const response = await axios.post('/api/academic-terms', termData)
    if (response.data.success) {
      await fetchTerms(pagination.value.current_page, pagination.value.per_page) // Refresh the list
      proxy?.$toast?.success(response.data.message)
      return true
    }
  } catch (error) {
    console.error('Error creating term:', error)
    const message = error.response?.data?.message || 'Failed to create academic term'
    proxy?.$toast?.error(message)
    return false
  } finally {
    loading.value = false
  }
}

const updateTerm = async (termId, termData) => {
  try {
    loading.value = true
    const response = await axios.put(`/api/academic-terms/${termId}`, termData)
    if (response.data.success) {
      await fetchTerms(pagination.value.current_page, pagination.value.per_page) // Refresh the list
      proxy?.$toast?.success(response.data.message)
      return true
    }
  } catch (error) {
    console.error('Error updating term:', error)
    const message = error.response?.data?.message || 'Failed to update academic term'
    proxy?.$toast?.error(message)
    return false
  } finally {
    loading.value = false
  }
}

const deleteTerm = async (termId) => {
  try {
    loading.value = true
    const response = await axios.delete(`/api/academic-terms/${termId}`)
    if (response.data.success) {
      // Check if we're deleting the last item on the current page
      const isLastItemOnPage = terms.value.length === 1
      const isNotFirstPage = pagination.value.current_page > 1
      
      // If deleting last item on a page that's not the first page, go to previous page
      const targetPage = (isLastItemOnPage && isNotFirstPage) 
        ? pagination.value.current_page - 1 
        : pagination.value.current_page
      
      await fetchTerms(targetPage, pagination.value.per_page) // Refresh the list
      proxy?.$toast?.success(response.data.message)
      return true
    }
  } catch (error) {
    console.error('Error deleting term:', error)
    const message = error.response?.data?.message || 'Failed to delete academic term'
    proxy?.$toast?.error(message)
    return false
  } finally {
    loading.value = false
  }
}

const activateTerm = async (termId) => {
  try {
    loading.value = true
    const response = await axios.post(`/api/academic-terms/${termId}/activate`)
    if (response.data.success) {
      await fetchTerms(pagination.value.current_page, pagination.value.per_page) // Refresh the list
      proxy?.$toast?.success(response.data.message)
      return true
    }
  } catch (error) {
    console.error('Error activating term:', error)
    const message = error.response?.data?.message || 'Failed to activate academic term'
    proxy?.$toast?.error(message)
    return false
  } finally {
    loading.value = false
  }
}

const archiveTerm = async (termId) => {
  try {
    loading.value = true
    const response = await axios.post(`/api/academic-terms/${termId}/archive`)
    if (response.data.success) {
      await fetchTerms(pagination.value.current_page, pagination.value.per_page) // Refresh the list
      proxy?.$toast?.success(response.data.message)
      return true
    }
  } catch (error) {
    console.error('Error archiving term:', error)
    const message = error.response?.data?.message || 'Failed to archive academic term'
    proxy?.$toast?.error(message)
    return false
  } finally {
    loading.value = false
  }
}

// Utility Methods
const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'active':
      return 'badge-success'
    case 'archived':
      return 'badge-secondary'
    case 'inactive':
    default:
      return 'badge-warning'
  }
}

const getStatusIcon = (status) => {
  switch(status) {
    case 'active':
      return 'fas fa-check-circle'
    case 'archived':
      return 'fas fa-archive'
    case 'inactive':
    default:
      return 'fas fa-pause-circle'
  }
}

const getStatusText = (status) => {
  switch(status) {
    case 'active':
      return 'Active'
    case 'archived':
      return 'Archived'
    case 'inactive':
    default:
      return 'Inactive'
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const activateSelected = async () => {
  if (selectedTerm.value && selectedTerm.value.status !== 'active') {
    const success = await activateTerm(selectedTerm.value.id)
    if (success) {
      selectedTerm.value = null
      // Refresh the table data
      await fetchTerms()
    }
  }
}

const archiveSelected = async () => {
  if (selectedTerm.value && selectedTerm.value.status !== 'archived') {
    const success = await archiveTerm(selectedTerm.value.id)
    if (success) {
      selectedTerm.value = null
      // Refresh the table data
      await fetchTerms()
    }
  }
}

const editTermAction = (term) => {
  editingTerm.value = term
  Object.assign(termForm, {
    school_year: term.school_year,
    semester: term.semester,
    start_date: term.start_date || '',
    end_date: term.end_date || '',
    status: term.status
  })
  showEditModal.value = true
  nextTick(() => {
    const modal = new bootstrap.Modal(document.getElementById('termModal'))
    modal.show()
  })
}

const deleteTermAction = async (term) => {
  if (confirm('Are you sure you want to delete this academic term?')) {
    const success = await deleteTerm(term.id)
    if (success) {
      // Refresh the table data
      await fetchTerms(pagination.value.current_page, pagination.value.per_page)
    }
  }
}

const saveTerm = async () => {
  let success = false
  
  if (showEditModal.value) {
    // Update existing term
    success = await updateTerm(editingTerm.value.id, termForm)
  } else {
    // Create new term
    success = await createTerm(termForm)
  }
  
  if (success) {
    closeModal()
    // Refresh the table data
    await fetchTerms(pagination.value.current_page, pagination.value.per_page)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingTerm.value = null
  Object.assign(termForm, {
    school_year: '',
    semester: '',
    start_date: '',
    end_date: '',
    status: 'inactive'
  })
  
  // Hide bootstrap modal if it exists
  const modalElement = document.getElementById('termModal')
  if (modalElement) {
    const modal = bootstrap.Modal.getInstance(modalElement)
    if (modal) {
      modal.hide()
    }
  }
}

// Pagination Methods
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
    fetchTerms(page, pagination.value.per_page)
  }
}

const getPageNumbers = () => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  // Show max 5 page numbers
  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)
  
  // Adjust if we're near the beginning or end
  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(last, start + 4)
    } else if (end === last) {
      start = Math.max(1, end - 4)
    }
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
}

// Lifecycle hooks
onMounted(() => {
  // Initialize with props data if available, otherwise fetch from API
  console.log('Initial terms:', props.initialTerms)
  
  if (props.initialTerms) {
    // If we have initial terms from Laravel pagination, extract them
    if (props.initialTerms.data) {
      terms.value = props.initialTerms.data
      pagination.value = {
        current_page: props.initialTerms.current_page,
        last_page: props.initialTerms.last_page,
        per_page: props.initialTerms.per_page,
        total: props.initialTerms.total,
        from: props.initialTerms.from,
        to: props.initialTerms.to,
        prev_page_url: props.initialTerms.prev_page_url,
        next_page_url: props.initialTerms.next_page_url
      }
      console.log('Pagination set:', pagination.value)
    } else if (Array.isArray(props.initialTerms)) {
      terms.value = props.initialTerms
    }
  } else {
    fetchTerms()
  }
})

// Watchers
watch(showCreateModal, (newVal) => {
  if (newVal) {
    nextTick(() => {
      const modal = new bootstrap.Modal(document.getElementById('termModal'))
      modal.show()
    })
  }
})
</script>

<style scoped>
.badge-success {
  background-color: #28a745;
  color: white;
}

.badge-warning {
  background-color: #ffc107;
  color: #212529;
}

.badge-secondary {
  background-color: #6c757d;
  color: white;
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.075);
}

.btn-group .btn {
  margin-left: 0.25rem;
}

.btn-group .btn:first-child {
  margin-left: 0;
}

.modal {
  display: block;
  background-color: rgba(0, 0, 0, 0.5);
}
</style>