<template>
  <div class="card">
    <div class="card-header">
      <h5>Create Staff Member</h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="submitForm">
        <div class="row">
          <!-- First Name -->
          <div class="col-md-6 mb-3">
            <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
            <input 
              type="text" 
              class="form-control" 
              id="firstName" 
              v-model="staff.first_name" 
              :class="{'is-invalid': errors.first_name}"
              placeholder="Enter first name"
            >
            <div v-if="errors.first_name" class="invalid-feedback">
              {{ errors.first_name }}
            </div>
          </div>
          
          <!-- Last Name -->
          <div class="col-md-6 mb-3">
            <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
            <input 
              type="text" 
              class="form-control" 
              id="lastName" 
              v-model="staff.last_name" 
              :class="{'is-invalid': errors.last_name}"
              placeholder="Enter last name"
            >
            <div v-if="errors.last_name" class="invalid-feedback">
              {{ errors.last_name }}
            </div>
          </div>
        </div>
        
        <!-- Middle Name (Optional) -->
        <div class="mb-3">
          <label for="middleName" class="form-label">Middle Name <span class="text-muted">(Optional)</span></label>
          <input 
            type="text" 
            class="form-control" 
            id="middleName" 
            v-model="staff.middle_name" 
            :class="{'is-invalid': errors.middle_name}"
            placeholder="Enter middle name"
          >
          <div v-if="errors.middle_name" class="invalid-feedback">
            {{ errors.middle_name }}
          </div>
        </div>
        
        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
          <input 
            type="email" 
            class="form-control" 
            id="email" 
            v-model="staff.email" 
            :class="{'is-invalid': errors.email}"
            placeholder="Enter email address"
          >
          <div v-if="errors.email" class="invalid-feedback">
            {{ errors.email }}
          </div>
        </div>
        
        <!-- Role -->
        <div class="mb-3">
          <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
          <select 
            class="form-select" 
            id="role" 
            v-model="staff.role" 
            :class="{'is-invalid': errors.role}"
            :disabled="loadingRoles"
          >
            <option value="">{{ loadingRoles ? 'Loading roles...' : 'Select a role' }}</option>
            <option v-for="role in roles" :key="role.id" :value="role.name">
              {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
            </option>
          </select>
          <div v-if="errors.role" class="invalid-feedback">
            {{ errors.role }}
          </div>
        </div>
        
        <div class="row">
          <!-- Password -->
          <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                class="form-control" 
                id="password" 
                v-model="staff.password" 
                :class="{'is-invalid': errors.password}"
                placeholder="Enter password"
              >
              <button 
                type="button" 
                class="btn btn-outline-secondary" 
                @click="togglePasswordVisibility"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <div v-if="errors.password" class="invalid-feedback d-block">
              {{ errors.password }}
            </div>
            <small class="form-text text-muted">
              Password must be at least 8 characters long
            </small>
          </div>
          
          <!-- Password Confirmation -->
          <div class="col-md-6 mb-3">
            <label for="passwordConfirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input 
                :type="showPasswordConfirm ? 'text' : 'password'" 
                class="form-control" 
                id="passwordConfirm" 
                v-model="staff.password_confirmation" 
                :class="{'is-invalid': errors.password_confirmation}"
                placeholder="Confirm password"
              >
              <button 
                type="button" 
                class="btn btn-outline-secondary" 
                @click="togglePasswordConfirmVisibility"
              >
                <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <div v-if="errors.password_confirmation" class="invalid-feedback d-block">
              {{ errors.password_confirmation }}
            </div>
          </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
            <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
            {{ isSubmitting ? 'Creating...' : 'Create Staff' }}
          </button>
          <button type="button" class="btn btn-secondary" @click="resetForm">
            Reset
          </button>
        </div>
      </form>
      
      <!-- Success/Error Messages -->
      <div v-if="message.text" class="alert mt-3" :class="message.type === 'success' ? 'alert-success' : 'alert-danger'">
        {{ message.text }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CreateStaff',
  data() {
    return {
      staff: {
        first_name: '',
        last_name: '',
        middle_name: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: ''
      },
      roles: [],
      loadingRoles: false,
      errors: {},
      isSubmitting: false,
      showPassword: false,
      showPasswordConfirm: false,
      message: {
        text: '',
        type: ''
      }
    }
  },
  mounted() {
    this.fetchRoles();
  },
  methods: {
    async fetchRoles() {
      this.loadingRoles = true;
      try {
        const response = await axios.get('/admin/api/roles');
        if (response.data.success) {
          this.roles = response.data.roles;
        } else {
          console.error('Failed to fetch roles:', response.data.message);
        }
      } catch (error) {
        console.error('Error fetching roles:', error);
      } finally {
        this.loadingRoles = false;
      }
    },
    validateForm() {
      this.errors = {};
      
      // First name validation
      if (!this.staff.first_name.trim()) {
        this.errors.first_name = 'First name is required';
      } else if (this.staff.first_name.length < 2) {
        this.errors.first_name = 'First name must be at least 2 characters';
      }
      
      // Last name validation
      if (!this.staff.last_name.trim()) {
        this.errors.last_name = 'Last name is required';
      } else if (this.staff.last_name.length < 2) {
        this.errors.last_name = 'Last name must be at least 2 characters';
      }
      
      // Email validation
      if (!this.staff.email.trim()) {
        this.errors.email = 'Email is required';
      } else if (!this.isValidEmail(this.staff.email)) {
        this.errors.email = 'Please enter a valid email address';
      }
      
      // Role validation
      if (!this.staff.role) {
        this.errors.role = 'Role is required';
      }
      
      // Password validation
      if (!this.staff.password) {
        this.errors.password = 'Password is required';
      } else if (this.staff.password.length < 8) {
        this.errors.password = 'Password must be at least 8 characters long';
      }
      
      // Password confirmation validation
      if (!this.staff.password_confirmation) {
        this.errors.password_confirmation = 'Password confirmation is required';
      } else if (this.staff.password !== this.staff.password_confirmation) {
        this.errors.password_confirmation = 'Passwords do not match';
      }
      
      return Object.keys(this.errors).length === 0;
    },
    
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    
    async submitForm() {
      // Clear previous messages
      this.message = { text: '', type: '' };
      
      // Validate form
      if (!this.validateForm()) {
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        // Make API call to create staff
        const response = await axios.post('/create-staff', this.staff);
        
        this.message = {
          text: 'Staff member created successfully!',
          type: 'success'
        };
        this.resetForm();
      } catch (error) {
        console.error('Error:', error);
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            this.message = {
              text: error.response.data.message || 'Failed to create staff member. Please try again.',
              type: 'error'
            };
          }
        } else {
          this.message = {
            text: 'An error occurred. Please try again.',
            type: 'error'
          };
        }
      } finally {
        this.isSubmitting = false;
      }
    },
    
    resetForm() {
      this.staff = {
        first_name: '',
        last_name: '',
        middle_name: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: ''
      };
      this.errors = {};
      this.showPassword = false;
      this.showPasswordConfirm = false;
    },
    
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    
    togglePasswordConfirmVisibility() {
      this.showPasswordConfirm = !this.showPasswordConfirm;
    }
  }
}
</script>

<style scoped>
.card {
  margin: 1rem 0;
}

.text-danger {
  color: #dc3545 !important;
}

.text-muted {
  color: #6c757d !important;
}

.input-group .btn {
  border-left: 0;
}

.form-control:focus {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: #dc3545;
}
</style>