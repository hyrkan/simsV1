<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            $perPage = $request->get('per_page', 2);
            $academicTerms = AcademicTerm::where('status', 'active')
                                          ->orWhere('status', 'inactive')
                                          ->orderBy('created_at', 'desc')
                                          ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $academicTerms->items(),
                'pagination' => [
                    'current_page' => $academicTerms->currentPage(),
                    'last_page' => $academicTerms->lastPage(),
                    'per_page' => $academicTerms->perPage(),
                    'total' => $academicTerms->total(),
                    'from' => $academicTerms->firstItem(),
                    'to' => $academicTerms->lastItem(),
                    'prev_page_url' => $academicTerms->previousPageUrl(),
                    'next_page_url' => $academicTerms->nextPageUrl()
                ]
            ]);
        }
        
        // For web requests, return the view with initial data
        $academicTerms = AcademicTerm::where('status', 'active')
                                      ->orWhere('status', 'inactive')
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(2);
        return view('admin.academic_term.index', compact('academicTerms'));
    }

    public function archiveTerms(Request $request)
    {
        // Check if this is an API request
        if ($request->expectsJson() || $request->is('api/*')) {
            $perPage = $request->get('per_page', 10);
            $archivedTerms = AcademicTerm::where('status', 'archived')
                                          ->orderBy('created_at', 'desc')
                                          ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $archivedTerms->items(),
                'pagination' => [
                    'current_page' => $archivedTerms->currentPage(),
                    'last_page' => $archivedTerms->lastPage(),
                    'per_page' => $archivedTerms->perPage(),
                    'total' => $archivedTerms->total(),
                    'from' => $archivedTerms->firstItem(),
                    'to' => $archivedTerms->lastItem(),
                    'prev_page_url' => $archivedTerms->previousPageUrl(),
                    'next_page_url' => $archivedTerms->nextPageUrl()
                ]
            ]);
        }
        
        // For web requests, return the view with initial data
        $archivedTerms = AcademicTerm::where('status', 'archived')
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(10);
        return view('admin.academic_term.archive', compact('archivedTerms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:255',
            'semester' => 'required|in:1st Sem,2nd Sem,Summer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'in:active,inactive,archived'
        ]);

        // If creating an active term, deactivate all other terms
        if (isset($validated['status']) && $validated['status'] === 'active') {
            AcademicTerm::where('status', 'active')->update(['status' => 'inactive']);
        }

        $academicTerm = AcademicTerm::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Academic term created successfully!',
            'data' => $academicTerm
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicTerm $academicTerm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicTerm $academicTerm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicTerm $academicTerm)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:255',
            'semester' => 'required|in:1st Sem,2nd Sem,Summer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'in:active,inactive,archived'
        ]);

        // If updating to active status, deactivate all other terms
        if (isset($validated['status']) && $validated['status'] === 'active' && $academicTerm->status !== 'active') {
            AcademicTerm::where('status', 'active')->update(['status' => 'inactive']);
        }

        $academicTerm->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Academic term updated successfully!',
            'data' => $academicTerm
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicTerm $academicTerm)
    {
        // Prevent deletion of active terms
        if ($academicTerm->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an active academic term.'
            ], 422);
        }

        $academicTerm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic term deleted successfully!'
        ]);
    }
}
