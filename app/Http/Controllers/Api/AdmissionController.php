<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Major;
use App\Models\AcademicTerm;
use App\Models\Exam;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Get available exams for admission form
     */
    public function getExams(): JsonResponse
    {
        try {
            $exams = Exam::with('academicTerm')
                ->where('status', 'scheduled')
                ->orderBy('exam_date')
                ->orderBy('start_time')
                ->get()
                ->map(function ($exam) {
                    return [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'exam_date' => $exam->exam_date,
                        'start_time' => $exam->start_time,
                        'end_time' => $exam->end_time,
                        'location' => $exam->location,
                        'max_capacity' => $exam->max_capacity,
                        'academic_term' => $exam->academicTerm ? [
                            'id' => $exam->academicTerm->id,
                            'name' => $exam->academicTerm->school_year . ' - ' . $exam->academicTerm->semester
                        ] : null,
                        'formatted_schedule' => $exam->exam_date . ' at ' . $exam->start_time . ' - ' . $exam->end_time . ' (' . $exam->location . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $exams
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch exams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new admission application
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                // Program Selection
                'program_id' => 'required|exists:programs,id',
                'major_id' => 'nullable|exists:majors,id',
                'year_level' => 'required|string',
                'student_status' => 'required|in:new,old,transferee',
                'academic_term_id' => 'nullable|exists:academic_terms,id',
                'exam_id' => 'nullable|exists:exams,id',
                
                // Personal Information
                'surname' => 'required|string|max:255',
                'given_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'lrn' => 'required|string|max:255|unique:admissions,lrn',
                'email' => 'required|email|max:255|unique:admissions,email',
                
                // Place of Birth
                'birth_sitio' => 'nullable|string|max:255',
                'birth_barangay' => 'required|string|max:255',
                'birth_city' => 'required|string|max:255',
                'birth_province' => 'required|string|max:255',
                
                // Personal Details
                'date_of_birth' => 'required|date',
                'age' => 'required|integer|min:1|max:120',
                'sex' => 'required|in:Male,Female',
                'civil_status' => 'required|in:Single,Married,Widowed,Separated',
                'contact_number' => 'required|string|max:255',
                'religion' => 'nullable|string|max:255',
                
                // Home Address
                'home_sitio' => 'nullable|string|max:255',
                'home_barangay' => 'required|string|max:255',
                'home_city' => 'required|string|max:255',
                'home_province' => 'required|string|max:255',
                
                // Father's Information
                'father_surname' => 'nullable|string|max:255',
                'father_given_name' => 'nullable|string|max:255',
                'father_middle_name' => 'nullable|string|max:255',
                'father_occupation' => 'nullable|string|max:255',
                
                // Mother's Information
                'mother_surname' => 'nullable|string|max:255',
                'mother_given_name' => 'nullable|string|max:255',
                'mother_middle_name' => 'nullable|string|max:255',
                'mother_occupation' => 'nullable|string|max:255',
                
                // Spouse's Information
                'spouse_surname' => 'nullable|string|max:255',
                'spouse_given_name' => 'nullable|string|max:255',
                'spouse_middle_name' => 'nullable|string|max:255',
                'spouse_occupation' => 'nullable|string|max:255',
                
                // Guardian Information
                'guardian_surname' => 'nullable|string|max:255',
                'guardian_given_name' => 'nullable|string|max:255',
                'guardian_middle_name' => 'nullable|string|max:255',
                'guardian_occupation' => 'nullable|string|max:255',
                
                // Guardian Address
                'guardian_sitio' => 'nullable|string|max:255',
                'guardian_barangay' => 'nullable|string|max:255',
                'guardian_city' => 'nullable|string|max:255',
                'guardian_province' => 'nullable|string|max:255',
                
                // Contact Person Information
                'contact_given_name' => 'nullable|string|max:255',
                'contact_middle_initial' => 'nullable|string|max:1',
                'contact_surname' => 'nullable|string|max:255',
                'contact_person_number' => 'nullable|string|max:255',
                
                // Educational Background
                'elementary_school' => 'nullable|string|max:255',
                'elementary_year' => 'nullable|integer|min:1990|max:2030',
                'junior_high_school' => 'nullable|string|max:255',
                'junior_high_year' => 'nullable|integer|min:1990|max:2030',
                'senior_high_school' => 'nullable|string|max:255',
                'senior_high_year' => 'nullable|integer|min:1990|max:2030',
                'track_strand' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $validatedData = $validator->validated();

            // If academic_term_id is not provided, set it to the active academic term
            if (empty($validatedData['academic_term_id'])) {
                $activeAcademicTerm = AcademicTerm::active()->first();
                
                if (!$activeAcademicTerm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No active academic term found. Please contact the administrator.'
                    ], 400);
                }
                
                $validatedData['academic_term_id'] = $activeAcademicTerm->id;
            }

            // Create the admission record
            $admission = Admission::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Admission application submitted successfully!',
                'data' => [
                    'id' => $admission->id,
                    'control_number' => $admission->control_number,
                    'application_status' => $admission->application_status,
                    'full_name' => $admission->full_name,
                    'email' => $admission->email,
                    'lrn' => $admission->lrn,
                    'created_at' => $admission->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit admission application',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
