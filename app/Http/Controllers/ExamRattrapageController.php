<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamRattrapage;
use App\Models\ExamRattrapageStudent;
use App\Models\Inscription;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamRattrapageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exam-edit');   // reuse existing permission
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LIST: all rattrapages for an exam
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Exam $exam)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        $rattrapages = ExamRattrapage::where('original_exam_id', $exam->id)
            ->with(['rattrapageExam', 'creator', 'students.user'])
            ->orderByDesc('created_at')
            ->get();

        return view('exams.rattrapages.index', compact('exam', 'rattrapages'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE FORM
    // ─────────────────────────────────────────────────────────────────────────

    public function create(Exam $exam)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        // Build a temporary ExamRattrapage to compute eligible students preview
        $tempRattrapage = new ExamRattrapage([
            'original_exam_id' => $exam->id,
            'include_absent'   => true,
            'include_failed'   => true,
            'score_threshold'  => $exam->passing_score,
        ]);
        $tempRattrapage->setRelation('originalExam', $exam);

        $eligibleStudents = $tempRattrapage->computeEligibleStudents();

        return view('exams.rattrapages.create', compact('exam', 'eligibleStudents'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX: preview eligible students when criteria change
    // ─────────────────────────────────────────────────────────────────────────

    public function previewEligible(Request $request, Exam $exam)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $tempRattrapage = new ExamRattrapage([
            'original_exam_id' => $exam->id,
            'include_absent'   => $request->boolean('include_absent', true),
            'include_failed'   => $request->boolean('include_failed', true),
            'score_threshold'  => $request->filled('score_threshold')
                                    ? (float) $request->score_threshold
                                    : $exam->passing_score,
        ]);
        $tempRattrapage->setRelation('originalExam', $exam);

        $eligible = $tempRattrapage->computeEligibleStudents();

        return response()->json([
            'count'    => $eligible->count(),
            'students' => $eligible->map(fn($e) => [
                'id'             => $e['user']->id,
                'name'           => $e['user']->name,
                'email'          => $e['user']->email,
                'reason'         => $e['reason'],
                'original_score' => $e['original_score'],
                'inscription_id' => $e['inscription']->id,
            ])->values(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request, Exam $exam)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'passing_score'    => 'required|integer|min:0|max:100',
            'max_attempts'     => 'required|integer|min:1|max:5',
            'available_from'   => 'nullable|date',
            'available_until'  => 'nullable|date|after:available_from',
            'include_absent'   => 'nullable|boolean',
            'include_failed'   => 'nullable|boolean',
            'score_threshold'  => 'nullable|numeric|min:0|max:100',
            'notes'            => 'nullable|string',
            // manual student override
            'manual_student_ids'   => 'nullable|array',
            'manual_student_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Create the rattrapage exam (clone settings from original)
            $rattrapageExam = Exam::create([
                'module_id'                => $exam->module_id,
                'title'                    => $request->title,
                'description'              => $request->description,
                'duration_minutes'         => $request->duration_minutes,
                'passing_score'            => $request->passing_score,
                'max_attempts'             => $request->max_attempts,
                'shuffle_questions'        => $exam->shuffle_questions,
                'show_results_immediately' => $exam->show_results_immediately,
                'show_correct_answers'     => $exam->show_correct_answers,
                'available_from'           => $request->available_from,
                'available_until'          => $request->available_until,
                'status'                   => 'draft', // start as draft, admin publishes after adding questions
                'created_by'               => $user->id,
            ]);

            // 2. Create the rattrapage record
            $includeAbsent  = $request->boolean('include_absent', true);
            $includeFailed  = $request->boolean('include_failed', true);
            $scoreThreshold = $request->filled('score_threshold')
                                ? (float) $request->score_threshold
                                : $exam->passing_score;

            $rattrapage = ExamRattrapage::create([
                'original_exam_id'  => $exam->id,
                'rattrapage_exam_id'=> $rattrapageExam->id,
                'created_by'        => $user->id,
                'include_absent'    => $includeAbsent,
                'include_failed'    => $includeFailed,
                'score_threshold'   => $scoreThreshold,
                'notes'             => $request->notes,
            ]);

            // 3. Compute & store eligible students
            $rattrapage->setRelation('originalExam', $exam);
            $eligibleStudents = $rattrapage->computeEligibleStudents();

            $manualIds = $request->input('manual_student_ids', []);

            foreach ($eligibleStudents as $eligibleData) {
                ExamRattrapageStudent::create([
                    'rattrapage_id'      => $rattrapage->id,
                    'user_id'            => $eligibleData['user']->id,
                    'inscription_id'     => $eligibleData['inscription']->id,
                    'eligibility_reason' => $eligibleData['reason'],
                    'original_score'     => $eligibleData['original_score'],
                ]);

                // Remove from manual list if already auto-added
                $manualIds = array_diff($manualIds, [$eligibleData['user']->id]);
            }

            // 4. Add manually selected students not already in the list
            foreach ($manualIds as $userId) {
                $inscription = Inscription::where('user_id', $userId)
                    ->where('status', 'active')
                    ->whereHas('formation.modules', function ($q) use ($exam) {
                        $q->where('modules.id', $exam->module_id);
                    })
                    ->first();

                if ($inscription) {
                    ExamRattrapageStudent::firstOrCreate(
                        ['rattrapage_id' => $rattrapage->id, 'user_id' => $userId],
                        [
                            'inscription_id'     => $inscription->id,
                            'eligibility_reason' => 'manual',
                            'original_score'     => null,
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('exams.rattrapages.show', [$exam, $rattrapage])
                ->with('success', 'Rattrapage créé! Ajoutez maintenant les questions puis publiez-le.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating rattrapage: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du rattrapage: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW details of one rattrapage
    // ─────────────────────────────────────────────────────────────────────────

    public function show(Exam $exam, ExamRattrapage $rattrapage)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        $rattrapage->load([
            'rattrapageExam.questions',
            'creator',
            'students.user',
            'students.inscription',
        ]);

        // For each student, fetch their attempt on the rattrapage exam
        $rattrapageExam = $rattrapage->rattrapageExam;
        $students = $rattrapage->students->map(function ($student) use ($rattrapageExam) {
            $attempt = ExamAttempt::where('exam_id', $rattrapageExam->id)
                ->where('user_id', $student->user_id)
                ->latest()
                ->first();
            $student->attempt = $attempt;
            return $student;
        });

        return view('exams.rattrapages.show', compact('exam', 'rattrapage', 'students', 'rattrapageExam'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADD STUDENT manually after creation
    // ─────────────────────────────────────────────────────────────────────────

    public function addStudent(Request $request, Exam $exam, ExamRattrapage $rattrapage)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $inscription = Inscription::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->whereHas('formation.modules', function ($q) use ($exam) {
                $q->where('modules.id', $exam->module_id);
            })
            ->first();

        if (!$inscription) {
            return response()->json([
                'success' => false,
                'message' => 'Cet étudiant n\'est pas inscrit à la formation liée à cet examen.'
            ], 422);
        }

        $existing = ExamRattrapageStudent::where('rattrapage_id', $rattrapage->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Cet étudiant est déjà dans la liste du rattrapage.'
            ], 422);
        }

        $student = ExamRattrapageStudent::create([
            'rattrapage_id'      => $rattrapage->id,
            'user_id'            => $request->user_id,
            'inscription_id'     => $inscription->id,
            'eligibility_reason' => 'manual',
            'original_score'     => null,
        ]);

        $student->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Étudiant ajouté avec succès!',
            'student' => [
                'id'    => $student->user->id,
                'name'  => $student->user->name,
                'email' => $student->user->email,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REMOVE STUDENT from rattrapage
    // ─────────────────────────────────────────────────────────────────────────

    public function removeStudent(Exam $exam, ExamRattrapage $rattrapage, $userId)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        ExamRattrapageStudent::where('rattrapage_id', $rattrapage->id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Étudiant retiré.']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE rattrapage
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(Exam $exam, ExamRattrapage $rattrapage)
    {
        $user = Auth::user();
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Soft-delete the linked rattrapage exam too
            $rattrapage->rattrapageExam?->delete();
            $rattrapage->delete();

            DB::commit();

            return redirect()
                ->route('exams.rattrapages.index', $exam)
                ->with('success', 'Rattrapage supprimé.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}