<template>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">{{ curriculum?.name || 'Loading...' }}</h2>
                        <button @click="goBack" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row" v-if="curriculum">
                            <div class="col-md-3">
                                <strong>Effective Year:</strong>
                                <p class="mb-0">{{ curriculum.effective_year }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <p class="mb-0">
                                    <span class="badge" :class="getStatusBadgeClass(curriculum.status)">
                                        {{ curriculum.status }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <strong>Program:</strong>
                                <p class="mb-0">{{ curriculum.program?.name || 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Courses:</strong>
                                <p class="mb-0">{{ courses.length }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button 
                                    class="nav-link" 
                                    :class="{ active: activeTab === 'overview' }"
                                    @click="activeTab = 'overview'"
                                    type="button"
                                >
                                    Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button 
                                    class="nav-link" 
                                    :class="{ active: activeTab === 'structure' }"
                                    @click="activeTab = 'structure'"
                                    type="button"
                                >
                                    Course Structure
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button 
                                    class="nav-link" 
                                    :class="{ active: activeTab === 'courses' }"
                                    @click="activeTab = 'courses'"
                                    type="button"
                                >
                                    Courses List
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="tab-pane">
                            <div class="row" v-if="curriculum">
                                <div class="col-md-6">
                                    <h5>Basic Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ curriculum.name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Effective Year:</strong></td>
                                            <td>{{ curriculum.effective_year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge" :class="getStatusBadgeClass(curriculum.status)">
                                                    {{ curriculum.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Program:</strong></td>
                                            <td>{{ curriculum.program?.name || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>{{ curriculum.program?.department?.name || 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Statistics</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Courses:</strong></td>
                                            <td>{{ courses.length }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Units:</strong></td>
                                            <td>{{ totalUnits }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Years:</strong></td>
                                            <td>{{ yearLevels.length }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ formatDate(curriculum.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ formatDate(curriculum.updated_at) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Course Structure Tab -->
                        <div v-if="activeTab === 'structure'" class="tab-pane">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Course Structure</h5>
                                <button @click="showAddCourseModal = true" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Course
                                </button>
                            </div>
                            
                            <div v-for="year in yearLevels" :key="year" class="mb-4">
                                <h6 class="text-primary border-bottom pb-2">Year {{ year }}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">1st Semester</h6>
                                        <div class="course-list">
                                            <div 
                                                v-for="course in getCoursesByYearSemester(year, 1)" 
                                                :key="course.id"
                                                class="course-item p-2 mb-2 border rounded"
                                            >
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ course.code }}</strong> - {{ course.title }}
                                                        <br>
                                                        <small class="text-muted">{{ course.units }} units</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button @click="editCourse(course)" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button @click="removeCourse(course)" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="getCoursesByYearSemester(year, 1).length === 0" class="text-muted text-center p-3">
                                                No courses for this semester
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">2nd Semester</h6>
                                        <div class="course-list">
                                            <div 
                                                v-for="course in getCoursesByYearSemester(year, 2)" 
                                                :key="course.id"
                                                class="course-item p-2 mb-2 border rounded"
                                            >
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ course.code }}</strong> - {{ course.title }}
                                                        <br>
                                                        <small class="text-muted">{{ course.units }} units</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button @click="editCourse(course)" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button @click="removeCourse(course)" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="getCoursesByYearSemester(year, 2).length === 0" class="text-muted text-center p-3">
                                                No courses for this semester
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Courses List Tab -->
                        <div v-if="activeTab === 'courses'" class="tab-pane">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>All Courses</h5>
                                <button @click="showAddCourseModal = true" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Course
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Title</th>
                                            <th>Units</th>
                                            <th>Year Level</th>
                                            <th>Semester</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="course in courses" :key="course.id">
                                            <td><strong>{{ course.code }}</strong></td>
                                            <td>{{ course.title }}</td>
                                            <td>{{ course.units }}</td>
                                            <td>{{ course.year_level }}</td>
                                            <td>{{ course.semester }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button @click="editCourse(course)" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button @click="removeCourse(course)" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="courses.length === 0">
                                            <td colspan="6" class="text-center text-muted">No courses found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Course Modal -->
        <div v-if="showAddCourseModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Course</h5>
                        <button @click="showAddCourseModal = false" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="addCourse">
                            <div class="mb-3">
                                <label class="form-label">Course Code</label>
                                <input v-model="courseForm.code" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course Title</label>
                                <input v-model="courseForm.title" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Units</label>
                                <input v-model="courseForm.units" type="number" class="form-control" min="1" max="6" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Year Level</label>
                                <select v-model="courseForm.year_level" class="form-select" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <select v-model="courseForm.semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click="showAddCourseModal = false" type="button" class="btn btn-secondary">Cancel</button>
                        <button @click="addCourse" type="button" class="btn btn-primary">Add Course</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Get curriculum ID from URL or props
const getCurriculumId = () => {
    const pathParts = window.location.pathname.split('/')
    return pathParts[pathParts.length - 1]
}

// Reactive data
const curriculum = ref(null)
const courses = ref([])
const activeTab = ref('overview')
const loading = ref(false)
const showAddCourseModal = ref(false)

const courseForm = ref({
    code: '',
    title: '',
    units: 3,
    year_level: '',
    semester: '',
    curriculum_id: getCurriculumId()
})

// Computed properties
const yearLevels = computed(() => {
    const years = [...new Set(courses.value.map(course => parseInt(course.year_level)))]
    return years.sort((a, b) => a - b)
})

const totalUnits = computed(() => {
    return courses.value.reduce((total, course) => total + parseInt(course.units || 0), 0)
})

// Methods
const fetchCurriculum = async () => {
    try {
        loading.value = true
        const curriculumId = getCurriculumId()
        const response = await axios.get(`/api/curricula/${curriculumId}`)
        curriculum.value = response.data
    } catch (error) {
        console.error('Error fetching curriculum:', error)
        alert('Error fetching curriculum details')
    } finally {
        loading.value = false
    }
}

const fetchCourses = async () => {
    try {
        const curriculumId = getCurriculumId()
        const response = await axios.get(`/api/curricula/${curriculumId}/courses`)
        courses.value = Array.isArray(response.data) ? response.data : []
    } catch (error) {
        console.error('Error fetching courses:', error)
        courses.value = []
    }
}

const getCoursesByYearSemester = (year, semester) => {
    return courses.value.filter(course => 
        parseInt(course.year_level) === year && parseInt(course.semester) === semester
    )
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

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString()
}

const addCourse = async () => {
    try {
        loading.value = true
        await axios.post('/api/courses', courseForm.value)
        showAddCourseModal.value = false
        resetCourseForm()
        await fetchCourses()
        alert('Course added successfully!')
    } catch (error) {
        console.error('Error adding course:', error)
        alert('Error adding course. Please try again.')
    } finally {
        loading.value = false
    }
}

const editCourse = (course) => {
    // TODO: Implement edit course functionality
    alert('Edit course functionality will be implemented')
}

const removeCourse = async (course) => {
    if (confirm(`Are you sure you want to remove "${course.code} - ${course.title}"?`)) {
        try {
            loading.value = true
            await axios.delete(`/api/courses/${course.id}`)
            await fetchCourses()
            alert('Course removed successfully!')
        } catch (error) {
            console.error('Error removing course:', error)
            alert('Error removing course. Please try again.')
        } finally {
            loading.value = false
        }
    }
}

const resetCourseForm = () => {
    courseForm.value = {
        code: '',
        title: '',
        units: 3,
        year_level: '',
        semester: '',
        curriculum_id: getCurriculumId()
    }
}

const goBack = () => {
    window.location.href = '/admin/curricula'
}

// Lifecycle
onMounted(() => {
    fetchCurriculum()
    fetchCourses()
})
</script>

<style scoped>
.course-item {
    transition: all 0.2s ease;
}

.course-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    background-color: transparent;
}

.course-list {
    min-height: 200px;
}
</style>
