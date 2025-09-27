<template>
  <div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Program Information</h4>
            <div>
              <button class="btn btn-outline-secondary btn-sm me-2" @click="goBack">
                <i class="fas fa-arrow-left"></i> Back
              </button>
              <button class="btn btn-primary btn-sm me-2" @click="editProgram">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button class="btn btn-warning btn-sm" @click="archiveProgram">
                <i class="fas fa-archive"></i> Archive
              </button>
            </div>
          </div>
          <div class="card-body" v-if="program">
            <div class="row">
              <div class="col-md-2">
                <strong>Code:</strong>
                <p class="text-muted">{{ program.code }}</p>
              </div>
              <div class="col-md-3">
                <strong>Name:</strong>
                <p class="text-muted">{{ program.name }}</p>
              </div>
              <div class="col-md-3">
                <strong>Department:</strong>
                <p class="text-muted">{{ program.department?.name || 'N/A' }}</p>
              </div>
              <div class="col-md-2">
                <strong>Duration:</strong>
                <p class="text-muted">{{ program.duration }} years</p>
              </div>
              <div class="col-md-2">
                <strong>Status:</strong>
                <span class="badge" :class="getStatusClass(program.status)">{{ program.status }}</span>
              </div>
            </div>
          </div>
          <div class="card-body" v-else>
            <div class="text-center">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Program Details Section -->
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Program Details</h5>
          </div>
          <div class="card-body" v-if="program">
            <div class="row">
              <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <td><strong>Program Code:</strong></td>
                      <td>{{ program.code }}</td>
                    </tr>
                    <tr>
                      <td><strong>Program Name:</strong></td>
                      <td>{{ program.name }}</td>
                    </tr>
                    <tr>
                      <td><strong>Department:</strong></td>
                      <td>{{ program.department?.name || 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td><strong>Degree Level:</strong></td>
                      <td>{{ program.degree_level }}</td>
                    </tr>
                    <tr>
                      <td><strong>Duration:</strong></td>
                      <td>{{ program.duration }} years</td>
                    </tr>
                    <tr>
                      <td><strong>Status:</strong></td>
                      <td><span class="badge" :class="getStatusClass(program.status)">{{ program.status }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <h6>Additional Details</h6>
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <td><strong>Created:</strong></td>
                      <td>{{ formatDate(program.created_at) }}</td>
                    </tr>
                    <tr>
                      <td><strong>Last Updated:</strong></td>
                      <td>{{ formatDate(program.updated_at) }}</td>
                    </tr>
                    <tr v-if="program.description">
                      <td><strong>Description:</strong></td>
                      <td>{{ program.description }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Majors Section -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Program Majors</h5>
            <button class="btn btn-success btn-sm" @click="addMajor">
              <i class="fas fa-plus"></i> Add Major
            </button>
          </div>
          <div class="card-body">
            <div v-if="majors.length > 0">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="major in majors" :key="major.id">
                      <td>{{ major.code }}</td>
                      <td>{{ major.name }}</td>
                      <td>{{ major.description || 'N/A' }}</td>
                      <td>
                        <span class="badge" :class="getStatusClass(major.status)">{{ major.status }}</span>
                      </td>
                      <td>{{ formatDate(major.created_at) }}</td>
                      <td>
                        <button class="btn btn-outline-primary btn-sm me-1" @click="viewMajor(major.id)">
                          <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-outline-secondary btn-sm me-1" @click="editMajor(major.id)">
                          <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm" @click="deleteMajor(major.id)">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div v-else class="text-center py-4">
              <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
              <p class="text-muted">No majors found for this program.</p>
              <button class="btn btn-primary" @click="addMajor">
                <i class="fas fa-plus"></i> Add First Major
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Major Modal -->
  <div class="modal fade" id="addMajorModal" tabindex="-1" aria-labelledby="addMajorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addMajorModalLabel">Add New Major</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitMajor">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="majorCode" class="form-label">Major Code <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="majorCode" 
                    v-model="majorForm.code"
                    :class="{ 'is-invalid': majorForm.errors.code }"
                    placeholder="e.g., CS, IT, ENG"
                    required
                  >
                  <div v-if="majorForm.errors.code" class="invalid-feedback">
                    {{ majorForm.errors.code[0] }}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="majorName" class="form-label">Major Name <span class="text-danger">*</span></label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="majorName" 
                    v-model="majorForm.name"
                    :class="{ 'is-invalid': majorForm.errors.name }"
                    placeholder="e.g., Computer Science"
                    required
                  >
                  <div v-if="majorForm.errors.name" class="invalid-feedback">
                    {{ majorForm.errors.name[0] }}
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="majorDescription" class="form-label">Description</label>
              <textarea 
                class="form-control" 
                id="majorDescription" 
                rows="3"
                v-model="majorForm.description"
                :class="{ 'is-invalid': majorForm.errors.description }"
                placeholder="Enter major description..."
              ></textarea>
              <div v-if="majorForm.errors.description" class="invalid-feedback">
                {{ majorForm.errors.description[0] }}
              </div>
            </div>
            <div class="mb-3">
              <label for="majorStatus" class="form-label">Status <span class="text-danger">*</span></label>
              <select 
                class="form-select" 
                id="majorStatus" 
                v-model="majorForm.status"
                :class="{ 'is-invalid': majorForm.errors.status }"
                required
              >
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
              </select>
              <div v-if="majorForm.errors.status" class="invalid-feedback">
                {{ majorForm.errors.status[0] }}
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button 
            type="button" 
            class="btn btn-primary" 
            @click="submitMajor"
            :disabled="majorForm.processing"
          >
            <span v-if="majorForm.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
            {{ majorForm.processing ? 'Creating...' : 'Create Major' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  programId: {
    type: [String, Number],
    required: true
  }
})

// Reactive data
const program = ref(null)
const majors = ref([])
const loading = ref(false)

// Major form data
const majorForm = ref({
  code: '',
  name: '',
  description: '',
  status: 'active',
  processing: false,
  errors: {}
})

// Get program ID from props
const programId = props.programId

// Fetch program details
const fetchProgram = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/programs/${programId}`)
    program.value = response.data.data
  } catch (error) {
    console.error('Error fetching program:', error)
    alert('Error loading program details')
  } finally {
    loading.value = false
  }
}

// Fetch majors for this program
const fetchMajors = async () => {
  try {
    const response = await axios.get(`/api/programs/${programId}/majors`)
    majors.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching majors:', error)
    // Set empty array if endpoint doesn't exist yet
    majors.value = []
  }
}

// Utility functions
const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'active':
      return 'bg-success'
    case 'inactive':
      return 'bg-secondary'
    case 'archived':
      return 'bg-warning'
    default:
      return 'bg-secondary'
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString()
}

// Navigation functions
const goBack = () => {
  window.location.href = '/admin/programs'
}

const editProgram = () => {
  window.location.href = `/admin/programs/${programId}/edit`
}

const archiveProgram = async () => {
  if (confirm('Are you sure you want to archive this program?')) {
    try {
      await axios.patch(`/api/programs/${programId}/archive`)
      alert('Program archived successfully')
      await fetchProgram()
    } catch (error) {
      console.error('Error archiving program:', error)
      alert('Error archiving program')
    }
  }
}

// Major functions
const addMajor = () => {
  // Reset form
  majorForm.value = {
    code: '',
    name: '',
    description: '',
    status: 'active',
    processing: false,
    errors: {}
  }
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('addMajorModal'))
  modal.show()
}

const submitMajor = async () => {
  majorForm.value.processing = true
  majorForm.value.errors = {}
  
  try {
    const response = await axios.post(`/api/programs/${programId}/majors`, {
      code: majorForm.value.code,
      name: majorForm.value.name,
      description: majorForm.value.description,
      status: majorForm.value.status
    })
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('addMajorModal'))
    modal.hide()
    
    // Refresh majors list
    await fetchMajors()
    
    // Show success message
    alert('Major created successfully')
    
  } catch (err) {
    if (err.response && err.response.status === 422) {
      // Validation errors
      majorForm.value.errors = err.response.data.errors || {}
    } else {
      console.error('Error creating major:', err)
      alert('Failed to create major')
    }
  } finally {
    majorForm.value.processing = false
  }
}

const viewMajor = (majorId) => {
  window.location.href = `/admin/majors/${majorId}`
}

const editMajor = (majorId) => {
  window.location.href = `/admin/majors/${majorId}/edit`
}

const deleteMajor = async (majorId) => {
  if (confirm('Are you sure you want to delete this major? This action cannot be undone.')) {
    try {
      await axios.delete(`/api/majors/${majorId}`)
      alert('Major deleted successfully')
      await fetchMajors()
    } catch (error) {
      console.error('Error deleting major:', error)
      alert('Error deleting major')
    }
  }
}

// Initialize component
onMounted(async () => {
  await fetchProgram()
  await fetchMajors()
})
</script>

<style scoped>
.table-borderless td {
  border: none;
  padding: 0.5rem 0;
}

.badge {
  font-size: 0.75em;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>