<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.programs.index');
    }

    /**
     * Get programs data for API
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Program::with('department');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('department', function($dq) use ($search) {
                      $dq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Department filter
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pagination with configurable per_page
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;
        
        $programs = $query->paginate($perPage);
        $programs->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $programs->items(),
            'current_page' => $programs->currentPage(),
            'last_page' => $programs->lastPage(),
            'per_page' => $programs->perPage(),
            'total' => $programs->total(),
            'from' => $programs->firstItem(),
            'to' => $programs->lastItem(),
            'has_more_pages' => $programs->hasMorePages(),
            'prev_page_url' => $programs->previousPageUrl(),
            'next_page_url' => $programs->nextPageUrl()
        ]);
    }

    /**
     * Get departments for dropdown
     */
    public function getDepartments(): JsonResponse
    {
        $departments = Department::where('status', 'Active')
                                ->orderBy('name')
                                ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:programs,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'degree_level' => 'required|in:Undergraduate,Graduate,Diploma,Certificate',
            'duration_years' => 'required|integer|min:1|max:10',
            'status' => 'required|in:Active,Inactive'
        ]);

        $program = Program::create($validated);
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully!',
            'data' => $program
        ]);
    }

    public function showProgram(Program $program)
    {
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): JsonResponse
    {
        $program->load('department');
        
        return response()->json([
            'success' => true,
            'data' => $program
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('programs')->ignore($program->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'degree_level' => 'required|in:Undergraduate,Graduate,Diploma,Certificate',
            'duration_years' => 'required|integer|min:1|max:10',
            'status' => 'required|in:Active,Inactive'
        ]);

        $program->update($validated);
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully!',
            'data' => $program
        ]);
    }

    /**
     * Archive the specified resource.
     */
    public function archive(Program $program): JsonResponse
    {
        $program->update(['status' => 'Inactive']);
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program archived successfully!',
            'data' => $program
        ]);
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Program $program): JsonResponse
    {
        $program->update(['status' => 'Active']);
        $program->load('department');

        return response()->json([
            'success' => true,
            'message' => 'Program restored successfully!',
            'data' => $program
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program): JsonResponse
    {
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully!'
        ]);
    }

    /**
     * Get curricula for a specific program
     */
    public function getCurricula(Request $request, Program $program): JsonResponse
    {
        $curricula = $program->curricula()
            ->orderBy('effective_year', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $curricula
        ]);
    }
}
