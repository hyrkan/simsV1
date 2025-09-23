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

    <!-- Tabs Section -->
    <div class="row">
      <div class="col-md-12">
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
                  <i class="fas fa-info-circle"></i> Overview
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button 
                  class="nav-link" 
                  :class="{ active: activeTab === 'curriculums' }"
                  @click="activeTab = 'curriculums'"
                  type="button"
                >
                  <i class="fas fa-book"></i> Curriculums
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button 
                  class="nav-link" 
                  :class="{ active: activeTab === 'courses' }"
                  @click="activeTab = 'courses'"
                  type="button"
                >
                  <i class="fas fa-graduation-cap"></i> Courses
                </button>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
              <div class="row" v-if="program">
                <div class="col-md-6">
                  <h5>Basic Information</h5>
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
                  <h5>Additional Details</h5>
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

            <!-- Curriculums Tab -->
            <div v-if="activeTab === 'curriculums'" class="tab-pane fade show active">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Curriculums</h5>
                <button class="btn btn-success btn-sm" @click="addCurriculum">
                  <i class="fas fa-plus"></i> Add Curriculum
                </button>
              </div>
              
              <div v-if="curriculums.length > 0">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="curriculum in curriculums" :key="curriculum.id">
                        <td>{{ curriculum.name }}</td>
                        <td>{{ curriculum.version }}</td>
                        <td>{{ formatDate(curriculum.effective_date) }}</td>
                        <td>
                          <span class="badge" :class="getStatusClass(curriculum.status)">{{ curriculum.status }}</span>
                        </td>
                        <td>
                          <button class="btn btn-outline-primary btn-sm me-1" @click="viewCurriculum(curriculum.id)">
                            <i class="fas fa-eye"></i> View
                          </button>
                          <button class="btn btn-outline-secondary btn-sm me-1" @click="editCurriculum(curriculum.id)">
                            <i class="fas fa-edit"></i> Edit
                          </button>
                          <button class="btn btn-outline-danger btn-sm" @click="archiveCurriculum(curriculum.id)">
                            <i class="fas fa-archive"></i> Archive
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                <p class="text-muted">No curriculums found for this program.</p>
                <button class="btn btn-primary" @click="addCurriculum">
                  <i class="fas fa-plus"></i> Add First Curriculum
                </button>
              </div>
            </div>

            <!-- Courses Tab -->
            <div v-if="activeTab === 'courses'" class="tab-pane fade show active">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Courses</h5>
                <div>
                  <select class="form-select d-inline-block w-auto me-2" v-model="selectedCurriculumId" @change="fetchCourses">
                    <option value="">All Curriculums</option>
                    <option v-for="curriculum in curriculums" :key="curriculum.id" :value="curriculum.id">
                      {{ curriculum.name }} ({{ curriculum.version }})
                    </option>
                  </select>
                </div>
              </div>
              
              <div v-if="courses.length > 0">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Curriculum</th>
                        <th>Year/Semester</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="course in courses" :key="course.id">
                        <td>{{ course.code }}</td>
                        <td>{{ course.name }}</td>
                        <td>{{ course.credits }}</td>
                        <td>{{ course.curriculum?.name }}</td>
                        <td>{{ course.year }}/{{ course.semester }}</td>
                        <td>
                          <button class="btn btn-outline-primary btn-sm" @click="viewCourse(course.id)">
                            <i class="fas fa-eye"></i> View
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <p class="text-muted">No courses found.</p>
              </div>
            </div>
          </div>
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
const curriculums = ref([])
const courses = ref([])
const activeTab = ref('overview')
const selectedCurriculumId = ref('')
const loading = ref(false)

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

// Fetch curriculums for this program
const fetchCurriculums = async () => {
  try {
    const response = await axios.get(`/api/programs/${programId}/curriculums`)
    curriculums.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching curriculums:', error)
    // Set empty array if endpoint doesn't exist yet
    curriculums.value = []
  }
}

// Fetch courses for this program
const fetchCourses = async () => {
  try {
    let url = `/api/programs/${programId}/courses`
    if (selectedCurriculumId.value) {
      url += `?curriculum_id=${selectedCurriculumId.value}`
    }
    const response = await axios.get(url)
    courses.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching courses:', error)
    // Set empty array if endpoint doesn't exist yet
    courses.value = []
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

// Curriculum functions
const addCurriculum = () => {
  window.location.href = `/admin/programs/${programId}/curriculums/create`
}

const viewCurriculum = (curriculumId) => {
  window.location.href = `/admin/curriculums/${curriculumId}`
}

const editCurriculum = (curriculumId) => {
  window.location.href = `/admin/curriculums/${curriculumId}/edit`
}

const archiveCurriculum = async (curriculumId) => {
  if (confirm('Are you sure you want to archive this curriculum?')) {
    try {
      await axios.patch(`/api/curriculums/${curriculumId}/archive`)
      alert('Curriculum archived successfully')
      await fetchCurriculums()
    } catch (error) {
      console.error('Error archiving curriculum:', error)
      alert('Error archiving curriculum')
    }
  }
}

// Course functions
const viewCourse = (courseId) => {
  window.location.href = `/admin/courses/${courseId}`
}

// Initialize component
onMounted(async () => {
  await fetchProgram()
  await fetchCurriculums()
  await fetchCourses()
})
</script>

<style scoped>
.nav-tabs .nav-link {
  color: #6c757d;
  border: none;
  border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
  background-color: transparent;
}

.nav-tabs .nav-link:hover {
  color: #0d6efd;
  border-bottom-color: #dee2e6;
}

.table-borderless td {
  border: none;
  padding: 0.5rem 0;
}

.card-header-tabs {
  margin-bottom: -1px;
}

.badge {
  font-size: 0.75em;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>