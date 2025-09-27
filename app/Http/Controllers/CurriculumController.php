<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // If it's an API request, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $curricula = Curriculum::with('program')->get();
            return response()->json($curricula);
        }
        
        // Otherwise return the view for web routes
        $curriculums = Curriculum::all();
        return view('admin.curricula.index', compact('curriculums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.curricula.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'effective_year' => 'required|integer|min:2000|max:2030',
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        $curriculum = Curriculum::create($validated);
        $curriculum->load('program');

        return response()->json([
            'message' => 'Curriculum created successfully',
            'curriculum' => $curriculum
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Curriculum $curriculum)
    {
        // If it's an API request, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $curriculum->load(['program.department']);
            return response()->json($curriculum);
        }
        
        // Otherwise return the view for web routes
        return view('admin.curricula.show', compact('curriculum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curriculum $curriculum)
    {
        return view('admin.curricula.edit', compact('curriculum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curriculum $curriculum): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'program_id' => 'sometimes|exists:programs,id',
            'effective_year' => 'required|integer|min:2000|max:2030',
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        $curriculum->update($validated);
        $curriculum->load('program');

        return response()->json([
            'message' => 'Curriculum updated successfully',
            'curriculum' => $curriculum
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum): JsonResponse
    {
        $curriculum->delete();

        return response()->json([
            'message' => 'Curriculum deleted successfully'
        ]);
    }

    /**
     * Archive a curriculum (PATCH request)
     */
    public function archive(Request $request, Curriculum $curriculum): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Archived'])]
        ]);

        $curriculum->update($validated);
        $curriculum->load('program');

        return response()->json([
            'message' => 'Curriculum status updated successfully',
            'curriculum' => $curriculum
        ]);
    }

    /**
     * Get subjects for a specific curriculum
     */
    public function getSubjects(Request $request, Curriculum $curriculum)
    {
        $subjects = $curriculum->subjects()
            ->with(['department'])
            ->withPivot(['major_id'])
            ->get()
            ->map(function ($subject) {
                // Load major information if major_id exists in pivot
                if ($subject->pivot->major_id) {
                    $subject->major = \App\Models\Major::find($subject->pivot->major_id);
                }
                return $subject;
            });

        return response()->json($subjects);
    }

    /**
     * Attach a subject to a curriculum
     */
    public function attachSubject(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'major_id' => 'nullable|exists:majors,id',
            'year_level' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:3',
            'order' => 'nullable|integer|min:1',
            'is_required' => 'boolean',
            'units_override' => 'nullable|integer|min:1|max:6'
        ]);

        // Check if subject is already attached to this curriculum with the same major
        $existingAttachment = $curriculum->subjects()
            ->where('subject_id', $validated['subject_id'])
            ->wherePivot('major_id', $validated['major_id'])
            ->exists();
            
        if ($existingAttachment) {
            $majorText = $validated['major_id'] ? 'for this major' : 'as a general subject';
            return response()->json([
                'message' => "Subject is already attached to this curriculum {$majorText}.",
                'error' => 'Duplicate attachment'
            ], 422);
        }

        // Set default values
        $validated['is_required'] = $validated['is_required'] ?? true;
        
        // If no order is specified, set it to the next available order for the year/semester
        if (!isset($validated['order'])) {
            $maxOrder = $curriculum->subjects()
                ->where('year_level', $validated['year_level'])
                ->where('semester', $validated['semester'])
                ->max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;
        }

        $curriculum->subjects()->attach($validated['subject_id'], [
            'major_id' => $validated['major_id'],
            'year_level' => $validated['year_level'],
            'semester' => $validated['semester'],
            'order' => $validated['order'],
            'is_required' => $validated['is_required'],
            'units_override' => $validated['units_override']
        ]);

        // Return the attached subject with pivot data
        $subject = $curriculum->subjects()
            ->with('department')
            ->where('subject_id', $validated['subject_id'])
            ->first();

        return response()->json([
            'message' => 'Subject attached to curriculum successfully',
            'subject' => $subject
        ], 201);
    }

    /**
     * Update curriculum-subject relationship
     */
    public function updateSubject(Request $request, Curriculum $curriculum, $subjectId)
    {
        $validated = $request->validate([
            'year_level' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:3',
            'order' => 'nullable|integer|min:1',
            'is_required' => 'boolean',
            'units_override' => 'nullable|integer|min:1|max:6'
        ]);

        // Check if subject is attached to this curriculum
        if (!$curriculum->subjects()->where('subject_id', $subjectId)->exists()) {
            return response()->json([
                'message' => 'Subject is not attached to this curriculum.',
                'error' => 'Subject not found'
            ], 404);
        }

        $curriculum->subjects()->updateExistingPivot($subjectId, $validated);

        // Return the updated subject with pivot data
        $subject = $curriculum->subjects()
            ->with('department')
            ->where('subject_id', $subjectId)
            ->first();

        return response()->json([
            'message' => 'Curriculum-subject relationship updated successfully',
            'subject' => $subject
        ]);
    }

    /**
     * Detach a subject from a curriculum
     */
    public function detachSubject(Request $request, Curriculum $curriculum, $subjectId)
    {
        // Check if subject is attached to this curriculum
        if (!$curriculum->subjects()->where('subject_id', $subjectId)->exists()) {
            return response()->json([
                'message' => 'Subject is not attached to this curriculum.',
                'error' => 'Subject not found'
            ], 404);
        }

        $curriculum->subjects()->detach($subjectId);

        return response()->json([
            'message' => 'Subject detached from curriculum successfully'
        ]);
    }
}
