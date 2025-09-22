<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.departments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * API method to get departments with pagination
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $departments = Department::orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($departments);
    }

    /**
     * API method to create a new department
     */
    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:departments,code',
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive'
        ]);

        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully!',
            'data' => $department
        ], 201);
    }

    /**
     * Get department options for dropdowns
     */
    public function getOptions(): JsonResponse
    {
        $departments = Department::select('id', 'name', 'code')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Archive a department
     */
    public function archive(Department $department): JsonResponse
    {
        $department->update(['status' => 'Archived']);

        return response()->json([
            'success' => true,
            'message' => 'Department archived successfully!',
            'data' => $department
        ]);
    }

    /**
     * Restore an archived department
     */
    public function restore(Department $department): JsonResponse
    {
        $department->update(['status' => 'Active']);

        return response()->json([
            'success' => true,
            'message' => 'Department restored successfully!',
            'data' => $department
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('departments')->ignore($department->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:Active,Inactive'
        ]);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully!',
            'data' => $department
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}
