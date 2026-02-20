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
    $user = Auth::user();
    $query = Exam::with(['module', 'creator', 'questions'])
        ->whereDoesntHave('rattrapageOf');  // ← Exclude rattrapages from main list

    // Ila consultant, ghir exams dyalo
    if ($user->hasRole('Consultant')) {
        $query->where('created_by', $user->id);
    }

    // Filters
    if ($request->filled('module_id')) {
        $query->where('module_id', $request->module_id);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $exams = $query->orderBy('created_at', 'desc')->paginate(15);
    
    // Get modules للفلاتر
    $modules = $user->hasRole('Consultant') 
        ? Module::where('user_id', $user->id)->get()
        : Module::all();

    return view('exams.index', compact('exams', 'modules'));
}

    /**
     * Form de création d'exam
     */
   /**
 * Form de création d'exam
 */
public function create(Request $request)
{
    $user = Auth::user();
    
    // ✅ Get modules avec progress = 100% et formations des catégories spécifiques
    if ($user->hasRole('Consultant')) {
        $modules = Module::where('user_id', $user->id)
            ->where('progress', '>=', 100)
            ->whereHas('formations.category', function($query) {
                $query->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU'
                ]);
            })
            ->with(['formations' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->whereIn('name', [
                        'Licence Professionnelle',
                        'Master Professionnelle',
                        'LICENCE PROFESSIONNELLE RECONNU'
                    ]);
                })
                ->orderBy('title'); // ✅ Trier par titre
            }])
            ->get();
    } else {
        // Admin voit tous les modules complétés
        $modules = Module::where('progress', '>=', 100)
            ->whereHas('formations.category', function($query) {
                $query->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU'
                ]);
            })
            ->with(['formations' => function($query) {
                $query->whereHas('category', function($q) {
                    $q->whereIn('name', [
                        'Licence Professionnelle',
                        'Master Professionnelle',
                        'LICENCE PROFESSIONNELLE RECONNU'
                    ]);
                })
                ->orderBy('title');
            }])
            ->get();
    }

    // Make sure modules exist
    if ($modules->isEmpty()) {
        return redirect()->back()
            ->with('error', 'Aucun module complété (100%) disponible. Les modules doivent avoir un progrès de 100% pour créer des examens.');
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
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'module_id' => $request->module_id,
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'show_results_immediately' => $request->boolean('show_results_immediately', true),
                'show_correct_answers' => $request->boolean('show_correct_answers', true),
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'status' => $request->status,
                'created_by' => Auth::id(),
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
        
        // Check authorization
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $exam->load(['module', 'creator', 'questions' => function($query) {
            $query->orderBy('order');
        }]);

        // Get statistics
        $stats = $exam->getStatistics();

        return view('exams.show', compact('exam', 'stats'));
    }

    /**
     * Edit exam
     */
    public function edit(Exam $exam)
    {
        
        $user = Auth::user();
        
        // Check authorization
        if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $exam->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

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
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'show_results_immediately' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $exam->update([
                'module_id' => $request->module_id,
                'title' => $request->title,
                'description' => $request->description,
                'duration_minutes' => $request->duration_minutes,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'show_results_immediately' => $request->boolean('show_results_immediately', true),
                'show_correct_answers' => $request->boolean('show_correct_answers', true),
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'status' => $request->status,
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

    /**
     * Add question to exam
     */
   public function addQuestion(Request $request, Exam $exam)
{
    // Base validation
    $rules = [
        'type' => 'required|in:qcm,true_false,text,essay,checkbox,fill_blanks,matching,ordering,numeric',
        'question_text' => 'required|string',
        'question_image' => 'nullable|image|max:2048',
        'points' => 'required|numeric|min:0.5|max:100',
        'explanation' => 'nullable|string',
    ];

    // Type-specific validation
    if (in_array($request->type, ['qcm', 'checkbox'])) {
        $rules['options'] = 'required|json';
    }
    if (in_array($request->type, ['true_false', 'text'])) {
        $rules['correct_answer'] = 'required|string';
    }
    if ($request->type === 'fill_blanks') {
        $rules['blanks'] = 'required|json';
    }
    if ($request->type === 'matching') {
        $rules['matching_pairs'] = 'required|json';
    }
    if ($request->type === 'ordering') {
        $rules['order_items'] = 'required|json';
    }
    if ($request->type === 'numeric') {
        $rules['numeric_data'] = 'required|json';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Validation échouée'
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = $request->file('question_image')->store('exam_questions', 'public');
        }

        // Get last order
        $lastOrder = $exam->questions()->max('order') ?? 0;

        // Prepare options/correct_answer based on type
        $options = null;
        $correctAnswer = null;

        switch ($request->type) {
            case 'qcm':
            case 'checkbox':
                $optionsData = json_decode($request->options, true);
                
                if (!is_array($optionsData) || empty($optionsData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Options invalides'
                    ], 422);
                }
                
                $options = $optionsData;
                break;

            case 'fill_blanks':
                $blanksData = json_decode($request->blanks, true);
                
                if (!is_array($blanksData) || empty($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Réponses des blancs invalides'
                    ], 422);
                }

                // Validate blank count
                $blankCount = substr_count($request->question_text, '[___]');
                if ($blankCount !== count($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Le nombre de blancs [___] ({$blankCount}) ne correspond pas au nombre de réponses (" . count($blanksData) . ")"
                    ], 422);
                }

                $correctAnswer = json_encode(['blanks' => $blanksData]);
                break;

            case 'matching':
                $pairsData = json_decode($request->matching_pairs, true);
                
                if (!is_array($pairsData) || count($pairsData) < 2) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum 2 paires requises'
                    ], 422);
                }

                $correctAnswer = json_encode(['pairs' => $pairsData]);
                break;

            case 'ordering':
                $itemsData = json_decode($request->order_items, true);
                
                if (!is_array($itemsData) || count($itemsData) < 2) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum 2 éléments requis'
                    ], 422);
                }

                $correctAnswer = json_encode(['items' => $itemsData]);
                break;

            case 'numeric':
                $numericData = json_decode($request->numeric_data, true);
                
                if (!isset($numericData['value'])) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Valeur numérique requise'
                    ], 422);
                }

                $correctAnswer = json_encode($numericData);
                break;

            default:
                $correctAnswer = $request->correct_answer;
                break;
        }

        $question = ExamQuestion::create([
            'exam_id' => $exam->id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'question_image' => $imagePath,
            'points' => $request->points,
            'order' => $lastOrder + 1,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Question ajoutée avec succès!',
            'question' => $question
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error adding question', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update question
     */
    /**
 * Update question
 */
public function updateQuestion(Request $request, ExamQuestion $question)
{
    // Base validation
    $rules = [
        'type' => 'required|in:qcm,true_false,text,essay,checkbox,fill_blanks,matching,ordering,numeric',
        'question_text' => 'required|string',
        'question_image' => 'nullable|image|max:2048',
        'points' => 'required|numeric|min:0.5|max:100',
        'explanation' => 'nullable|string',
    ];

    // Type-specific validation
    if (in_array($request->type, ['qcm', 'checkbox'])) {
        $rules['options'] = 'required|json';
    }
    if (in_array($request->type, ['true_false', 'text'])) {
        $rules['correct_answer'] = 'required|string';
    }
    if ($request->type === 'fill_blanks') {
        $rules['blanks'] = 'required|json';
    }
    if ($request->type === 'matching') {
        $rules['matching_pairs'] = 'required|json';
    }
    if ($request->type === 'ordering') {
        $rules['order_items'] = 'required|json';
    }
    if ($request->type === 'numeric') {
        $rules['numeric_data'] = 'required|json';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => 'Validation échouée'
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Handle image upload
        $imagePath = $question->question_image;
        if ($request->hasFile('question_image')) {
            // Delete old image
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('question_image')->store('exam_questions', 'public');
        }

        // Prepare options/correct_answer based on type
        $options = null;
        $correctAnswer = null;

        switch ($request->type) {
            case 'qcm':
            case 'checkbox':
                $optionsData = json_decode($request->options, true);
                
                if (!is_array($optionsData) || empty($optionsData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Options invalides'
                    ], 422);
                }
                
                $options = $optionsData;
                break;

            case 'fill_blanks':
                $blanksData = json_decode($request->blanks, true);
                
                if (!is_array($blanksData) || empty($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Réponses des blancs invalides'
                    ], 422);
                }

                // Validate blank count
                $blankCount = substr_count($request->question_text, '[___]');
                if ($blankCount !== count($blanksData)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Le nombre de blancs [___] ({$blankCount}) ne correspond pas au nombre de réponses (" . count($blanksData) . ")"
                    ], 422);
                }

                $correctAnswer = json_encode(['blanks' => $blanksData]);
                break;

            case 'matching':
                $pairsData = json_decode($request->matching_pairs, true);
                
                if (!is_array($pairsData) || count($pairsData) < 2) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum 2 paires requises'
                    ], 422);
                }

                $correctAnswer = json_encode(['pairs' => $pairsData]);
                break;

            case 'ordering':
                $itemsData = json_decode($request->order_items, true);
                
                if (!is_array($itemsData) || count($itemsData) < 2) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum 2 éléments requis'
                    ], 422);
                }

                $correctAnswer = json_encode(['items' => $itemsData]);
                break;

            case 'numeric':
                $numericData = json_decode($request->numeric_data, true);
                
                if (!isset($numericData['value'])) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Valeur numérique requise'
                    ], 422);
                }

                $correctAnswer = json_encode($numericData);
                break;

            default:
                $correctAnswer = $request->correct_answer;
                break;
        }

        $question->update([
            'type' => $request->type,
            'question_text' => $request->question_text,
            'question_image' => $imagePath,
            'points' => $request->points,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Question mise à jour avec succès!',
            'question' => $question
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error updating question', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Delete question
     */
    public function deleteQuestion(ExamQuestion $question)
    {
        try {
            // Delete image
            if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
                Storage::disk('public')->delete($question->question_image);
            }

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Question supprimée!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.'
            ], 500);
        }
    }

    /**
     * Reorder questions
     */
    public function reorderQuestions(Request $request, Exam $exam)
    {
        $validator = Validator::make($request->all(), [
            'questions' => 'required|array',
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

            return response()->json([
                'success' => true,
                'message' => 'Ordre mis à jour!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur.'
            ], 500);
        }
    }

    // ============================================
    // STUDENT EXAM TAKING METHODS
    // ============================================

    /**
     * Liste des examens disponibles pour étudiant
     */
    public function availableExams()
{
    $user = Auth::user();

    // ── Regular exams ──────────────────────────────────────────────────────
    $inscriptions = Inscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereHas('formation.category', function ($query) {
            $query->whereIn('name', [
                'Licence Professionnelle',
                'Master Professionnelle',
                'LICENCE PROFESSIONNELLE RECONNU',
            ]);
        })
        ->with(['formation.modules' => function ($query) {
            $query->where('progress', '>=', 100);
        }])
        ->get();

    $availableExams = collect();

    foreach ($inscriptions as $inscription) {
        foreach ($inscription->formation->modules as $module) {
            // Only non-rattrapage exams (normal exams)
            $exams = $module->availableExams()
                ->whereDoesntHave('rattrapageOf')           // ← exclude rattrapages here
                ->with(['questions', 'attempts' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->get();

            foreach ($exams as $exam) {
                $canAttempt = $exam->canUserAttempt($user->id);
                $exam->can_attempt_data  = $canAttempt;
                $exam->inscription_id    = $inscription->id;
                $exam->module_title      = $module->title;
                $availableExams->push($exam);
            }
        }
    }

    // ── Rattrapage exams ───────────────────────────────────────────────────
    $rattrapageExams = collect();

    // Find all rattrapages where this student is on the whitelist
    $rattrapageStudentRecords = \App\Models\ExamRattrapageStudent::where('user_id', $user->id)
        ->with([
            'rattrapage.rattrapageExam.questions',
            'rattrapage.rattrapageExam.attempts' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            },
            'inscription',
        ])
        ->get();

    foreach ($rattrapageStudentRecords as $record) {
        $rattrapage     = $record->rattrapage;
        $rattrapageExam = $rattrapage->rattrapageExam;

        // Only show if the rattrapage exam is published & available
        if (!$rattrapageExam || !$rattrapageExam->isAvailable()) {
            continue;
        }

        $canAttempt = $rattrapageExam->canUserAttempt($user->id);

        $rattrapageExams->push([
            'exam'              => $rattrapageExam,
            'can_attempt_data'  => $canAttempt,
            'inscription_id'    => $record->inscription_id,
            'module_title'      => $rattrapageExam->module->title ?? '—',
            'eligibility_reason'=> $record->eligibility_reason,
            'original_score'    => $record->original_score,
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
    
    // Verify inscription
    $inscription = Inscription::where('id', $request->inscription_id)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->whereHas('formation.category', function ($query) {
            $query->whereIn('name', [
                'Licence Professionnelle',
                'Master Professionnelle',
                'LICENCE PROFESSIONNELLE RECONNU'
            ]);
        })
        ->first();

    if (!$inscription) {
        return redirect()->back()
            ->with('error', 'Vous n\'êtes pas inscrit à cette formation.');
    }

    // ✅ NEW: Check if this is a rattrapage exam and verify whitelist
    $rattrapageOf = $exam->rattrapageOf;
    if ($rattrapageOf && !$rattrapageOf->isUserAllowed($user->id)) {
        return redirect()->back()
            ->with('error', 'Vous n\'êtes pas autorisé à passer ce rattrapage.');
    }

    // Check if can attempt
    $canAttempt = $exam->canUserAttempt($user->id);
    
    if (!$canAttempt['can_attempt']) {
        // Check if has ongoing attempt
        if (isset($canAttempt['ongoing_attempt'])) {
            return redirect()->route('exams.take', $canAttempt['ongoing_attempt']);
        }
        
        return redirect()->back()
            ->with('error', $canAttempt['reason']);
    }

    DB::beginTransaction();
    try {
        // Count previous attempts
        $attemptNumber = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count() + 1;

        $now = Carbon::now();
        $timeLimit = $now->copy()->addMinutes($exam->duration_minutes);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'inscription_id' => $inscription->id,
            'attempt_number' => $attemptNumber,
            'started_at' => $now,
            'time_limit_at' => $timeLimit,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        DB::commit();

        return redirect()->route('exams.take', $attempt);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error starting attempt: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Erreur lors du démarrage de l\'examen.');
    }
}

    /**
     * Take exam (display questions)
     */
    public function takeExam(ExamAttempt $attempt)
    {
        $user = Auth::user();
        
        // Check ownership
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        // Check status
        if ($attempt->status !== ExamAttempt::STATUS_IN_PROGRESS) {
            return redirect()->route('exams.result', $attempt)
                ->with('info', 'Cet examen est déjà terminé.');
        }

        // Check time limit
        if ($attempt->isTimedOut()) {
            $attempt->status = ExamAttempt::STATUS_TIMED_OUT;
            $attempt->calculateScore();
            $attempt->save();
            
            return redirect()->route('exams.result', $attempt)
                ->with('warning', 'Le temps est écoulé. Votre examen a été soumis automatiquement.');
        }

        $exam = $attempt->exam;
        $exam->load(['questions' => function($query) use ($exam) {
            $query->orderBy('order');
            
            // Shuffle if enabled
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
            'answer' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $attempt->saveAnswer($request->question_id, $request->answer);

            return response()->json([
                'success' => true,
                'message' => 'Réponse sauvegardée'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving answer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur'
            ], 500);
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
            return redirect()->back()
                ->with('error', 'Erreur lors de la soumission.');
        }
    }

    /**
     * View result
     */
    public function viewResult(ExamAttempt $attempt)
    {
        $user = Auth::user();
        
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        $exam = $attempt->exam;
        $exam->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        return view('exams.student.result', compact('attempt', 'exam'));
    }

    /**
     * My attempts (history)
     */
    public function myAttempts()
{
    $user = Auth::user();
    
    $attempts = ExamAttempt::where('user_id', $user->id)
        ->whereHas('exam')  // ← add this line
        ->with(['exam.module', 'inscription'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('exams.student.attempts', compact('attempts'));
}

    /**
     * Grade attempt manually (للأسئلة Essay)
     */
    public function gradeAttempt(Request $request, ExamAttempt $attempt)
    {
        $validator = Validator::make($request->all(), [
            'grades' => 'required|array',
            'grades.*' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $attempt->gradeManually($request->grades);
            
            DB::commit();

            return redirect()->back()
                ->with('success', 'Correction effectuée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error grading attempt: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la correction.');
        }
    }


    public function examAttempts(Exam $exam)
{
    $user = Auth::user();
    
    // Check authorization
    if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
        abort(403, 'Action non autorisée.');
    }

    $attempts = $exam->attempts()
        ->with(['user', 'inscription'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('exams.attempts', compact('exam', 'attempts'));
}

/**
 * Détails d'un attempt spécifique avec réponses
 */
public function attemptDetails(ExamAttempt $attempt)
{
    $user = Auth::user();
    $exam = $attempt->exam;
    
    // Check authorization
    if ($user->hasRole('Consultant') && $exam->created_by !== $user->id) {
        abort(403, 'Action non autorisée.');
    }

    $exam->load(['questions' => function($query) {
        $query->orderBy('order');
    }]);

    return view('exams.attempt-details', compact('attempt', 'exam'));
}


public function getQuestion(ExamQuestion $question)
{
    $user = Auth::user();
    
    // Check authorization
    if ($user->hasRole('Consultant') && $question->exam->created_by !== $user->id) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    // Parse correct_answer JSON for specific types
    $questionData = $question->toArray();
    
    // For types that store JSON in correct_answer, parse it
    if (in_array($question->type, ['fill_blanks', 'matching', 'ordering', 'numeric'])) {
        if (is_string($questionData['correct_answer'])) {
            $questionData['correct_answer'] = json_decode($questionData['correct_answer'], true);
        }
    }

    return response()->json($questionData);
}

public function viewSecurityLogs(ExamAttempt $attempt)
{
    $user = Auth::user();
    
    // Only allow exam creator or admin to view
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