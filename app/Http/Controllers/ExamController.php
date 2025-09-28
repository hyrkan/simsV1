<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.exams.index');
    }

    /**
     * Get exams data for API
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Exam::with('academicTerm');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhereHas('academicTerm', function($aq) use ($search) {
                      $aq->where('school_year', 'like', '%' . $search . '%')
                         ->orWhere('semester', 'like', '%' . $search . '%');
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Academic term filter
        if ($request->has('academic_term') && $request->academic_term) {
            $query->where('academic_term_id', $request->academic_term);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('exam_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('exam_date', '<=', $request->date_to);
        }

        // Pagination with configurable per_page
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 10;
        
        $exams = $query->orderBy('exam_date', 'desc')
                      ->orderBy('start_time', 'desc')
                      ->paginate($perPage);
        $exams->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $exams->items(),
            'pagination' => [
                'current_page' => $exams->currentPage(),
                'last_page' => $exams->lastPage(),
                'per_page' => $exams->perPage(),
                'total' => $exams->total(),
                'from' => $exams->firstItem(),
                'to' => $exams->lastItem(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicTerms = AcademicTerm::where('status', 'Active')->get();
        return view('admin.exams.create', compact('academicTerms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exam_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1|max:1000',
            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled'])]
        ]);

        $exam = Exam::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Exam created successfully!',
                'data' => $exam->load('academicTerm')
            ], 201);
        }

        return redirect()->route('exams.index')->with('success', 'Exam created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam): JsonResponse
    {
        $exam->load('academicTerm');
        
        return response()->json([
            'success' => true,
            'data' => $exam
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $academicTerms = AcademicTerm::where('status', 'Active')->get();
        return view('admin.exams.edit', compact('exam', 'academicTerms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'max_capacity' => 'required|integer|min:1|max:1000',
            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled'])]
        ]);

        $exam->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Exam updated successfully!',
                'data' => $exam->load('academicTerm')
            ]);
        }

        return redirect()->route('exams.index')->with('success', 'Exam updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // Check if exam has any admissions
        if ($exam->admissions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete exam with existing admissions. Please cancel the exam instead.'
            ], 422);
        }

        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully!'
        ]);
    }
}
