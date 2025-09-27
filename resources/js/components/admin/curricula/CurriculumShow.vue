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
                                <strong>Total Subjects:</strong>
                                <p class="mb-0">{{ subjects.length }}</p>
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
                                    Subject Structure
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button 
                                    class="nav-link" 
                                    :class="{ active: activeTab === 'subjects' }"
                                    @click="activeTab = 'subjects'"
                                    type="button"
                                >
                                    Subjects List
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
                                        <tbody>
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
                                            <tr>
                                                <td><strong>Curriculum Type:</strong></td>
                                                <td>
                                                    <span class="badge" :class="hasMajorSpecificSubjects ? 'bg-info' : 'bg-secondary'">
                                                        {{ curriculumType }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr v-if="majorsInCurriculum.length > 0">
                                                <td><strong>Majors:</strong></td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span 
                                                            v-for="major in majorsInCurriculum" 
                                                            :key="major.id"
                                                            class="badge bg-primary"
                                                        >
                                                            {{ major.name }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Statistics</h5>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td><strong>Total Subjects:</strong></td>
                                                <td>{{ subjects.length }}</td>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Structure Tab -->
                        <div v-if="activeTab === 'structure'" class="tab-pane">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Subject Structure</h5>
                                <button @click="showAddSubjectModal = true" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Subject
                                </button>
                            </div>
                            
                            <!-- Curriculum Structure Tables -->
                            <div v-for="year in yearLevels" :key="year" class="mb-5">
                                <h4 class="text-uppercase fw-bold mb-3">{{ getYearName(year) }}</h4>
                                
                                <!-- First Semester -->
                                <div v-if="getSubjectsByYearSemester(year, 1).length > 0" class="mb-4">
                                    <h5 class="text-decoration-underline mb-3">First Semester</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 15%;">Course No.</th>
                                                    <th style="width: 45%;">Course Title</th>
                                                    <th style="width: 10%;" class="text-center">Lec Hrs</th>
                                                    <th style="width: 10%;" class="text-center">Lab Hrs</th>
                                                    <th style="width: 10%;" class="text-center">Total</th>
                                                    <th style="width: 10%;" class="text-center">Units</th>
                                                    <th style="width: 15%;" class="text-center">Pre-Requisites</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="subject in getSubjectsByYearSemester(year, 1)" :key="subject.id">
                                                    <td><strong>{{ subject.code }}</strong></td>
                                                    <td>
                                                        {{ subject.title }}
                                                        <div class="mt-1">
                                                            <span class="badge me-1" :class="getSubjectTypeBadgeClass(subject.type)">
                                                                {{ subject.type }}
                                                            </span>
                                                            <span v-if="subject.major" class="badge bg-info">
                                                                {{ subject.major.name }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ subject.lecture_hours || 0 }}</td>
                                                    <td class="text-center">{{ subject.lab_hours || 0 }}</td>
                                                    <td class="text-center">{{ (parseInt(subject.lecture_hours) || 0) + (parseInt(subject.lab_hours) || 0) }}</td>
                                                    <td class="text-center"><strong>{{ subject.pivot?.units_override || subject.units }}</strong></td>
                                                    <td class="text-center">{{ subject.prerequisites || '' }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 1).lecture_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 1).lab_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 1).total_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 1).units }}</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Second Semester -->
                                <div v-if="getSubjectsByYearSemester(year, 2).length > 0" class="mb-4">
                                    <h5 class="text-decoration-underline mb-3">Second Semester</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 15%;">Course No.</th>
                                                    <th style="width: 45%;">Course Title</th>
                                                    <th style="width: 10%;" class="text-center">Lec Hrs</th>
                                                    <th style="width: 10%;" class="text-center">Lab Hrs</th>
                                                    <th style="width: 10%;" class="text-center">Total</th>
                                                    <th style="width: 10%;" class="text-center">Units</th>
                                                    <th style="width: 15%;" class="text-center">Pre-Requisites</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="subject in getSubjectsByYearSemester(year, 2)" :key="subject.id">
                                                    <td><strong>{{ subject.code }}</strong></td>
                                                    <td>
                                                        {{ subject.title }}
                                                        <div class="mt-1">
                                                            <span class="badge me-1" :class="getSubjectTypeBadgeClass(subject.type)">
                                                                {{ subject.type }}
                                                            </span>
                                                            <span v-if="subject.major" class="badge bg-info">
                                                                {{ subject.major.name }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ subject.lecture_hours || 0 }}</td>
                                                    <td class="text-center">{{ subject.lab_hours || 0 }}</td>
                                                    <td class="text-center">{{ (parseInt(subject.lecture_hours) || 0) + (parseInt(subject.lab_hours) || 0) }}</td>
                                                    <td class="text-center"><strong>{{ subject.pivot?.units_override || subject.units }}</strong></td>
                                                    <td class="text-center">{{ subject.prerequisites || '' }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 2).lecture_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 2).lab_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 2).total_hours }}</strong></td>
                                                    <td class="text-center"><strong>{{ getSemesterTotals(year, 2).units }}</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Grand Total Summary -->
                            <div v-if="yearSemesterTotals.byYearSemester.length > 0" class="mt-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-dark">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">CURRICULUM SUMMARY</th>
                                                <th class="text-center">Lec Hrs</th>
                                                <th class="text-center">Lab Hrs</th>
                                                <th class="text-center">Total</th>
                                                <th class="text-center">Units</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-light">
                                            <tr>
                                                <td colspan="2" class="text-center"><strong>GRAND TOTAL</strong></td>
                                                <td class="text-center"><strong>{{ yearSemesterTotals.grandTotal.lecture_hours }}</strong></td>
                                                <td class="text-center"><strong>{{ yearSemesterTotals.grandTotal.lab_hours }}</strong></td>
                                                <td class="text-center"><strong>{{ yearSemesterTotals.grandTotal.total_hours }}</strong></td>
                                                <td class="text-center"><strong>{{ yearSemesterTotals.grandTotal.units }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Subjects List Tab -->
                        <div v-if="activeTab === 'subjects'" class="tab-pane">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>All Subjects</h5>
                                <button @click="showAddSubjectModal = true" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Subject
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Title</th>
                                            <th>Units</th>
                                            <th>Type</th>
                                            <th>Year Level</th>
                                            <th>Semester</th>
                                            <th>Required</th>
                                            <th>Major</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="subject in subjects" :key="subject.id">
                                            <td><strong>{{ subject.code }}</strong></td>
                                            <td>{{ subject.title }}</td>
                                            <td>{{ subject.units }}</td>
                                            <td>{{ subject.type }}</td>
                                            <td>{{ subject.pivot?.year_level }}</td>
                                            <td>{{ subject.pivot?.semester }}</td>
                                            <td>
                                                <span v-if="subject.pivot?.is_required" class="badge bg-success">Required</span>
                                                <span v-else class="badge bg-secondary">Elective</span>
                                            </td>
                                            <td>
                                                <span v-if="subject.major" class="badge bg-info">{{ subject.major.name }}</span>
                                                <span v-else class="text-muted">General</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button @click="editSubject(subject)" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button @click="removeSubject(subject)" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="subjects.length === 0">
                                            <td colspan="9" class="text-center text-muted">No subjects found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Subject Modal -->
        <div v-if="showAddSubjectModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Subject to Curriculum</h5>
                        <button @click="showAddSubjectModal = false" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="addSubject">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                                        <input v-model="subjectForm.code" type="text" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subject Title <span class="text-danger">*</span></label>
                                        <input v-model="subjectForm.title" type="text" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="subjectForm.description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Units <span class="text-danger">*</span></label>
                                        <input v-model="subjectForm.units" type="number" class="form-control" min="1" max="6" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Lecture Hours</label>
                                        <input v-model="subjectForm.lecture_hours" type="number" class="form-control" min="0" max="10">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Lab Hours</label>
                                        <input v-model="subjectForm.lab_hours" type="number" class="form-control" min="0" max="10">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subject Type <span class="text-danger">*</span></label>
                                        <select v-model="subjectForm.type" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="Core">Core</option>
                                            <option value="Major">Major</option>
                                            <option value="Minor">Minor</option>
                                            <option value="Elective">Elective</option>
                                            <option value="General Education">General Education</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Major Specification</label>
                                        <select v-model="subjectForm.major_id" class="form-select">
                                            <option value="">All Majors (General Subject)</option>
                                            <option v-for="major in majors" :key="major.id" :value="major.id">
                                                {{ major.name }} ({{ major.code }})
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Select a specific major if this subject is only for that major, or leave as "All Majors" for general subjects
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h6>Curriculum Placement</h6>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Year Level <span class="text-danger">*</span></label>
                                        <select v-model="subjectForm.year_level" class="form-select" required>
                                            <option value="">Select Year Level</option>
                                            <option value="1">1st Year</option>
                                            <option value="2">2nd Year</option>
                                            <option value="3">3rd Year</option>
                                            <option value="4">4th Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                                        <select v-model="subjectForm.semester" class="form-select" required>
                                            <option value="">Select Semester</option>
                                            <option value="1">1st Semester</option>
                                            <option value="2">2nd Semester</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Order</label>
                                        <input v-model="subjectForm.order" type="number" class="form-control" min="1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input v-model="subjectForm.is_required" class="form-check-input" type="checkbox" id="isRequired">
                                            <label class="form-check-label" for="isRequired">
                                                Required Subject
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Units Override</label>
                                        <input v-model="subjectForm.units_override" type="number" class="form-control" min="1" max="6">
                                        <small class="form-text text-muted">Leave empty to use subject's default units</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click="showAddSubjectModal = false" type="button" class="btn btn-secondary">Cancel</button>
                        <button @click="addSubject" type="button" class="btn btn-primary" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Add Subject
                        </button>
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
const subjects = ref([])
const majors = ref([])
const activeTab = ref('overview')
const loading = ref(false)
const showAddSubjectModal = ref(false)

const subjectForm = ref({
    code: '',
    title: '',
    description: '',
    units: 3,
    lecture_hours: 0,
    lab_hours: 0,
    type: '',
    status: 'Active',
    major_id: '',
    year_level: '',
    semester: '',
    order: 1,
    is_required: true,
    units_override: null
})

// Computed properties
const yearLevels = computed(() => {
    const years = [...new Set(subjects.value.map(subject => parseInt(subject.pivot?.year_level)).filter(Boolean))]
    return years.sort((a, b) => a - b)
})

const totalUnits = computed(() => {
    return subjects.value.reduce((total, subject) => {
        const units = subject.pivot?.units_override || subject.units || 0
        return total + parseInt(units)
    }, 0)
})

const hasMajorSpecificSubjects = computed(() => {
    return subjects.value.some(subject => subject.major)
})

const majorsInCurriculum = computed(() => {
    const majors = subjects.value
        .filter(subject => subject.major)
        .map(subject => subject.major)
        .filter((major, index, self) => 
            index === self.findIndex(m => m.id === major.id)
        )
    return majors
})

const curriculumType = computed(() => {
    if (hasMajorSpecificSubjects.value) {
        if (majorsInCurriculum.value.length === 1) {
            return `Major-Specific (${majorsInCurriculum.value[0].name})`
        } else {
            return `Multi-Major (${majorsInCurriculum.value.length} majors)`
        }
    }
    return 'General Curriculum'
})

const yearSemesterTotals = computed(() => {
    const totals = {}
    let grandTotal = {
        lecture_hours: 0,
        lab_hours: 0,
        total_hours: 0,
        units: 0
    }

    subjects.value.forEach(subject => {
        const year = parseInt(subject.pivot?.year_level)
        const semester = parseInt(subject.pivot?.semester)
        const units = subject.pivot?.units_override || subject.units || 0
        const lectureHours = parseInt(subject.lecture_hours) || 0
        const labHours = parseInt(subject.lab_hours) || 0
        const totalHours = lectureHours + labHours

        if (year && semester) {
            const key = `${year}-${semester}`
            if (!totals[key]) {
                totals[key] = {
                    year,
                    semester,
                    lecture_hours: 0,
                    lab_hours: 0,
                    total_hours: 0,
                    units: 0
                }
            }

            totals[key].lecture_hours += lectureHours
            totals[key].lab_hours += labHours
            totals[key].total_hours += totalHours
            totals[key].units += parseInt(units)

            // Add to grand total
            grandTotal.lecture_hours += lectureHours
            grandTotal.lab_hours += labHours
            grandTotal.total_hours += totalHours
            grandTotal.units += parseInt(units)
        }
    })

    // Convert to array and sort by year then semester
    const sortedTotals = Object.values(totals).sort((a, b) => {
        if (a.year !== b.year) return a.year - b.year
        return a.semester - b.semester
    })

    return {
        byYearSemester: sortedTotals,
        grandTotal
    }
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

const fetchSubjects = async () => {
    try {
        const curriculumId = getCurriculumId()
        const response = await axios.get(`/api/curricula/${curriculumId}/subjects`)
        subjects.value = Array.isArray(response.data) ? response.data : []
    } catch (error) {
        console.error('Error fetching subjects:', error)
        subjects.value = []
    }
}

const fetchMajors = async () => {
    try {
        if (curriculum.value?.program_id) {
            const response = await axios.get(`/api/programs/${curriculum.value.program_id}/majors`)
            majors.value = Array.isArray(response.data.data) ? response.data.data : []
        }
    } catch (error) {
        console.error('Error fetching majors:', error)
        majors.value = []
    }
}

const getSubjectsByYearSemester = (year, semester) => {
    return subjects.value.filter(subject => 
        parseInt(subject.pivot?.year_level) === year && parseInt(subject.pivot?.semester) === semester
    )
}

const getYearName = (year) => {
    const yearNames = {
        1: 'FIRST YEAR',
        2: 'SECOND YEAR', 
        3: 'THIRD YEAR',
        4: 'FOURTH YEAR',
        5: 'FIFTH YEAR'
    }
    return yearNames[year] || `YEAR ${year}`
}

const getSemesterTotals = (year, semester) => {
    const semesterSubjects = getSubjectsByYearSemester(year, semester)
    
    return semesterSubjects.reduce((totals, subject) => {
        const lectureHours = parseInt(subject.lecture_hours) || 0
        const labHours = parseInt(subject.lab_hours) || 0
        const units = parseInt(subject.pivot?.units_override || subject.units) || 0
        
        totals.lecture_hours += lectureHours
        totals.lab_hours += labHours
        totals.total_hours += lectureHours + labHours
        totals.units += units
        
        return totals
    }, {
        lecture_hours: 0,
        lab_hours: 0,
        total_hours: 0,
        units: 0
    })
}

const getSubjectTypeBadgeClass = (type) => {
    switch (type) {
        case 'Core':
            return 'bg-primary'
        case 'Major':
            return 'bg-success'
        case 'Minor':
            return 'bg-warning'
        case 'Elective':
            return 'bg-secondary'
        case 'General Education':
            return 'bg-info'
        default:
            return 'bg-light text-dark'
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

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString()
}

const addSubject = async () => {
    // Frontend validation
    const errors = []
    
    if (!subjectForm.value.code.trim()) {
        errors.push('Subject Code is required')
    }
    if (!subjectForm.value.title.trim()) {
        errors.push('Subject Title is required')
    }
    if (!subjectForm.value.type) {
        errors.push('Subject Type is required')
    }
    if (!subjectForm.value.year_level) {
        errors.push('Year Level is required')
    }
    if (!subjectForm.value.semester) {
        errors.push('Semester is required')
    }
    
    if (errors.length > 0) {
        alert('Please fill in all required fields:\n' + errors.join('\n'))
        return
    }
    
    try {
        loading.value = true
        const curriculumId = getCurriculumId()
        
        // Create the subject first
        const subjectData = {
            code: subjectForm.value.code,
            title: subjectForm.value.title,
            description: subjectForm.value.description,
            units: subjectForm.value.units,
            lecture_hours: subjectForm.value.lecture_hours,
            lab_hours: subjectForm.value.lab_hours,
            type: subjectForm.value.type,
            status: subjectForm.value.status
        }
        
        const subjectResponse = await axios.post('/api/subjects', subjectData)
        const subjectId = subjectResponse.data.id
        
        // Then attach it to the curriculum with pivot data
        const pivotData = {
            subject_id: subjectId,
            major_id: subjectForm.value.major_id || null,
            year_level: subjectForm.value.year_level,
            semester: subjectForm.value.semester,
            order: subjectForm.value.order,
            is_required: subjectForm.value.is_required,
            units_override: subjectForm.value.units_override
        }
        
        await axios.post(`/api/curricula/${curriculumId}/subjects`, pivotData)
        
        showAddSubjectModal.value = false
        resetSubjectForm()
        await fetchSubjects()
        alert('Subject added successfully!')
    } catch (error) {
        console.error('Error adding subject:', error)
        if (error.response?.data?.message) {
            alert(`Error: ${error.response.data.message}`)
        } else {
            alert('Error adding subject. Please try again.')
        }
    } finally {
        loading.value = false
    }
}

const editSubject = (subject) => {
    // TODO: Implement edit subject functionality
    alert('Edit subject functionality will be implemented')
}

const removeSubject = async (subject) => {
    if (confirm(`Are you sure you want to remove "${subject.code} - ${subject.title}" from this curriculum?`)) {
        try {
            loading.value = true
            const curriculumId = getCurriculumId()
            await axios.delete(`/api/curricula/${curriculumId}/subjects/${subject.id}`)
            await fetchSubjects()
            alert('Subject removed from curriculum successfully!')
        } catch (error) {
            console.error('Error removing subject:', error)
            alert('Error removing subject. Please try again.')
        } finally {
            loading.value = false
        }
    }
}

const resetSubjectForm = () => {
    subjectForm.value = {
        code: '',
        title: '',
        description: '',
        units: 3,
        lecture_hours: 0,
        lab_hours: 0,
        type: '',
        status: 'Active',
        major_id: '',
        year_level: '',
        semester: '',
        order: 1,
        is_required: true,
        units_override: null
    }
}

const goBack = () => {
    window.location.href = '/admin/curricula'
}

// Lifecycle
onMounted(async () => {
    await fetchCurriculum()
    await fetchSubjects()
    await fetchMajors()
})
</script>

<style scoped>
.subject-item {
    transition: all 0.2s ease;
}

.subject-item:hover {
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

.subject-list {
    min-height: 200px;
}
</style>
