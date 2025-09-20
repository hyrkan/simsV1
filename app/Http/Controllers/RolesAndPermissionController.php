<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class RolesAndPermissionController extends Controller
{
    public function index()
    {
        return view('admin.roles_and_permission.index');
    }

    /**
     * Get all roles with permissions count
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::withCount('permissions')->get();
            return response()->json([
                'success' => true,
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all permissions with pagination and search
     */
    public function getPermissions(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            
            $query = Permission::query();
            
            if (!empty($search)) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            }
            
            $permissions = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $permissions->items(),
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'from' => $permissions->firstItem(),
                    'to' => $permissions->lastItem(),
                    'total' => $permissions->total(),
                    'per_page' => $permissions->perPage()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new role
     */
    public function createRole(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new permission
     */
    public function createPermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => $permission
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a role
     */
    public function deleteRole($id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            
            // Prevent deletion of super-admin role
            if ($role->name === 'super-admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete super-admin role'
                ], 403);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a permission
     */
    public function deletePermission($id): JsonResponse
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions($id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'permissions' => $role->permissions
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching role permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            
            $role->syncPermissions($permissions);

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
