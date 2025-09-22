<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title">Archived Academic Terms</h4>
      <div class="btn-group">
        <button 
          class="btn btn-secondary btn-sm" 
          @click="restoreSelected"
          :disabled="!selectedTerm"
        >
          <i class="fas fa-undo me-1"></i>
          Restore
        </button>
        <button 
          class="btn btn-danger btn-sm" 
          @click="deleteSelected"
          :disabled="!selectedTerm"
        >
          <i class="fas fa-trash me-1"></i>
          Delete Permanently
        </button>
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
            <tr v-for="term in terms" :key="term.id">
              <td>
                <input 
                  type="radio" 
                  name="selectTerm" 
                  :value="term.id"
                  v-model="selectedTerm"
                  @change="selectedTerm = term"
                  class="form-check-input"
                >
              </td>
              <td>
                <strong>{{ term.school_year }}</strong><br>
                <small class="text-muted">{{ term.semester }}</small>
              </td>
              <td>
                <span class="badge bg-red-600" :class="getStatusBadgeClass(term.status)">
                  <i :class="getStatusIcon(term.status)" class="me-1"></i>
                  {{ getStatusText(term.status) }}
                </span>
              </td>
              <td>{{ formatDate(term.created_at) }}</td>
              <td>
                <div class="dropdown">
                  <button 
                    class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                    type="button" 
                    data-bs-toggle="dropdown"
                  >
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="dropdown-item" href="#" @click="restoreTermAction(term)">
                        <i class="fas fa-undo me-2"></i>Restore
                      </a>
                    </li>
                    <li>
                      <a 
                        class="dropdown-item text-danger" 
                        href="#" 
                        @click="deleteTermAction(term)"
                      >
                        <i class="fas fa-trash me-2"></i>Delete Permanently
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
            <tr v-if="terms.length === 0">
              <td colspan="5" class="text-center text-muted py-4">
                <i class="fas fa-archive fa-2x mb-2"></i>
                <br>
                No archived academic terms found.
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
        <nav aria-label="Archived terms pagination" v-if="pagination.last_page > 1">
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
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
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

// API Methods
const fetchArchivedTerms = async (page = 1, perPage = 10) => {
  try {
    loading.value = true
    const response = await axios.get('/api/academic-terms/archived', {
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
    console.error('Error fetching archived terms:', error)
    proxy?.$toast?.error('Failed to load archived academic terms')
  } finally {
    loading.value = false
  }
}

const restoreTerm = async (termId) => {
  try {
    loading.value = true
    const response = await axios.put(`/api/academic-terms/${termId}`, {
      status: 'inactive'
    })
    if (response.data.success) {
      await fetchArchivedTerms(pagination.value.current_page, pagination.value.per_page)
      proxy?.$toast?.success('Academic term restored successfully!')
      return true
    }
  } catch (error) {
    console.error('Error restoring term:', error)
    const message = error.response?.data?.message || 'Failed to restore academic term'
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
      await fetchArchivedTerms(pagination.value.current_page, pagination.value.per_page)
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

// Utility Methods
const getStatusBadgeClass = (status) => {
  return 'badge-danger' // Red badge for archived terms
}

const getStatusIcon = (status) => {
  return 'fas fa-archive'
}

const getStatusText = (status) => {
  return 'Archived'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const restoreSelected = async () => {
  if (selectedTerm.value) {
    const success = await restoreTerm(selectedTerm.value.id)
    if (success) {
      selectedTerm.value = null
    }
  }
}

const deleteSelected = async () => {
  if (selectedTerm.value) {
    if (confirm('Are you sure you want to permanently delete this academic term? This action cannot be undone.')) {
      const success = await deleteTerm(selectedTerm.value.id)
      if (success) {
        selectedTerm.value = null
      }
    }
  }
}

const restoreTermAction = async (term) => {
  const success = await restoreTerm(term.id)
  if (success) {
    selectedTerm.value = null
  }
}

const deleteTermAction = async (term) => {
  if (confirm('Are you sure you want to permanently delete this academic term? This action cannot be undone.')) {
    const success = await deleteTerm(term.id)
    if (success) {
      selectedTerm.value = null
    }
  }
}

// Pagination Methods
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
    fetchArchivedTerms(page, pagination.value.per_page)
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
  console.log('Archive initialTerms:', props.initialTerms)
  
  if (props.initialTerms) {
    // Check if initialTerms has pagination structure
    if (props.initialTerms.data) {
      terms.value = props.initialTerms.data
      pagination.value = {
        current_page: props.initialTerms.current_page || 1,
        last_page: props.initialTerms.last_page || 1,
        per_page: props.initialTerms.per_page || 10,
        total: props.initialTerms.total || 0,
        from: props.initialTerms.from || 0,
        to: props.initialTerms.to || 0,
        prev_page_url: props.initialTerms.prev_page_url || null,
        next_page_url: props.initialTerms.next_page_url || null
      }
    } else if (Array.isArray(props.initialTerms)) {
      // Handle non-paginated data
      terms.value = props.initialTerms
      pagination.value.total = props.initialTerms.length
    } else {
      terms.value = props.initialTerms
    }
  } else {
    fetchArchivedTerms()
  }
})
</script>