<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // If it's an API request, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $subjects = Subject::with('department')->get();
            return response()->json($subjects);
        }
        
        // Otherwise return the view for web routes
        $subjects = Subject::with('department')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:6',
            'lecture_hours' => 'nullable|integer|min:0|max:10',
            'lab_hours' => 'nullable|integer|min:0|max:10',
            'type' => ['required', Rule::in(['Core', 'Major', 'Minor', 'Elective', 'General Education'])],
            'department_id' => 'required|exists:departments,id',
            'status' => ['nullable', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'Active';

        // Check for duplicate subject code within the same department
        $existingSubject = Subject::where('code', $validated['code'])
            ->where('department_id', $validated['department_id'])
            ->first();

        if ($existingSubject) {
            return response()->json([
                'message' => 'A subject with this code already exists in the selected department.',
                'errors' => [
                    'code' => ['Subject code must be unique within the department.']
                ]
            ], 422);
        }

        $subject = Subject::create($validated);
        $subject->load('department');

        return response()->json([
            'message' => 'Subject created successfully',
            'subject' => $subject,
            'id' => $subject->id
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Subject $subject)
    {
        // If it's an API request, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $subject->load('department');
            return response()->json($subject);
        }
        
        // Otherwise return the view for web routes
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:6',
            'lecture_hours' => 'nullable|integer|min:0|max:10',
            'lab_hours' => 'nullable|integer|min:0|max:10',
            'type' => ['required', Rule::in(['Core', 'Major', 'Minor', 'Elective', 'General Education'])],
            'department_id' => 'required|exists:departments,id',
            'status' => ['nullable', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        // Check for duplicate subject code within the same department (excluding current subject)
        $existingSubject = Subject::where('code', $validated['code'])
            ->where('department_id', $validated['department_id'])
            ->where('id', '!=', $subject->id)
            ->first();

        if ($existingSubject) {
            return response()->json([
                'message' => 'A subject with this code already exists in the selected department.',
                'errors' => [
                    'code' => ['Subject code must be unique within the department.']
                ]
            ], 422);
        }

        $subject->update($validated);
        $subject->load('department');

        return response()->json([
            'message' => 'Subject updated successfully',
            'subject' => $subject
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        // Check if subject is attached to any curricula
        if ($subject->curricula()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete subject. It is currently attached to one or more curricula.',
                'error' => 'Subject is in use'
            ], 422);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }

    /**
     * Archive a subject (PATCH request)
     */
    public function archive(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        $subject->update($validated);
        $subject->load('department');

        return response()->json([
            'message' => 'Subject status updated successfully',
            'subject' => $subject
        ]);
    }
}
