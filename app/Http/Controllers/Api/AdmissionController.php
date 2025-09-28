<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Major;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdmissionController extends Controller
{
    /**
     * Get all programs for admission form
     */
    public function getPrograms(): JsonResponse
    {
        try {
            $programs = Program::where('status', 'Active')
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $programs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get majors by program ID
     */
    public function getMajorsByProgram(Request $request): JsonResponse
    {
        try {
            $programId = $request->query('program_id');
            
            if (!$programId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program ID is required'
                ], 400);
            }

            $majors = Major::where('program_id', $programId)
                ->select('id', 'name', 'code')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $majors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch majors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all academic terms for admission form
     */
    public function getAcademicTerms(): JsonResponse
    {
        try {
            $academicTerms = AcademicTerm::select('id', 'school_year', 'semester', 'status')
                ->where('status', 'active')
                ->orderBy('school_year', 'desc')
                ->orderBy('semester')
                ->get()
                ->map(function ($term) {
                    return [
                        'id' => $term->id,
                        'name' => $term->school_year . ' - ' . $term->semester,
                        'school_year' => $term->school_year,
                        'semester' => $term->semester
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $academicTerms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch academic terms',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
