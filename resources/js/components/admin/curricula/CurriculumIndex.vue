<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-book me-2"></i>Curricula Management
                    </h5>
                    <button 
                        class="btn btn-primary" 
                        @click="showAddModal = true"
                    >
                        <i class="ti ti-plus me-1"></i>Add Curriculum
                    </button>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Filter by Program</label>
                            <select class="form-select" v-model="filters.program" @change="applyFilters">
                                <option value="">All Programs</option>
                                <option v-for="program in programs" :key="program.id" :value="program.id">
                                    {{ program.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter by Status</label>
                            <select class="form-select" v-model="filters.status" @change="applyFilters">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Items per page</label>
                            <select class="form-select" v-model="pagination.per_page" @change="applyFilters">
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

                    <!-- Curricula Table -->
                    <div v-else class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Effective Year</th>
                                    <th>Status</th>
                                    <th>Total Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="groupedCurricula.length > 0">
                                    <template v-for="group in paginatedCurricula" :key="group.program_id">
                                        <!-- Program Group Header -->
                                        <tr class="table-secondary">
                                            <td colspan="6" class="fw-bold">
                                                <i class="ti ti-school me-2"></i>{{ group.program_name }} ({{ group.curricula.length }} curricula)
                                            </td>
                                        </tr>
                                        <!-- Curricula in this program -->
                                        <tr v-for="curriculum in group.curricula" :key="curriculum.id">
                                            <td>{{ curriculum.name }}</td>
                                            <td>{{ curriculum.program?.name || 'N/A' }}</td>
                                            <td>{{ curriculum.effective_year }}</td>
                                            <td>
                                                <span class="badge" :class="getStatusBadgeClass(curriculum.status)">
                                                    {{ curriculum.status }}
                                                </span>
                                            </td>
                                            <td>{{ curriculum.total_courses || 0 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        @click="viewCurriculum(curriculum)" 
                                                        title="View"
                                                    >
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                    <button 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        @click="editCurriculum(curriculum)" 
                                                        title="Edit"
                                                    >
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <button 
                                                        v-if="curriculum.status !== 'Archived'"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        @click="archiveCurriculum(curriculum)" 
                                                        title="Archive"
                                                    >
                                                        <i class="ti ti-archive"></i>
                                                    </button>
                                                    <button 
                                                        v-else
                                                        class="btn btn-sm btn-outline-success" 
                                                        @click="restoreCurriculum(curriculum)" 
                                                        title="Restore"
                                                    >
                                                        <i class="ti ti-restore"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <tr v-else>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-book" style="font-size: 2rem;"></i>
                                            <p class="mt-2">No curricula found</p>
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
                                    <button class="page-link" @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
                                        Previous
                                    </button>
                                </li>
                                <li 
                                    v-for="page in getPageNumbers()" 
                                    :key="page" 
                                    class="page-item" 
                                    :class="{ active: page === pagination.current_page }"
                                >
                                    <button class="page-link" @click="goToPage(page)">
                                        {{ page }}
                                    </button>
                                </li>
                                <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                    <button class="page-link" @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
                                        Next
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Add Curriculum Modal -->
            <div class="modal fade" :class="{ show: showAddModal }" :style="{ display: showAddModal ? 'block' : 'none' }" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Curriculum</h5>
                            <button type="button" class="btn-close" @click="showAddModal = false"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="submitForm">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Curriculum Name</label>
                                    <input type="text" class="form-control" id="name" v-model="form.name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="program_id" class="form-label">Program</label>
                                    <select class="form-select" id="program_id" v-model="form.program_id" required>
                                        <option value="">Select a Program</option>
                                        <template v-for="program in programs" :key="program?.id">
                                            <option v-if="program" :value="program.id">
                                                {{ program.name }}
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="effective_year" class="form-label">Effective Year</label>
                                    <input type="number" class="form-control" id="effective_year" v-model="form.effective_year" min="2000" max="2030" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" v-model="form.status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Archived">Archived</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showAddModal = false">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="submitForm" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                {{ loading ? 'Adding...' : 'Add Curriculum' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Curriculum Modal -->
            <div class="modal fade" :class="{ show: showEditModal }" :style="{ display: showEditModal ? 'block' : 'none' }" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Curriculum</h5>
                            <button type="button" class="btn-close" @click="showEditModal = false"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="updateCurriculum">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Curriculum Name</label>
                                    <input type="text" class="form-control" id="edit_name" v-model="editForm.name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_program_id" class="form-label">Program</label>
                                    <select class="form-select" id="edit_program_id" v-model="editForm.program_id" required>
                                        <option value="">Select a Program</option>
                                        <template v-for="program in programs" :key="program?.id">
                                            <option v-if="program" :value="program.id">
                                                {{ program.name }}
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_effective_year" class="form-label">Effective Year</label>
                                    <input type="number" class="form-control" id="edit_effective_year" v-model="editForm.effective_year" min="2000" max="2030" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_status" v-model="editForm.status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Archived">Archived</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="updateCurriculum" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                {{ loading ? 'Updating...' : 'Update Curriculum' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

// Reactive data
const curricula = ref([])
const programs = ref([])
const loading = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const filters = ref({
    program: '',
    status: ''
})
const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0,
    last_page: 1
})
const form = ref({
    name: '',
    program_id: '',
    effective_year: new Date().getFullYear(),
    status: 'Active'
})
const editForm = ref({
    id: null,
    name: '',
    program_id: '',
    effective_year: '',
    status: ''
})

// Computed properties
const filteredCurricula = computed(() => {
    let filtered = curricula.value
    
    if (filters.value.program) {
        filtered = filtered.filter(curriculum => curriculum.program_id == filters.value.program)
    }
    
    if (filters.value.status) {
        filtered = filtered.filter(curriculum => curriculum.status === filters.value.status)
    }
    
    return filtered
})

const groupedCurricula = computed(() => {
    const groups = {}
    
    filteredCurricula.value.forEach(curriculum => {
        const programId = curriculum.program_id || 'no-program'
        const programName = curriculum.program?.name || 'No Program'
        
        if (!groups[programId]) {
            groups[programId] = {
                program_id: programId,
                program_name: programName,
                curricula: []
            }
        }
        
        groups[programId].curricula.push(curriculum)
    })
    
    return Object.values(groups).sort((a, b) => a.program_name.localeCompare(b.program_name))
})

const paginatedCurricula = computed(() => {
    const start = (pagination.value.current_page - 1) * pagination.value.per_page
    const end = start + pagination.value.per_page
    
    // For grouped display, we need to handle pagination differently
    let allCurriculaFlat = []
    groupedCurricula.value.forEach(group => {
        allCurriculaFlat = allCurriculaFlat.concat(group.curricula)
    })
    
    // Update pagination info
    pagination.value.total = allCurriculaFlat.length
    pagination.value.from = Math.min(start + 1, allCurriculaFlat.length)
    pagination.value.to = Math.min(end, allCurriculaFlat.length)
    pagination.value.last_page = Math.ceil(allCurriculaFlat.length / pagination.value.per_page)
    
    // Return paginated groups
    const paginatedFlat = allCurriculaFlat.slice(start, end)
    const paginatedGroups = {}
    
    paginatedFlat.forEach(curriculum => {
        const programId = curriculum.program_id || 'no-program'
        const programName = curriculum.program?.name || 'No Program'
        
        if (!paginatedGroups[programId]) {
            paginatedGroups[programId] = {
                program_id: programId,
                program_name: programName,
                curricula: []
            }
        }
        
        paginatedGroups[programId].curricula.push(curriculum)
    })
    
    return Object.values(paginatedGroups)
})

// Methods
const fetchCurricula = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/curricula')
        curricula.value = response.data
    } catch (error) {
        console.error('Error fetching curricula:', error)
        alert('Error fetching curricula. Please try again.')
    } finally {
        loading.value = false
    }
}

const fetchPrograms = async () => {
    try {
        const response = await axios.get('/api/programs')
        // Handle different response structures
        let programsData = response.data
        if (programsData && typeof programsData === 'object' && programsData.data) {
            programsData = programsData.data // Handle paginated responses
        }
        // Ensure we have an array and filter out any null or undefined programs
        programs.value = Array.isArray(programsData) ? programsData.filter(program => program && program.id) : []
    } catch (error) {
        console.error('Error fetching programs:', error)
        alert('Error fetching programs. Please try again.')
        programs.value = [] // Ensure programs is always an array
    }
}

const submitForm = async () => {
    try {
        loading.value = true
        await axios.post('/api/curricula', form.value)
        showAddModal.value = false
        alert('Curriculum added successfully!')
        resetForm()
        fetchCurricula()
    } catch (error) {
        console.error('Error adding curriculum:', error)
        alert('Error adding curriculum. Please try again.')
    } finally {
        loading.value = false
    }
}

const editCurriculum = (curriculum) => {
    editForm.value = {
        id: curriculum.id,
        name: curriculum.name,
        program_id: curriculum.program_id,
        effective_year: curriculum.effective_year,
        status: curriculum.status
    }
    showEditModal.value = true
}

const updateCurriculum = async () => {
    try {
        loading.value = true
        await axios.put(`/api/curricula/${editForm.value.id}`, editForm.value)
        showEditModal.value = false
        alert('Curriculum updated successfully!')
        fetchCurricula()
    } catch (error) {
        console.error('Error updating curriculum:', error)
        alert('Error updating curriculum. Please try again.')
    } finally {
        loading.value = false
    }
}

const viewCurriculum = (curriculum) => {
    window.location.href = `/admin/curricula/${curriculum.id}`
}

const archiveCurriculum = async (curriculum) => {
    if (confirm(`Are you sure you want to archive "${curriculum.name}"?`)) {
        try {
            loading.value = true
            await axios.patch(`/api/curricula/${curriculum.id}`, { status: 'Archived' })
            alert('Curriculum archived successfully!')
            fetchCurricula()
        } catch (error) {
            console.error('Error archiving curriculum:', error)
            alert('Error archiving curriculum. Please try again.')
        } finally {
            loading.value = false
        }
    }
}

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Active':
            return 'bg-blue-400'
        case 'Inactive':
            return 'bg-orange-400'
        case 'Archived':
            return 'bg-gray-400'
        default:
            return 'bg-gray-400'
    }
}

const resetForm = () => {
    form.value = {
        name: '',
        program_id: '',
        effective_year: new Date().getFullYear(),
        status: 'Active'
    }
}

const restoreCurriculum = async (curriculum) => {
    if (confirm(`Are you sure you want to restore "${curriculum.name}"?`)) {
        try {
            loading.value = true
            await axios.patch(`/api/curricula/${curriculum.id}`, { status: 'Active' })
            alert('Curriculum restored successfully!')
            fetchCurricula()
        } catch (error) {
            console.error('Error restoring curriculum:', error)
            alert('Error restoring curriculum. Please try again.')
        } finally {
            loading.value = false
        }
    }
}

const applyFilters = () => {
    pagination.value.current_page = 1
}

const clearFilters = () => {
    filters.value.program = ''
    filters.value.status = ''
    pagination.value.current_page = 1
}

const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
        pagination.value.current_page = page
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

// Lifecycle
onMounted(() => {
    fetchCurricula()
    fetchPrograms()
})
</script>
