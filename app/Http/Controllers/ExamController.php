<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\Module;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function __construct()
    {
        // Permissions
        $this->middleware('permission:exam-list', ['only' => ['index', 'show', 'examAttempts', 'attemptDetails']]);
        $this->middleware('permission:exam-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:exam-edit', ['only' => ['edit', 'update', 'updateStatus', 'addQuestion', 'updateQuestion', 'deleteQuestion', 'reorderQuestions', 'gradeAttempt']]);
        $this->middleware('permission:exam-delete', ['only' => ['destroy']]);

        // Student permissions
        $this->middleware('permission:exam-take', ['only' => ['startAttempt', 'takeExam', 'submitExam', 'saveAnswer']]);
        $this->middleware('permission:exam-view-results', ['only' => ['viewResult', 'myAttempts']]);
    }

    /**
     * Liste des examens (Admin/Consultant)
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Exam::with(['module', 'creator', 'questions'])
            ->whereDoesntHave('rattrapageOf');

        if ($user->hasRole('Consultant')) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exams   = $query->orderBy('created_at', 'desc')->paginate(15);
        $modules = $user->hasRole('Consultant')
            ? Module::where('user_id', $user->id)->get()
            : Module::all();

        return view('exams.index', compact('exams', 'modules'));
    }

    /**
     * Form de création d'exam
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $moduleQuery = fn($q) => $q->whereHas('category', function ($c) {
            $c->whereIn('name', [
                'Licence Professionnelle',
                'Master Professionnelle',
                'LICENCE PROFESSIONNELLE RECONNU',
            ]);
        })->orderBy('title');

        $base = Module::where('progress', '>=', 100)
            ->whereHas('formations.category', function ($q) {
                $q->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU',
                ]);
            })
            ->with(['formations' => $moduleQuery]);

        if ($user->hasRole('Consultant')) {
            $base->where('user_id', $user->id);
        }

        $modules = $base->get();

        if ($modules->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Aucun module complété (100%) disponible.');
        }

        $moduleId = $request->get('module_id', null);

        return view('exams.create', compact('modules', 'moduleId'));
    }

    /**
     * Store exam
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id'                => 'required|exists:modules,id',
            'title'                    => 'required|string|max:255',
            'description'              => 'nullable|string',
            'duration_minutes'         => 'required|integer|min:1|max:300',
            'passing_score'            => 'required|integer|min:0|max:100',
            'max_attempts'             => 'required|integer|min:1|max:10',
            'shuffle_questions'        => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers'     => 'nullable|boolean',
            'available_from'           => 'nullable|date',
            'available_until'          => 'nullable|date|after:available_from',
            'status'                   => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'module_id'                => $request->module_id,
                'title'                    => $request->title,
                'description'              => $request->description,
                'duration_minutes'         => $request->duration_minutes,
                'passing_score'            => $request->passing_score,
                'max_attempts'             => $request->max_attempts,
                'shuffle_questions'        => $request->boolean('shuffle_questions', false),
                'show_results_immediately' => $request->boolean('show_results_immediately', true),
                'show_correct_answers'     => $request->boolean('show_correct_answers', true),
                'available_from'           => $request->available_from,
                'available_until'          => $request->available_until,
                'status'                   => $request->status,
                'created_by'               => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('exams.edit', $exam)
                ->with('success', 'Examen créé avec succès! Ajoutez maintenant des questions.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating exam: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la création de l\'examen.')
                ->withInput();
        }
    }

    /**
     * Show exam details
     */
    public function show(Exam $exam)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $exam->load(['module', 'creator', 'questions' => fn($q) => $q->orderBy('order')]);
        $stats = $exam->getStatistics();

        return view('exams.show', compact('exam', 'stats'));
    }

    /**
     * Edit exam
     */
    public function edit(Exam $exam)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $exam->load(['questions' => fn($q) => $q->orderBy('order')]);

        $modules = $user->hasRole('Consultant')
            ? Module::where('user_id', $user->id)->get()
            : Module::all();

        return view('exams.edit', compact('exam', 'modules'));
    }

    /**
     * Update exam
     */
    public function update(Request $request, Exam $exam)
    {
        $validator = Validator::make($request->all(), [
            'module_id'                => 'required|exists:modules,id',
            'title'                    => 'required|string|max:255',
            'description'              => 'nullable|string',
            'duration_minutes'         => 'required|integer|min:1|max:300',
            'passing_score'            => 'required|integer|min:0|max:100',
            'max_attempts'             => 'required|integer|min:1|max:10',
            'shuffle_questions'        => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers'     => 'nullable|boolean',
            'available_from'           => 'nullable|date',
            'available_until'          => 'nullable|date|after:available_from',
            'status'                   => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $exam->update([
                'module_id'                => $request->module_id,
                'title'                    => $request->title,
                'description'              => $request->description,
                'duration_minutes'         => $request->duration_minutes,
                'passing_score'            => $request->passing_score,
                'max_attempts'             => $request->max_attempts,
                'shuffle_questions'        => $request->boolean('shuffle_questions', false),
                'show_results_immediately' => $request->boolean('show_results_immediately', true),
                'show_correct_answers'     => $request->boolean('show_correct_answers', true),
                'available_from'           => $request->available_from,
                'available_until'          => $request->available_until,
                'status'                   => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('exams.show', $exam)
                ->with('success', 'Examen mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating exam: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour.')
                ->withInput();
        }
    }

    /**
     * Delete exam
     */
    public function destroy(Exam $exam)
    {
        try {
            $exam->delete();

            return redirect()
                ->route('exams.index')
                ->with('success', 'Examen supprimé avec succès!');

        } catch (\Exception $e) {
            Log::error('Error deleting exam: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // SHARED HELPER: build correct_answer value for storage
    // ✅ FIX: qcm/true_false/text use json_encode so the JSON column stores
    //         them consistently as "\"b\"" instead of plain "b"
    // ─────────────────────────────────────────────────────────────────────
    private function buildCorrectAnswer(Request $request): ?string
    {
        switch ($request->type) {

            case 'qcm':
            case 'checkbox':
                // correct_answer is derived from options (is_correct flag), not stored separately
                return null;

            case 'true_false':
            case 'text':
                // ✅ json_encode so MySQL JSON column stores "\"b\"" not "b"
                return json_encode($request->correct_answer);

            case 'fill_blanks':
                $blanksData = json_decode($request->blanks, true);
                return json_encode(['blanks' => $blanksData]);

            case 'matching':
                $pairsData = json_decode($request->matching_pairs, true);
                return json_encode(['pairs' => $pairsData]);

            case 'ordering':
                $itemsData = json_decode($request->order_items, true);
                return json_encode(['items' => $itemsData]);

            case 'numeric':
                $numericData = json_decode($request->numeric_data, true);
                return json_encode($numericData);

            default:
                return null;
        }
    }

    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, Exam $exam)
    {
        $rules = [
            'type'           => 'required|in:qcm,true_false,text,essay,checkbox,fill_blanks,matching,ordering,numeric',
            'question_text'  => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'points'         => 'required|numeric|min:0.5|max:100',
            'explanation'    => 'nullable|string',
        ];

        if (in_array($request->type, ['qcm', 'checkbox'])) {
            $rules['options'] = 'required|json';
        }
        if (in_array($request->type, ['true_false', 'text'])) {
            $rules['correct_answer'] = 'required|string';
        }
        if ($request->type === 'fill_blanks')  { $rules['blanks']          = 'required|json'; }
        if ($request->type === 'matching')     { $rules['matching_pairs']  = 'required|json'; }
        if ($request->type === 'ordering')     { $rules['order_items']     = 'required|json'; }
        if ($request->type === 'numeric')      { $rules['numeric_data']    = 'required|json'; }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Validation échouée',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('exam_questions', 'public');
            }

            $lastOrder = $exam->questions()->max('order') ?? 0;

            // ── Options (QCM / Checkbox) ──────────────────────────────────
            $options = null;
            if (in_array($request->type, ['qcm', 'checkbox'])) {
                $optionsData = json_decode($request->options, true);
                if (!is_array($optionsData) || empty($optionsData)) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Options invalides'], 422);
                }
                $options = $optionsData;
            }

            // ── Extra validation before building correct_answer ───────────
            if ($request->type === 'fill_blanks') {
                $blanksData = json_decode($request->blanks, true);
                $blankCount = substr_count($request->question_text, '[___]');
                if ($blankCount !== count($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Le nombre de blancs [___] ({$blankCount}) ne correspond pas au nombre de réponses (" . count($blanksData) . ")",
                    ], 422);
                }
            }
            if ($request->type === 'matching') {
                $pairsData = json_decode($request->matching_pairs, true);
                if (count($pairsData) < 2) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Minimum 2 paires requises'], 422);
                }
            }
            if ($request->type === 'ordering') {
                $itemsData = json_decode($request->order_items, true);
                if (count($itemsData) < 2) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Minimum 2 éléments requis'], 422);
                }
            }
            if ($request->type === 'numeric') {
                $numericData = json_decode($request->numeric_data, true);
                if (!isset($numericData['value'])) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Valeur numérique requise'], 422);
                }
            }

            // ✅ Build correct_answer using shared helper
            $correctAnswer = $this->buildCorrectAnswer($request);

            $question = ExamQuestion::create([
                'exam_id'        => $exam->id,
                'type'           => $request->type,
                'question_text'  => $request->question_text,
                'question_image' => $imagePath,
                'points'         => $request->points,
                'order'          => $lastOrder + 1,
                'options'        => $options,
                'correct_answer' => $correctAnswer,
                'explanation'    => $request->explanation,
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Question ajoutée avec succès!',
                'question' => $question,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding question', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, ExamQuestion $question)
    {
        $rules = [
            'type'           => 'required|in:qcm,true_false,text,essay,checkbox,fill_blanks,matching,ordering,numeric',
            'question_text'  => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'points'         => 'required|numeric|min:0.5|max:100',
            'explanation'    => 'nullable|string',
        ];

        if (in_array($request->type, ['qcm', 'checkbox'])) {
            $rules['options'] = 'required|json';
        }
        if (in_array($request->type, ['true_false', 'text'])) {
            $rules['correct_answer'] = 'required|string';
        }
        if ($request->type === 'fill_blanks')  { $rules['blanks']          = 'required|json'; }
        if ($request->type === 'matching')     { $rules['matching_pairs']  = 'required|json'; }
        if ($request->type === 'ordering')     { $rules['order_items']     = 'required|json'; }
        if ($request->type === 'numeric')      { $rules['numeric_data']    = 'required|json'; }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Validation échouée',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $imagePath = $question->question_image;
            if ($request->hasFile('question_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('exam_questions', 'public');
            }

            // ── Options (QCM / Checkbox) ──────────────────────────────────
            $options = null;
            if (in_array($request->type, ['qcm', 'checkbox'])) {
                $optionsData = json_decode($request->options, true);
                if (!is_array($optionsData) || empty($optionsData)) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Options invalides'], 422);
                }
                $options = $optionsData;
            }

            // ── Extra validation ──────────────────────────────────────────
            if ($request->type === 'fill_blanks') {
                $blanksData = json_decode($request->blanks, true);
                $blankCount = substr_count($request->question_text, '[___]');
                if ($blankCount !== count($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Le nombre de blancs [___] ({$blankCount}) ne correspond pas au nombre de réponses (" . count($blanksData) . ")",
                    ], 422);
                }
            }
            if ($request->type === 'matching') {
                $pairsData = json_decode($request->matching_pairs, true);
                if (count($pairsData) < 2) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Minimum 2 paires requises'], 422);
                }
            }
            if ($request->type === 'ordering') {
                $itemsData = json_decode($request->order_items, true);
                if (count($itemsData) < 2) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Minimum 2 éléments requis'], 422);
                }
            }
            if ($request->type === 'numeric') {
                $numericData = json_decode($request->numeric_data, true);
                if (!isset($numericData['value'])) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Valeur numérique requise'], 422);
                }
            }

            // ✅ Build correct_answer using shared helper
            $correctAnswer = $this->buildCorrectAnswer($request);

            $question->update([
                'type'           => $request->type,
                'question_text'  => $request->question_text,
                'question_image' => $imagePath,
                'points'         => $request->points,
                'options'        => $options,
                'correct_answer' => $correctAnswer,
                'explanation'    => $request->explanation,
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Question mise à jour avec succès!',
                'question' => $question,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating question', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete question
     */
    public function deleteQuestion(ExamQuestion $question)
    {
        try {
            if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
                Storage::disk('public')->delete($question->question_image);
            }

            $question->delete();

            return response()->json(['success' => true, 'message' => 'Question supprimée!']);

        } catch (\Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.',
            ], 500);
        }
    }

    /**
     * Reorder questions
     */
    public function reorderQuestions(Request $request, Exam $exam)
    {
        $validator = Validator::make($request->all(), [
            'questions'   => 'required|array',
            'questions.*' => 'exists:exam_questions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->questions as $index => $questionId) {
                ExamQuestion::where('id', $questionId)
                    ->where('exam_id', $exam->id)
                    ->update(['order' => $index + 1]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ordre mis à jour!']);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['success' => false, 'message' => 'Erreur.'], 500);
        }
    }

    // =========================================================================
    // STUDENT EXAM TAKING METHODS
    // =========================================================================

    /**
     * Liste des examens disponibles pour étudiant
     */
    public function availableExams()
    {
        $user = Auth::user();

        $inscriptions = Inscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereHas('formation.category', function ($query) {
                $query->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU',
                ]);
            })
            ->with(['formation.modules' => fn($q) => $q->where('progress', '>=', 100)])
            ->get();

        $availableExams = collect();

        foreach ($inscriptions as $inscription) {
            foreach ($inscription->formation->modules as $module) {
                $exams = $module->availableExams()
                    ->whereDoesntHave('rattrapageOf')
                    ->with(['questions', 'attempts' => fn($q) => $q->where('user_id', $user->id)])
                    ->get();

                foreach ($exams as $exam) {
                    $exam->can_attempt_data = $exam->canUserAttempt($user->id);
                    $exam->inscription_id   = $inscription->id;
                    $exam->module_title     = $module->title;
                    $availableExams->push($exam);
                }
            }
        }

        $rattrapageExams = collect();

        $rattrapageStudentRecords = \App\Models\ExamRattrapageStudent::where('user_id', $user->id)
            ->with([
                'rattrapage.rattrapageExam.questions',
                'rattrapage.rattrapageExam.attempts' => fn($q) => $q->where('user_id', $user->id),
                'inscription',
            ])
            ->get();

        foreach ($rattrapageStudentRecords as $record) {
            $rattrapage     = $record->rattrapage;
            $rattrapageExam = $rattrapage->rattrapageExam;

            if (!$rattrapageExam || !$rattrapageExam->isAvailable()) continue;

            $rattrapageExams->push([
                'exam'               => $rattrapageExam,
                'can_attempt_data'   => $rattrapageExam->canUserAttempt($user->id),
                'inscription_id'     => $record->inscription_id,
                'module_title'       => $rattrapageExam->module->title ?? '—',
                'eligibility_reason' => $record->eligibility_reason,
                'original_score'     => $record->original_score,
            ]);
        }

        return view('exams.student.available', compact('availableExams', 'rattrapageExams'));
    }

    /**
     * Start exam attempt
     */
    public function startAttempt(Request $request, Exam $exam)
    {
        $user = Auth::user();

        $inscription = Inscription::where('id', $request->inscription_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereHas('formation.category', function ($query) {
                $query->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU',
                ]);
            })
            ->first();

        if (!$inscription) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à cette formation.');
        }

        $rattrapageOf = $exam->rattrapageOf;
        if ($rattrapageOf && !$rattrapageOf->isUserAllowed($user->id)) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à passer ce rattrapage.');
        }

        $canAttempt = $exam->canUserAttempt($user->id);

        if (!$canAttempt['can_attempt']) {
            if (isset($canAttempt['ongoing_attempt'])) {
                return redirect()->route('exams.take', $canAttempt['ongoing_attempt']);
            }
            return redirect()->back()->with('error', $canAttempt['reason']);
        }

        DB::beginTransaction();
        try {
            $attemptNumber = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->count() + 1;

            $now       = Carbon::now();
            $timeLimit = $now->copy()->addMinutes($exam->duration_minutes);

            $attempt = ExamAttempt::create([
                'exam_id'        => $exam->id,
                'user_id'        => $user->id,
                'inscription_id' => $inscription->id,
                'attempt_number' => $attemptNumber,
                'started_at'     => $now,
                'time_limit_at'  => $timeLimit,
                'status'         => ExamAttempt::STATUS_IN_PROGRESS,
            ]);

            DB::commit();

            return redirect()->route('exams.take', $attempt);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error starting attempt: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors du démarrage de l\'examen.');
        }
    }

    /**
     * Take exam (display questions)
     */
    public function takeExam(ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) abort(403);

        if ($attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('exams.result', $attempt)
                ->with('info', 'Cet examen est déjà terminé.');
        }

        if ($attempt->isTimedOut()) {
            $attempt->status = ExamAttempt::STATUS_TIMED_OUT;
            $attempt->calculateScore();
            $attempt->save();

            return redirect()->route('exams.result', $attempt)
                ->with('warning', 'Le temps est écoulé. Votre examen a été soumis automatiquement.');
        }

        $exam = $attempt->exam;
        $exam->load(['questions' => function ($query) use ($exam) {
            $query->orderBy('order');
            if ($exam->shuffle_questions) {
                $query->inRandomOrder();
            }
        }]);

        return view('exams.student.take', compact('attempt', 'exam'));
    }

    /**
     * Save answer (AJAX)
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        if ($attempt->isTimedOut()) {
            return response()->json(['success' => false, 'message' => 'Temps écoulé'], 400);
        }

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:exam_questions,id',
            'answer'      => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $attempt->saveAnswer($request->question_id, $request->answer);

            return response()->json(['success' => true, 'message' => 'Réponse sauvegardée']);

        } catch (\Exception $e) {
            Log::error('Error saving answer: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erreur'], 500);
        }
    }

    /**
     * Submit exam
     */
    public function submitExam(Request $request, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->back()->with('error', 'Non autorisé');
        }

        DB::beginTransaction();
        try {
            $attempt->submit();
            DB::commit();

            return redirect()->route('exams.result', $attempt)
                ->with('success', 'Examen soumis avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting exam: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la soumission.');
        }
    }

    /**
     * View result
     */
    public function viewResult(ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) abort(403);

        $exam = $attempt->exam;
        $exam->load(['questions' => fn($q) => $q->orderBy('order')]);

        return view('exams.student.result', compact('attempt', 'exam'));
    }

    /**
     * My attempts (history)
     */
    public function myAttempts()
    {
        $user = Auth::user();

        $attempts = ExamAttempt::where('user_id', $user->id)
            ->whereHas('exam')
            ->with(['exam.module', 'inscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('exams.student.attempts', compact('attempts'));
    }

    /**
     * Grade attempt manually (Essay questions)
     */
    public function gradeAttempt(Request $request, ExamAttempt $attempt)
    {
        $validator = Validator::make($request->all(), [
            'grades'   => 'required|array',
            'grades.*' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $attempt->gradeManually($request->grades);
            DB::commit();

            return redirect()->back()->with('success', 'Correction effectuée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error grading attempt: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de la correction.');
        }
    }

    /**
     * Exam attempts list (Admin/Consultant)
     */
     public function examAttempts(Request $request, Exam $exam)
    {
        $user = Auth::user();
 
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }
 
        // ── Toutes les inscriptions actives pour ce module (via formation) ──
        $inscriptions = Inscription::where('status', 'active')
            ->whereHas('formation.modules', fn($q) => $q->where('modules.id', $exam->module_id))
            ->with(['user', 'formation.category'])
            ->get();
 
        // Grouper par formation
        $formationGroups = $inscriptions->groupBy('formation_id')->map(function ($group) {
            return [
                'formation' => $group->first()->formation,
                'inscriptions' => $group,
            ];
        })->values();
 
        // ── Filtre par formation_id (bouton "Voir") ──
        $selectedFormationId = $request->filled('formation_id')
            ? (int) $request->formation_id
            : null;
 
        // ── Requête de base des tentatives ──
        $query = ExamAttempt::where('exam_id', $exam->id)
            ->with(['user', 'inscription.formation']);
 
        // Filtre formation
        if ($selectedFormationId) {
            $query->whereHas('inscription', fn($q) => $q->where('formation_id', $selectedFormationId));
        }
 
        // Filtre recherche par nom / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
 
        // Filtre score
        if ($request->filled('score_filter')) {
            $threshold = (int) $request->get('score_threshold', $exam->passing_score);
            if ($request->score_filter === 'below') {
                $query->whereIn('status', ['submitted', 'graded'])
                      ->where('score', '<', $threshold);
            } elseif ($request->score_filter === 'passed') {
                $query->where('passed', true);
            } elseif ($request->score_filter === 'failed') {
                $query->whereIn('status', ['submitted', 'graded'])->where('passed', false);
            }
        }
 
        $attempts = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
 
        // ── Étudiants absents (inscrits mais 0 tentative) ──
        // On calcule ça par formation sélectionnée ou toutes formations
        $absentStudents = collect();
 
        if ($request->filled('score_filter') && $request->score_filter === 'absent') {
            $inscriptionsScope = $selectedFormationId
                ? $inscriptions->where('formation_id', $selectedFormationId)
                : $inscriptions;
 
            $attemptedUserIds = ExamAttempt::where('exam_id', $exam->id)
                ->pluck('user_id')
                ->unique();
 
            $absentStudents = $inscriptionsScope->filter(
                fn($ins) => !$attemptedUserIds->contains($ins->user_id)
            )->values();
 
            // Override: on affiche les absents, pas les attempts
            $attempts = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 20
            );
        }
 
        // ── Stats par formation (pour les cartes du haut) ──
        $formationStats = $formationGroups->map(function ($group) use ($exam) {
            $formation      = $group['formation'];
            $inscriptions   = $group['inscriptions'];
            $studentIds     = $inscriptions->pluck('user_id');
            $inscriptionIds = $inscriptions->pluck('id');
 
            $attemptCount = ExamAttempt::where('exam_id', $exam->id)
                ->whereIn('inscription_id', $inscriptionIds)
                ->whereIn('status', ['submitted', 'graded', 'timed_out'])
                ->distinct('user_id')
                ->count('user_id');
 
            $passedCount = ExamAttempt::where('exam_id', $exam->id)
                ->whereIn('inscription_id', $inscriptionIds)
                ->where('passed', true)
                ->distinct('user_id')
                ->count('user_id');
 
            $avgScore = ExamAttempt::where('exam_id', $exam->id)
                ->whereIn('inscription_id', $inscriptionIds)
                ->whereIn('status', ['submitted', 'graded'])
                ->avg('score');
 
            return [
                'formation'      => $formation,
                'total_students' => $studentIds->count(),
                'attempted'      => $attemptCount,
                'absent'         => $studentIds->count() - $attemptCount,
                'passed'         => $passedCount,
                'avg_score'      => round($avgScore ?? 0, 1),
            ];
        });
 
        return view('exams.attempts', compact(
            'exam',
            'attempts',
            'formationGroups',
            'formationStats',
            'selectedFormationId',
            'absentStudents'
        ));
    }

    /**
     * Attempt details with answers
     */
    public function attemptDetails(ExamAttempt $attempt)
    {
        $user = Auth::user();
        $exam = $attempt->exam;

        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $exam->load(['questions' => fn($q) => $q->orderBy('order')]);

        return view('exams.attempt-details', compact('attempt', 'exam'));
    }

    /**
     * Get question data (AJAX)
     */
    public function getQuestion(ExamQuestion $question)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $question->exam->created_by !== $user->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $questionData = $question->toArray();

        if (in_array($question->type, ['fill_blanks', 'matching', 'ordering', 'numeric'])) {
            if (is_string($questionData['correct_answer'])) {
                $questionData['correct_answer'] = json_decode($questionData['correct_answer'], true);
            }
        }

        return response()->json($questionData);
    }

    /**
     * Security logs
     */
    public function viewSecurityLogs(ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($user->hasRole('Consultant') && $attempt->exam->created_by !== $user->id) {
            abort(403);
        }

        $logs = DB::table('exam_security_logs')
            ->where('exam_attempt_id', $attempt->id)
            ->orderBy('activity_timestamp', 'desc')
            ->get();

        $suspiciousActivities = [
            'copy_attempt'      => $logs->where('activity_type', 'copy_attempt')->count(),
            'right_click'       => $logs->where('activity_type', 'right_click')->count(),
            'tab_switch'        => $logs->where('activity_type', 'tab_switch')->count(),
            'fullscreen_exit'   => $logs->where('activity_type', 'fullscreen_exit')->count(),
            'devtools_attempt'  => $logs->where('activity_type', 'devtools_attempt')->count(),
            'devtools_detected' => $logs->where('activity_type', 'devtools_detected')->count(),
        ];

        return view('exams.security-logs', compact('attempt', 'logs', 'suspiciousActivities'));
    }
}