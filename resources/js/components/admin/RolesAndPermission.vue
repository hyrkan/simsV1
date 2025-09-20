<template>
    <div class="container-fluid">
        <div class="row">
            <!-- Roles Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="feather icon-shield"></i> Roles Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Role Form -->
                        <form @submit.prevent="createRole" class="mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="newRole.name"
                                        placeholder="Enter role name"
                                        required
                                    >
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="feather icon-plus"></i> Add Role
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Roles List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Role Name</th>
                                        <th class="text-center">Permissions Count</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="role in roles" :key="role.id">
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ role.name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ role.permissions_count || 0 }} permissions</span>
                                        </td>
                                        <td class="text-center">
                                            <button 
                                                class="btn btn-sm btn-info me-2" 
                                                @click="editRolePermissions(role)"
                                            >
                                                <i class="feather icon-edit"></i> Permissions
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-danger" 
                                                @click="deleteRole(role.id)"
                                                v-if="role.name !== 'super-admin'"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="feather icon-key"></i> Permissions Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Permission Form -->
                        <form @submit.prevent="createPermission" class="mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="newPermission.name"
                                        placeholder="Enter permission name"
                                        required
                                    >
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="feather icon-plus"></i> Add Permission
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Search Permissions -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    v-model="permissionSearch"
                                    @input="searchPermissions"
                                    placeholder="Search permissions..."
                                >
                            </div>
                        </div>

                        <!-- Permissions List -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Permission Name</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="permission in permissions.data" :key="permission.id">
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ permission.name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button 
                                                @click="deletePermission(permission.id)" 
                                                class="btn btn-sm btn-danger"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3" v-if="permissions.total > 0">
                            <div>
                                <small class="text-muted">
                                    Showing {{ permissions.from }} to {{ permissions.to }} of {{ permissions.total }} results
                                </small>
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item" :class="{ disabled: permissions.current_page === 1 }">
                                        <button class="page-link" @click="changePage(permissions.current_page - 1)" :disabled="permissions.current_page === 1">
                                            Previous
                                        </button>
                                    </li>
                                    <li class="page-item" 
                                        v-for="page in visiblePages" 
                                        :key="page" 
                                        :class="{ active: page === permissions.current_page }"
                                    >
                                        <button class="page-link" @click="changePage(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: permissions.current_page === permissions.last_page }">
                                        <button class="page-link" @click="changePage(permissions.current_page + 1)" :disabled="permissions.current_page === permissions.last_page">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Permissions Modal -->
        <div class="modal fade" id="rolePermissionsModal" tabindex="-1" role="dialog" aria-labelledby="rolePermissionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rolePermissionsModalLabel">
                            Manage Permissions for: <span class="text-primary">{{ selectedRole?.name }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <h6>Available Permissions:</h6>
                                <div class="permission-grid">
                                    <div 
                                        v-for="permission in allPermissions" 
                                        :key="permission.id" 
                                        class="form-check mb-2"
                                    >
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            :id="'perm-' + permission.id"
                                            :value="permission.id"
                                            v-model="selectedPermissions"
                                        >
                                        <label class="form-check-label" :for="'perm-' + permission.id">
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" @click="updateRolePermissions">
                            <i class="feather icon-save"></i> Save Permissions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RolesAndPermission',
    data() {
        return {
            roles: [],
            permissions: {
                data: [],
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0
            },
            newRole: {
                name: ''
            },
            newPermission: {
                name: ''
            },
            selectedRole: null,
            rolePermissions: [],
            availablePermissions: [],
            allPermissions: [],
            selectedPermissions: [],
            permissionSearch: '',
            searchTimeout: null
        };
    },
    computed: {
        visiblePages() {
            const pages = [];
            const current = this.permissions.current_page;
            const last = this.permissions.last_page;
            
            // Show up to 5 pages around current page
            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
    },
    mounted() {
        this.fetchRoles();
        this.fetchPermissions();
        this.fetchAllPermissions();
    },
    methods: {
        async fetchRoles() {
            try {
                const response = await fetch('/admin/api/roles');
                const data = await response.json();
                this.roles = data.roles || [];
            } catch (error) {
                console.error('Error fetching roles:', error);
                this.showAlert('Error fetching roles', 'danger');
            }
        },
        async fetchPermissions(page = 1, search = '') {
            try {
                const params = new URLSearchParams({
                    page: page,
                    per_page: 10
                });
                
                if (search) {
                    params.append('search', search);
                }
                
                const response = await fetch(`/admin/api/permissions?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.permissions = data.data;
                }
            } catch (error) {
                console.error('Error fetching permissions:', error);
                this.showAlert('Error fetching permissions', 'danger');
            }
        },
        
        searchPermissions() {
            // Clear existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Set new timeout for debounced search
            this.searchTimeout = setTimeout(() => {
                this.fetchPermissions(1, this.permissionSearch);
            }, 300);
        },
        
        changePage(page) {
             if (page >= 1 && page <= this.permissions.last_page) {
                 this.fetchPermissions(page, this.permissionSearch);
             }
         },
         
         async fetchAllPermissions() {
             try {
                 const response = await fetch('/admin/api/permissions?per_page=1000');
                 const data = await response.json();
                 
                 if (data.success) {
                     this.allPermissions = data.data.data;
                 }
             } catch (error) {
                 console.error('Error fetching all permissions:', error);
             }
         },
        async createRole() {
            try {
                const response = await fetch('/admin/api/roles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newRole)
                });
                
                if (response.ok) {
                    this.newRole.name = '';
                    this.fetchRoles();
                    this.showAlert('Role created successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showAlert(error.message || 'Error creating role', 'danger');
                }
            } catch (error) {
                console.error('Error creating role:', error);
                this.showAlert('Error creating role', 'danger');
            }
        },
        async createPermission() {
            try {
                const response = await fetch('/admin/api/permissions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newPermission)
                });
                
                if (response.ok) {
                    this.newPermission.name = '';
                    this.fetchPermissions(this.permissions.current_page, this.permissionSearch);
                    this.fetchAllPermissions(); // Refresh all permissions for modal
                    this.showAlert('Permission created successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showAlert(error.message || 'Error creating permission', 'danger');
                }
            } catch (error) {
                console.error('Error creating permission:', error);
                this.showAlert('Error creating permission', 'danger');
            }
        },
        async deleteRole(roleId) {
            if (!confirm('Are you sure you want to delete this role?')) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/api/roles/${roleId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    this.fetchRoles();
                    this.showAlert('Role deleted successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showAlert(error.message || 'Error deleting role', 'danger');
                }
            } catch (error) {
                console.error('Error deleting role:', error);
                this.showAlert('Error deleting role', 'danger');
            }
        },
        async deletePermission(permissionId) {
            if (!confirm('Are you sure you want to delete this permission?')) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/api/permissions/${permissionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // If we're on the last page and it becomes empty, go to previous page
                    let currentPage = this.permissions.current_page;
                    if (this.permissions.data.length === 1 && currentPage > 1) {
                        currentPage = currentPage - 1;
                    }
                    this.fetchPermissions(currentPage, this.permissionSearch);
                    this.fetchAllPermissions(); // Refresh all permissions for modal
                    this.showAlert('Permission deleted successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showAlert(error.message || 'Error deleting permission', 'danger');
                }
            } catch (error) {
                console.error('Error deleting permission:', error);
                this.showAlert('Error deleting permission', 'danger');
            }
        },
        async editRolePermissions(role) {
            this.selectedRole = role;
            
            // Fetch current role permissions
            try {
                const response = await fetch(`/admin/api/roles/${role.id}/permissions`);
                const data = await response.json();
                this.selectedPermissions = data.permissions.map(p => p.id);
            } catch (error) {
                console.error('Error fetching role permissions:', error);
                this.selectedPermissions = [];
            }
            
            // Show modal with proper accessibility handling
            const modalElement = document.getElementById('rolePermissionsModal');
            const modal = new bootstrap.Modal(modalElement);
            
            // Handle modal events for accessibility
            modalElement.addEventListener('shown.bs.modal', function () {
                // Remove aria-hidden when modal is fully shown to prevent focus conflicts
                modalElement.removeAttribute('aria-hidden');
                modalElement.setAttribute('aria-modal', 'true');
                // Focus on the first checkbox in the modal for better UX
                const firstCheckbox = modalElement.querySelector('.form-check-input');
                if (firstCheckbox) {
                    firstCheckbox.focus();
                }
            });
            
            modalElement.addEventListener('hidden.bs.modal', function () {
                // Clean up accessibility attributes when modal is hidden
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
            });
            
            modalElement.addEventListener('hide.bs.modal', function () {
                // Prevent focus conflicts during modal hiding transition
                const focusedElement = modalElement.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
            });
            
            modal.show();
        },
        async updateRolePermissions() {
            try {
                const response = await fetch(`/admin/api/roles/${this.selectedRole.id}/permissions`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        permissions: this.selectedPermissions
                    })
                });
                
                if (response.ok) {
                    this.fetchRoles();
                    this.showAlert('Role permissions updated successfully', 'success');
                    
                    // Hide modal with proper accessibility handling
                    const modalElement = document.getElementById('rolePermissionsModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    const error = await response.json();
                    this.showAlert(error.message || 'Error updating permissions', 'danger');
                }
            } catch (error) {
                console.error('Error updating role permissions:', error);
                this.showAlert('Error updating permissions', 'danger');
            }
        },
        showAlert(message, type) {
            // Create and show alert (you can customize this based on your alert system)
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
};
</script>

<style scoped>
.permission-grid {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
}

.badge {
    font-size: 0.875em;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-check {
    padding-left: 1.5em;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}
</style>