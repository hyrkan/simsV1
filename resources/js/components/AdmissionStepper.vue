<template>
  <div class="stepper-container p-4">
    <!-- Stepper Navigation -->
    <div class="stepper-nav mb-4">
      <div class="row">
        <div 
          v-for="(step, index) in steps" 
          :key="index"
          class="col-md-2 col-6 mb-2"
        >
          <div 
            class="step-item"
            :class="{
              'active': currentStep === index,
              'completed': index < currentStep,
              'disabled': index > currentStep
            }"
            @click="goToStep(index)"
          >
            <div class="step-circle">
              <i v-if="index < currentStep" class="fas fa-check"></i>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <div class="step-label">{{ step.title }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress mb-4">
      <div 
        class="progress-bar bg-primary" 
        :style="{ width: progressPercentage + '%' }"
      ></div>
    </div>

    <!-- Form Steps -->
    <form @submit.prevent="handleSubmit">
      <!-- Step 1: Program Selection -->
      <div v-show="currentStep === 0" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-graduation-cap me-2"></i>Program Selection
        </h3>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Student Status <span class="text-danger">*</span></label>
            <select v-model="formData.student_status" class="form-select" required>
              <option value="">Select Status</option>
              <option value="new">New Student</option>
              <option value="old">Old Student</option>
              <option value="transferee">Transferee</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Program Name <span class="text-danger">*</span></label>
            <select v-model="formData.program_id" class="form-select" required :disabled="isLoading">
              <option value="">{{ isLoading ? 'Loading programs...' : 'Select Program' }}</option>
              <option v-for="program in programs" :key="program.id" :value="program.id">
                {{ program.name }}
              </option>
            </select>
          </div>
          <div class="col-md-2 mb-3">
            <label class="form-label">Year Level <span class="text-danger">*</span></label>
            <select v-model="formData.year_level" class="form-select" required>
              <option value="">Select Year</option>
              <option v-for="yearLevel in availableYearLevels" :key="yearLevel.value" :value="yearLevel.value">
                {{ yearLevel.label }}
              </option>
            </select>
          </div>
        </div>
        
        <div class="row">
          <div v-if="programHasMajors" class="col-md-4 mb-3">
            <label class="form-label">Major <span class="text-danger">*</span></label>
            <select v-model="formData.major_id" class="form-select" :disabled="isLoading" required>
              <option v-for="major in majors" :key="major.id" :value="major.id">
                {{ major.name }}
              </option>
            </select>
          </div>
          <div v-if="formData.student_status === 'new' || formData.student_status === 'transferee'" class="col-md-4 mb-3">
            <label class="form-label">Exam Schedule <span class="text-danger">*</span></label>
            <input v-model="formData.exam_schedule" type="datetime-local" class="form-control" required>
            <small class="form-text text-muted">Select your preferred exam date and time</small>
          </div>
        </div>
      </div>

      <!-- Step 2: Personal Information -->
      <div v-show="currentStep === 1" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-user me-2"></i>Student's Name
        </h3>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Surname <span class="text-danger">*</span></label>
            <input v-model="formData.surname" type="text" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Given Name <span class="text-danger">*</span></label>
            <input v-model="formData.given_name" type="text" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Middle Name</label>
            <input v-model="formData.middle_name" type="text" class="form-control">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">LRN (Learner Reference Number) <span class="text-danger">*</span></label>
            <input v-model="formData.lrn" type="text" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Gmail Account <span class="text-danger">*</span></label>
            <input v-model="formData.email" type="email" class="form-control" required>
          </div>
        </div>

        <h4 class="mt-4 mb-3">
          <i class="fas fa-map-marker-alt me-2"></i>Place of Birth
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Sitio/Purok</label>
            <input v-model="formData.birth_sitio" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Barangay <span class="text-danger">*</span></label>
            <input v-model="formData.birth_barangay" type="text" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">City/Municipality <span class="text-danger">*</span></label>
            <input v-model="formData.birth_city" type="text" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Province <span class="text-danger">*</span></label>
            <input v-model="formData.birth_province" type="text" class="form-control" required>
          </div>
        </div>
      </div>

      <!-- Step 3: Personal Details -->
      <div v-show="currentStep === 2" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-id-card me-2"></i>Personal Information
        </h3>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
            <input v-model="formData.date_of_birth" type="date" class="form-control" required>
          </div>
          <div class="col-md-2 mb-3">
            <label class="form-label">Age <span class="text-danger">*</span></label>
            <input v-model="formData.age" type="number" class="form-control" required>
          </div>
          <div class="col-md-2 mb-3">
            <label class="form-label">Sex <span class="text-danger">*</span></label>
            <select v-model="formData.sex" class="form-select" required>
              <option value="">Select</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
            <select v-model="formData.civil_status" class="form-select" required>
              <option value="">Select</option>
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>
          <div class="col-md-2 mb-3">
            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
            <input v-model="formData.contact_number" type="tel" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Religion</label>
            <input v-model="formData.religion" type="text" class="form-control">
          </div>
        </div>

        <h4 class="mt-4 mb-3">
          <i class="fas fa-home me-2"></i>Home Address
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Sitio/Purok</label>
            <input v-model="formData.home_sitio" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Barangay <span class="text-danger">*</span></label>
            <input v-model="formData.home_barangay" type="text" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">City/Municipality <span class="text-danger">*</span></label>
            <input v-model="formData.home_city" type="text" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Province <span class="text-danger">*</span></label>
            <input v-model="formData.home_province" type="text" class="form-control" required>
          </div>
        </div>
      </div>

      <!-- Step 4: Family Information -->
      <div v-show="currentStep === 3" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-users me-2"></i>Family Information
        </h3>
        
        <!-- Father's Information -->
        <h4 class="mb-3">
          <i class="fas fa-male me-2"></i>Father's Information
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Surname</label>
            <input v-model="formData.father_surname" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Given Name</label>
            <input v-model="formData.father_given_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Middle Name</label>
            <input v-model="formData.father_middle_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Occupation</label>
            <input v-model="formData.father_occupation" type="text" class="form-control">
          </div>
        </div>

        <!-- Mother's Information -->
        <h4 class="mt-4 mb-3">
          <i class="fas fa-female me-2"></i>Mother's Information
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Surname</label>
            <input v-model="formData.mother_surname" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Given Name</label>
            <input v-model="formData.mother_given_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Middle Name</label>
            <input v-model="formData.mother_middle_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Occupation</label>
            <input v-model="formData.mother_occupation" type="text" class="form-control">
          </div>
        </div>

        <!-- Spouse's Information (if married) -->
        <div v-if="formData.civil_status === 'Married'">
          <h4 class="mt-4 mb-3">
            <i class="fas fa-heart me-2"></i>Spouse's Information
          </h4>
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Surname</label>
              <input v-model="formData.spouse_surname" type="text" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Given Name</label>
              <input v-model="formData.spouse_given_name" type="text" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Middle Name</label>
              <input v-model="formData.spouse_middle_name" type="text" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Occupation</label>
              <input v-model="formData.spouse_occupation" type="text" class="form-control">
            </div>
          </div>
        </div>
      </div>

      <!-- Step 5: Guardian Information -->
      <div v-show="currentStep === 4" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-user-shield me-2"></i>Guardian Information
        </h3>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Surname</label>
            <input v-model="formData.guardian_surname" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Given Name</label>
            <input v-model="formData.guardian_given_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Middle Name</label>
            <input v-model="formData.guardian_middle_name" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Occupation</label>
            <input v-model="formData.guardian_occupation" type="text" class="form-control">
          </div>
        </div>

        <h4 class="mt-4 mb-3">
          <i class="fas fa-map-marker-alt me-2"></i>Address of Parent/Guardian/Spouse
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Sitio/Purok</label>
            <input v-model="formData.guardian_sitio" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Barangay</label>
            <input v-model="formData.guardian_barangay" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">City/Municipality</label>
            <input v-model="formData.guardian_city" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Province</label>
            <input v-model="formData.guardian_province" type="text" class="form-control">
          </div>
        </div>
      </div>

      <!-- Step 6: Educational Background -->
      <div v-show="currentStep === 5" class="step-content">
        <h3 class="step-title">
          <i class="fas fa-school me-2"></i>Educational Background
        </h3>
        
        <!-- Contact Person -->
        <h4 class="mb-3">
          <i class="fas fa-phone me-2"></i>Contact Person for Records
        </h4>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label">Given Name</label>
            <input v-model="formData.contact_given_name" type="text" class="form-control">
          </div>
          <div class="col-md-2 mb-3">
            <label class="form-label">Middle Initial</label>
            <input v-model="formData.contact_middle_initial" type="text" class="form-control" maxlength="1">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Surname</label>
            <input v-model="formData.contact_surname" type="text" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Contact Number</label>
            <input v-model="formData.contact_person_number" type="tel" class="form-control">
          </div>
        </div>

        <!-- Elementary -->
        <h4 class="mt-4 mb-3">
          <i class="fas fa-child me-2"></i>Elementary Education
        </h4>
        <div class="row">
          <div class="col-md-8 mb-3">
            <label class="form-label">Name of School (Elementary)</label>
            <input v-model="formData.elementary_school" type="text" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Year Graduated</label>
            <input v-model="formData.elementary_year" type="number" class="form-control" min="1990" max="2030">
          </div>
        </div>

        <!-- Junior High School -->
        <h4 class="mt-4 mb-3">
          <i class="fas fa-user-graduate me-2"></i>Junior High School
        </h4>
        <div class="row">
          <div class="col-md-8 mb-3">
            <label class="form-label">Name of School (Junior High School/High School)</label>
            <input v-model="formData.junior_high_school" type="text" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Year Graduated</label>
            <input v-model="formData.junior_high_year" type="number" class="form-control" min="1990" max="2030">
          </div>
        </div>

        <!-- Senior High School -->
        <h4 class="mt-4 mb-3">
          <i class="fas fa-graduation-cap me-2"></i>Senior High School
        </h4>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Name of School (Senior High)</label>
            <input v-model="formData.senior_high_school" type="text" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Year Graduated</label>
            <input v-model="formData.senior_high_year" type="number" class="form-control" min="1990" max="2030">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Track and Strand</label>
            <input v-model="formData.track_strand" type="text" class="form-control">
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="d-flex justify-content-between mt-4">
        <button 
          type="button" 
          class="btn btn-outline-secondary"
          @click="previousStep"
          :disabled="currentStep === 0"
        >
          <i class="fas fa-arrow-left me-2"></i>Previous
        </button>
        
        <button 
          v-if="currentStep < steps.length - 1"
          type="button" 
          class="btn btn-primary"
          @click="nextStep"
        >
          Next<i class="fas fa-arrow-right ms-2"></i>
        </button>
        
        <button 
          v-else
          type="submit" 
          class="btn btn-success"
          :disabled="isSubmitting"
        >
          <i class="fas fa-paper-plane me-2"></i>
          {{ isSubmitting ? 'Submitting...' : 'Submit Application' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'AdmissionStepper',
  data() {
    return {
      currentStep: 0,
      isSubmitting: false,
      isLoading: false,
      programs: [],
      majors: [],
      academicTerms: [],
      steps: [
        { title: 'Program', icon: 'graduation-cap' },
        { title: 'Personal Info', icon: 'user' },
        { title: 'Details', icon: 'id-card' },
        { title: 'Family', icon: 'users' },
        { title: 'Guardian', icon: 'user-shield' },
        { title: 'Education', icon: 'school' }
      ],
      formData: {
        // Program Selection
        program_id: '',
        major_id: '',
        year_level: '',
        student_status: '',
        academic_term_id: '',
        exam_schedule: '',
        
        // Personal Information
        surname: '',
        given_name: '',
        middle_name: '',
        lrn: '',
        email: '',
        
        // Place of Birth
        birth_sitio: '',
        birth_barangay: '',
        birth_city: '',
        birth_province: '',
        
        // Personal Details
        date_of_birth: '',
        age: '',
        sex: '',
        civil_status: '',
        contact_number: '',
        religion: '',
        
        // Home Address
        home_sitio: '',
        home_barangay: '',
        home_city: '',
        home_province: '',
        
        // Father's Information
        father_surname: '',
        father_given_name: '',
        father_middle_name: '',
        father_occupation: '',
        
        // Mother's Information
        mother_surname: '',
        mother_given_name: '',
        mother_middle_name: '',
        mother_occupation: '',
        
        // Spouse's Information
        spouse_surname: '',
        spouse_given_name: '',
        spouse_middle_name: '',
        spouse_occupation: '',
        
        // Guardian Information
        guardian_surname: '',
        guardian_given_name: '',
        guardian_middle_name: '',
        guardian_occupation: '',
        
        // Guardian Address
        guardian_sitio: '',
        guardian_barangay: '',
        guardian_city: '',
        guardian_province: '',
        
        // Contact Person Information
        contact_given_name: '',
        contact_middle_initial: '',
        contact_surname: '',
        contact_person_number: '',

        // Basic Education Background
        elementary_school: '',
        elementary_year: '',
        junior_high_school: '',
        junior_high_year: '',
        senior_high_school: '',
        senior_high_year: '',
        track_strand: ''
      }
    }
  },
  computed: {
    progressPercentage() {
      return ((this.currentStep + 1) / this.steps.length) * 100;
    },
    selectedProgram() {
      return this.programs.find(program => program.id == this.formData.program_id);
    },
    programHasMajors() {
      return this.selectedProgram && this.majors.length > 0;
    },
    availableYearLevels() {
      // For new students, only show 1st Year
      if (this.formData.student_status === 'new') {
        return [{ value: '1st Year', label: '1st Year' }];
      }
      // For other statuses, show all year levels
      return [
        { value: '1st Year', label: '1st Year' },
        { value: '2nd Year', label: '2nd Year' },
        { value: '3rd Year', label: '3rd Year' },
        { value: '4th Year', label: '4th Year' }
      ];
    }
  },
  methods: {
    nextStep() {
      if (this.validateCurrentStep()) {
        this.currentStep++;
      }
    },
    previousStep() {
      if (this.currentStep > 0) {
        this.currentStep--;
      }
    },
    goToStep(stepIndex) {
      if (stepIndex <= this.currentStep) {
        this.currentStep = stepIndex;
      }
    },
    validateCurrentStep() {
      // Basic validation for required fields
      const currentStepElement = document.querySelector('.step-content:not([style*="display: none"])');
      const requiredFields = currentStepElement.querySelectorAll('[required]');
      
      for (let field of requiredFields) {
        if (!field.value.trim()) {
          field.focus();
          alert('Please fill in all required fields before proceeding.');
          return false;
        }
      }
      return true;
    },
    async handleSubmit() {
      if (!this.validateCurrentStep()) {
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        // Add timestamp
        const submissionData = {
          ...this.formData,
          timestamp: new Date().toISOString(),
          status: 'pending'
        };
        
        // Here you would typically send the data to your backend
        console.log('Submission Data:', submissionData);
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        alert('Application submitted successfully! You will receive a confirmation email shortly.');
        
        // Reset form or redirect
        this.resetForm();
        
      } catch (error) {
        console.error('Submission error:', error);
        alert('There was an error submitting your application. Please try again.');
      } finally {
        this.isSubmitting = false;
      }
    },
    resetForm() {
      this.currentStep = 0;
      Object.keys(this.formData).forEach(key => {
        this.formData[key] = '';
      });
    },
    async fetchPrograms() {
      try {
        const response = await fetch('/api/admission/programs');
        const data = await response.json();
        console.log('Programs API Response:', data);
        if (data.success) {
          this.programs = data.data;
          console.log('Programs stored:', this.programs);
          // Log specific program with ID 4
          const program4 = this.programs.find(p => p.id === 4);
          console.log('Program ID 4:', program4);
        } else {
          console.error('Failed to fetch programs:', data.message);
        }
      } catch (error) {
        console.error('Error fetching programs:', error);
      }
    },
    async fetchMajors() {
      if (!this.formData.program_id) {
        this.majors = [];
        this.formData.major_id = '';
        return;
      }
      
      try {
        const response = await fetch(`/api/admission/majors?program_id=${this.formData.program_id}`);
        const data = await response.json();
        if (data.success) {
          this.majors = data.data;
          // Auto-select the first major if available
          if (this.majors.length > 0 && !this.formData.major_id) {
            this.formData.major_id = this.majors[0].id;
          }
        } else {
          console.error('Failed to fetch majors:', data.message);
          this.majors = [];
        }
      } catch (error) {
        console.error('Error fetching majors:', error);
        this.majors = [];
      }
    },
    async fetchAcademicTerms() {
      try {
        const response = await fetch('/api/admission/academic-terms');
        const data = await response.json();
        if (data.success) {
          this.academicTerms = data.data;
          // Automatically set the active term as default
          const activeTerm = this.academicTerms.find(term => term.status === 'active');
          if (activeTerm && !this.formData.academic_term_id) {
            this.formData.academic_term_id = activeTerm.id;
          }
        } else {
          console.error('Failed to fetch academic terms:', data.message);
        }
      } catch (error) {
        console.error('Error fetching academic terms:', error);
      }
    },
    onProgramChange() {
      this.formData.major_id = ''; // Reset major when program changes
      this.fetchMajors();
    }
  },
  async mounted() {
    this.isLoading = true;
    try {
      await Promise.all([
        this.fetchPrograms(),
        this.fetchAcademicTerms()
      ]);
    } catch (error) {
      console.error('Error loading initial data:', error);
    } finally {
      this.isLoading = false;
    }
  },
  watch: {
    'formData.program_id': function(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.onProgramChange();
      }
    },
    'formData.student_status': function(newVal, oldVal) {
      if (newVal !== oldVal) {
        // Reset year level when student status changes
        this.formData.year_level = '';
        // If new student is selected, automatically set to 1st Year
        if (newVal === 'new') {
          this.formData.year_level = '1st Year';
        }
      }
    }
  }
}
</script>

<style scoped>
.stepper-container {
  max-width: 100%;
}

.stepper-nav {
  margin-bottom: 2rem;
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.step-item.disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.step-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #e9ecef;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
}

.step-item.active .step-circle {
  background: #0d6efd;
  color: white;
  transform: scale(1.1);
}

.step-item.completed .step-circle {
  background: #198754;
  color: white;
}

.step-label {
  font-size: 0.875rem;
  text-align: center;
  font-weight: 500;
  color: #6c757d;
}

.step-item.active .step-label {
  color: #0d6efd;
  font-weight: 600;
}

.step-item.completed .step-label {
  color: #198754;
}

.step-content {
  background: #fefefe;
  padding: 2rem;
  border-radius: 15px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.step-title {
  color: #495057;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e9ecef;
  font-weight: 600;
}

.form-label {
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
}

.form-control, .form-select {
  border: 2px solid #e9ecef;
  border-radius: 8px;
  padding: 0.75rem;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.progress {
  height: 8px;
  border-radius: 4px;
  background: #e9ecef;
}

.progress-bar {
  border-radius: 4px;
  transition: width 0.3s ease;
}

@media (max-width: 768px) {
  .step-circle {
    width: 40px;
    height: 40px;
  }
  
  .step-label {
    font-size: 0.75rem;
  }
  
  .step-content {
    padding: 1rem;
  }
  
  .step-title {
    font-size: 1.25rem;
  }
}
</style>