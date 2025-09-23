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
            $curriculum->load('program');
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
}
