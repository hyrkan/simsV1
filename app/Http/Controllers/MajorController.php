<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Program $program): JsonResponse
    {
        // Use the program from route parameter
        $programId = $program->id;
        
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('majors')->where(function ($query) use ($programId) {
                    return $query->where('program_id', $programId);
                })
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => ['required', Rule::in(['active', 'inactive', 'draft'])]
        ]);

        // Add program_id to validated data
        $validated['program_id'] = $programId;

        $major = Major::create($validated);
        $major->load('program');

        return response()->json([
            'success' => true,
            'message' => 'Major created successfully',
            'data' => $major
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Major $major)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Major $major)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major): JsonResponse
    {
        try {
            $major->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Major deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete major'
            ], 500);
        }
    }
}
