<template>
    <div class="row">
  <div class="col-xl-12">
      <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Staff Management</h5>
              <a href="/admin/staff/create" class="btn btn-primary">
                  <i class="feather icon-plus"></i> Add New Staff
              </a>
          </div>
          <div class="card-body">
              <!-- Search and Filter Section -->
              <form method="GET" @submit.prevent="submitFilters">
                  <div class="row mb-4">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="searchInput">Search Staff</label>
                              <input 
                                  type="text" 
                                  class="form-control" 
                                  id="searchInput" 
                                  name="search"
                                  v-model="searchQuery"
                                  placeholder="Search by name or email..."
                              >
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label for="roleFilter">Filter by Role</label>
                              <select class="form-control" id="roleFilter" name="role" v-model="roleFilter" :disabled="loadingRoles">
                                  <option value="">All Roles</option>
                                  <option v-if="loadingRoles" disabled>Loading roles...</option>
                                  <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label>&nbsp;</label>
                              <div>
                                  <button type="submit" class="btn btn-primary me-2">
                                      <i class="feather icon-search"></i> Search
                                  </button>
                                  <button type="button" class="btn btn-secondary" @click="clearFilters">
                                      <i class="feather icon-x"></i> Clear
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </form>

              <!-- Staff Table -->
              <div class="table-responsive">
                  <table class="table table-striped table-hover">
                      <thead class="thead-light">
                          <tr>
                              <th>#</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Role</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="(staff, index) in filteredStaffs" :key="staff.id">
                              <td>{{ index + 1 }}</td>
                              <td>
                                  <div class="d-flex align-items-center">
                                      <div class="avatar avatar-sm me-3">
                                          <div class="avatar-title bg-primary rounded-circle">
                                              {{ getInitials(staff.name) }}
                                          </div>
                                      </div>
                                      <div>
                                          <h6 class="mb-0">{{ staff.name }}</h6>
                                          <small class="text-muted">{{ staff.admin ? staff.admin.first_name + ' ' + staff.admin.last_name : staff.name }}</small>
                                      </div>
                                  </div>
                              </td>
                              <td>{{ staff.email }}</td>
                              <td>
                                  {{ getUserRole(staff) }}
                              </td>
                              <td>
                                  <div class="btn-group" role="group">
                                      <button type="button" class="btn btn-sm btn-outline-primary" @click="editStaff(staff.id)">
                                          <i class="feather icon-edit"></i>
                                      </button>
                                      <button type="button" class="btn btn-sm btn-outline-info" @click="viewStaff(staff.id)">
                                          <i class="feather icon-eye"></i>
                                      </button>
                                      <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteStaff(staff.id)">
                                          <i class="feather icon-trash-2"></i>
                                      </button>
                                  </div>
                              </td>
                          </tr>
                          <tr v-if="filteredStaffs.length === 0">
                              <td colspan="6" class="text-center py-4">
                                  <div class="text-muted">
                                      <i class="feather icon-users" style="font-size: 2rem;"></i>
                                      <p class="mt-2">No staff members found</p>
                                  </div>
                              </td>
                          </tr>
                      </tbody>
                  </table>
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-between align-items-center mt-4" v-if="pagination.last_page > 1">
                  <div>
                      <small class="text-muted">
                          Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} entries
                      </small>
                  </div>
                  <nav aria-label="Staff pagination">
                      <ul class="pagination pagination-sm mb-0">
                          <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                              <a class="page-link" :href="pagination.prev_page_url" @click.prevent="goToPage(pagination.current_page - 1)" tabindex="-1">Previous</a>
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
                              <a class="page-link" :href="pagination.next_page_url" @click.prevent="goToPage(pagination.current_page + 1)">Next</a>
                          </li>
                      </ul>
                  </nav>
              </div>
          </div>
      </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
import { translateRole } from '../../utils/roleTranslations';

export default {
    name: 'StaffTable',
    props: {
        staffs: {
            type: Array,
            default: () => []
        },
        pagination: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            searchQuery: '',
            roleFilter: '',
            roles: [],
            loadingRoles: false
        }
    },
    computed: {
        filteredStaffs() {
            // For server-side pagination, return the staffs as-is
            // Filtering will be handled by the backend
            return this.staffs;
        }
    },
    methods: {
        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        },
        getUserRole(staff) {
            if (staff.roles && staff.roles.length > 0) {
                return translateRole(staff.roles[0].name);
            }
            return translateRole('User');
        },

        editStaff(id) {
            window.location.href = `/admin/staff/${id}/edit`;
        },
        viewStaff(id) {
            window.location.href = `/admin/staff/${id}`;
        },
        deleteStaff(id) {
            if (confirm('Are you sure you want to delete this staff member?')) {
                // Handle delete logic here
                console.log('Delete staff:', id);
            }
        },
        goToPage(page) {
            if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
                // Build URL with page parameter
                const url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            }
        },
        getPageNumbers() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            // Show max 5 page numbers
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);
            
            // Adjust if we're near the beginning or end
            if (end - start < 4) {
                if (start === 1) {
                    end = Math.min(last, start + 4);
                } else if (end === last) {
                    start = Math.max(1, end - 4);
                }
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },
        submitFilters() {
            const url = new URL(window.location.href);
            url.searchParams.delete('page'); // Reset to first page when filtering
            
            if (this.searchQuery) {
                url.searchParams.set('search', this.searchQuery);
            } else {
                url.searchParams.delete('search');
            }
            
            if (this.roleFilter) {
                url.searchParams.set('role', this.roleFilter);
            } else {
                url.searchParams.delete('role');
            }
            
            window.location.href = url.toString();
        },
        clearFilters() {
            this.searchQuery = '';
            this.roleFilter = '';
            
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('role');
            url.searchParams.delete('page');
            
            window.location.href = url.toString();
        },
        initializeFiltersFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            this.searchQuery = urlParams.get('search') || '';
            this.roleFilter = urlParams.get('role') || '';
        },
        async fetchRoles() {
            this.loadingRoles = true;
            try {
                const response = await axios.get('/admin/api/roles');
                this.roles = response.data;
            } catch (error) {
                console.error('Error fetching roles:', error);
            } finally {
                this.loadingRoles = false;
            }
        }
    },
    mounted() {
        this.initializeFiltersFromUrl();
        this.fetchRoles();
    }
}
</script>

<style scoped>
.avatar {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>