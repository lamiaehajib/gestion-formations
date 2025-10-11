<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FormationMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:message-create')->only(['create', 'store']);
        $this->middleware('can:message-list-all')->only('index');
        $this->middleware('can:message-view-all')->only('show');
        $this->middleware('can:message-delete')->only('destroy');
        $this->middleware('can:message-get-students-count')->only('getFormationStudentsCount');
        $this->middleware('can:message-view-own')->only(['studentMessages', 'studentShow']);
    }

    public function create()
    {
        $formations = Formation::where('status', 'published')
            ->with('inscriptions')
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'title' => $formation->title,
                    'students_count' => $formation->active_students_count,
                ];
            });

        return view('admin.messages.create', compact('formations'));
    }

    /**
     * ✨ MODIFIÉ: Enregistre et envoie le message (avec support audio)
     */
    public function store(Request $request)
    {
        // ✨ Règles de validation mises à jour
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string', // Nullable car on peut avoir que de l'audio
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:10240', // 10MB max
            'priority' => 'required|in:normal,urgent,important',
            'formation_ids' => 'required|array|min:1',
            'formation_ids.*' => 'exists:formations,id',
        ], [
            'subject.required' => 'Le sujet est requis.',
            'audio_file.mimes' => 'Le fichier audio doit être au format: mp3, wav, ogg, m4a ou webm.',
            'audio_file.max' => 'Le fichier audio ne doit pas dépasser 10 Mo.',
            'formation_ids.required' => 'Vous devez sélectionner au moins une formation.',
            'formation_ids.min' => 'Vous devez sélectionner au moins une formation.',
        ]);

        // ✨ Validation personnalisée: au moins un message texte OU audio
        $validator->after(function ($validator) use ($request) {
            if (empty($request->message) && !$request->hasFile('audio_file')) {
                $validator->errors()->add('message', 'Vous devez fournir soit un message texte, soit un message audio.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $audioPath = null;
            $audioDuration = null;

            // ✨ Gérer l'upload du fichier audio
            if ($request->hasFile('audio_file')) {
                $audioFile = $request->file('audio_file');
                $audioPath = $audioFile->store('formation_messages/audio', 'public');
                
                // Optionnel: Calculer la durée de l'audio (nécessite getID3 ou FFmpeg)
                // $audioDuration = $this->getAudioDuration(storage_path('app/public/' . $audioPath));
            }

            // Création du message
            $message = FormationMessage::create([
                'subject' => $request->subject,
                'message' => $request->message,
                'audio_path' => $audioPath,
                'audio_duration' => $audioDuration,
                'priority' => $request->priority,
                'status' => 'draft',
                'sent_by' => Auth::id(),
            ]);

            // Envoie le message aux formations sélectionnées
            $message->sendToFormations($request->formation_ids);

            $messageType = $audioPath ? 'audio' : 'texte';
            return redirect()->route('messages.index')
                ->with('success', "Message {$messageType} envoyé avec succès à " . $message->recipients_count . ' étudiant(s)');

        } catch (\Exception $e) {
            // Supprimer le fichier audio en cas d'erreur
            if (isset($audioPath) && Storage::disk('public')->exists($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
            
            \Log::error('Erreur lors de la création du message de formation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'envoi du message : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function index()
    {
        $baseQuery = FormationMessage::query();
        
        $totalMessages = $baseQuery->clone()->count();
        $sentToday = $baseQuery->clone()->whereDate('created_at', today())->count();
        $urgentMessages = $baseQuery->clone()->where('priority', 'urgent')->count();
        $totalRecipients = $baseQuery->clone()->sum('recipients_count');

        $messages = $baseQuery->clone()
            ->with(['sender', 'formations'])
            ->withCount('recipientRecords')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.messages.index', compact(
            'messages',
            'totalMessages',
            'sentToday',
            'urgentMessages',
            'totalRecipients'
        ));
    }

    public function show($id)
    {
        $message = FormationMessage::with(['sender', 'formations', 'recipientRecords.user', 'recipientRecords.inscription'])
            ->findOrFail($id);

        return view('admin.messages.show', compact('message'));
    }

    public function studentMessages()
    {
        $user = Auth::user();
        
        $messages = $user->formationMessages()
            ->with(['sender', 'formations'])
            ->paginate(20);

        return view('student.messages.index', compact('messages'));
    }


    public function edit($id)
    {
        $message = FormationMessage::with(['formations'])->findOrFail($id);
        
        // Récupérer toutes les formations disponibles
        $formations = Formation::where('status', 'published')
            ->with('inscriptions')
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'title' => $formation->title,
                    'students_count' => $formation->active_students_count,
                ];
            });

        // IDs des formations déjà sélectionnées
        $selectedFormationIds = $message->formations->pluck('id')->toArray();

        return view('admin.messages.edit', compact('message', 'formations', 'selectedFormationIds'));
    }

    /**
     * ✨ Met à jour le message
     */
    public function update(Request $request, $id)
    {
        $message = FormationMessage::findOrFail($id);
        
        // Validation
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a,webm|max:10240',
            'remove_audio' => 'nullable|boolean', // Pour supprimer l'audio existant
            'priority' => 'required|in:normal,urgent,important',
            'formation_ids' => 'required|array|min:1',
            'formation_ids.*' => 'exists:formations,id',
        ], [
            'subject.required' => 'Le sujet est requis.',
            'audio_file.mimes' => 'Le fichier audio doit être au format: mp3, wav, ogg, m4a ou webm.',
            'audio_file.max' => 'Le fichier audio ne doit pas dépasser 10 Mo.',
            'formation_ids.required' => 'Vous devez sélectionner au moins une formation.',
        ]);

        // Validation personnalisée: au moins un message texte OU audio
        $validator->after(function ($validator) use ($request, $message) {
            // Si on supprime l'audio ET qu'il n'y a pas de nouveau fichier ET pas de texte
            if ($request->remove_audio && !$request->hasFile('audio_file') && empty($request->message)) {
                $validator->errors()->add('message', 'Vous devez fournir soit un message texte, soit un message audio.');
            }
            // Si le message n'a pas d'audio ET pas de nouveau fichier ET pas de texte
            if (!$message->audio_path && !$request->hasFile('audio_file') && empty($request->message)) {
                $validator->errors()->add('message', 'Vous devez fournir soit un message texte, soit un message audio.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $audioPath = $message->audio_path;
            $audioDuration = $message->audio_duration;

            // ✨ Supprimer l'ancien audio si demandé
            if ($request->remove_audio && $message->audio_path) {
                if (Storage::disk('public')->exists($message->audio_path)) {
                    Storage::disk('public')->delete($message->audio_path);
                }
                $audioPath = null;
                $audioDuration = null;
            }

            // ✨ Upload du nouveau fichier audio
            if ($request->hasFile('audio_file')) {
                // Supprimer l'ancien audio
                if ($message->audio_path && Storage::disk('public')->exists($message->audio_path)) {
                    Storage::disk('public')->delete($message->audio_path);
                }
                
                // Stocker le nouveau
                $audioFile = $request->file('audio_file');
                $audioPath = $audioFile->store('formation_messages/audio', 'public');
                
                // Optionnel: Calculer la durée
                // $audioDuration = $this->getAudioDuration(storage_path('app/public/' . $audioPath));
            }

            // Mettre à jour le message
            $message->update([
                'subject' => $request->subject,
                'message' => $request->message,
                'audio_path' => $audioPath,
                'audio_duration' => $audioDuration,
                'priority' => $request->priority,
            ]);

            // Mettre à jour les formations ciblées
            $message->formations()->sync($request->formation_ids);
            
            // Recalculer le nombre de destinataires
            $message->updateRecipientsCount();

            return redirect()->route('messages.show', $message->id)
                ->with('success', 'Message mis à jour avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du message: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du message.')
                ->withInput();
        }
    }

    public function studentShow($id)
    {
        $user = Auth::user();
        
        $message = FormationMessage::findOrFail($id);
        
        $receipt = $message->recipientRecords()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $receipt->markAsRead();

        $studentFormation = $message->formations()
            ->whereHas('inscriptions', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereIn('status', ['active', 'completed']);
            })
            ->first();

        return view('student.messages.show', compact('message', 'receipt', 'studentFormation'));
    }

    /**
     * ✨ MODIFIÉ: Supprime un message (avec son fichier audio)
     */
    public function destroy($id)
    {
        try {
            $message = FormationMessage::findOrFail($id);
            
            // Supprimer le fichier audio s'il existe
            if ($message->audio_path && Storage::disk('public')->exists($message->audio_path)) {
                Storage::disk('public')->delete($message->audio_path);
            }
            
            $message->delete();

            return redirect()->route('messages.index')
                ->with('success', 'Le message a été supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du message.');
        }
    }

    public function getFormationStudentsCount(Request $request)
    {
        $formationIds = $request->input('formation_ids', []);
        
        if (empty($formationIds)) {
            return response()->json(['count' => 0]);
        }

        $count = \App\Models\Inscription::whereIn('formation_id', $formationIds)
            ->whereIn('status', ['active', 'pending'])
            ->distinct('user_id')
            ->count('user_id');

        return response()->json(['count' => $count]);
    }
    
    /**
     * ✨ OPTIONNEL: Fonction pour obtenir la durée de l'audio
     * Nécessite la bibliothèque getID3 (composer require james-heinrich/getid3)
     */
    private function getAudioDuration($filePath)
    {
        try {
            $getID3 = new \getID3;
            $file = $getID3->analyze($filePath);
            return isset($file['playtime_seconds']) ? (int) $file['playtime_seconds'] : null;
        } catch (\Exception $e) {
            \Log::warning('Impossible de déterminer la durée audio: ' . $e->getMessage());
            return null;
        }
    }

    public function studentShowContent($id)
{
    $user = Auth::user();
    
    $message = FormationMessage::findOrFail($id);
    
    $receipt = $message->recipientRecords()
        ->where('user_id', $user->id)
        ->firstOrFail();

    $receipt->markAsRead();

    $studentFormation = $message->formations()
        ->whereHas('inscriptions', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->whereIn('status', ['active', 'completed']);
        })
        ->first();

    // Préparer les données pour le frontend
    $data = [
        'sender_name' => $message->sender->name ?? 'Administrateur',
        'sender_initial' => strtoupper(substr($message->sender->name ?? 'A', 0, 1)),
        'subject' => $message->subject,
        'message' => $message->message ? nl2br(e($message->message)) : null,
        'priority' => $message->priority,
        'created_at' => $message->created_at->diffForHumans(),
        'time' => $message->created_at->format('H:i'),
        'audio_path' => $message->audio_path,
        'audio_url' => $message->audio_path ? Storage::url($message->audio_path) : null,
        'audio_duration' => $message->audio_duration ? gmdate('i:s', $message->audio_duration) : null,
        'formation' => $studentFormation ? $studentFormation->title : null,
    ];

    return response()->json($data);
}



public function getMessageDetails($id)
{
    $message = FormationMessage::with([
        'formations.activeStudents',
        'recipientRecords.user',
        'recipientRecords.inscription.formation',
        'sender'
    ])->findOrFail($id);

    // Calculate stats
    $recipientsCount = $message->recipientRecords->count();
    $readCount = $message->recipientRecords->where('is_read', true)->count();
    $unreadCount = $recipientsCount - $readCount;
    $readPercentage = $recipientsCount > 0 ? round(($readCount / $recipientsCount) * 100) : 0;

    // Format formations
    $formations = $message->formations->map(function ($formation) {
        return [
            'title' => $formation->title,
            'count' => $formation->activeStudents->count() . ' étudiant(s)'
        ];
    });

    // Format recipients
    $recipients = $message->recipientRecords->map(function ($recipient) {
        return [
            'name' => $recipient->user->name,
            'email' => $recipient->user->email,
            'formation' => $recipient->inscription->formation->title ?? 'Non spécifié',
            'isRead' => $recipient->is_read,
            'readAt' => $recipient->read_at ? $recipient->read_at->diffForHumans() : null
        ];
    });

    return response()->json([
        'id' => $message->id,
        'subject' => $message->subject,
        'message' => $message->message,
        'priority' => $message->priority,
        'audio_path' => $message->audio_path,
        'audio_duration' => $message->audio_duration,
        'created_at' => $message->created_at->format('Y-m-d H:i'),
        'sent_at' => $message->sent_at ? $message->sent_at->diffForHumans() : null,
        'sender' => [
            'name' => $message->sender->name ?? 'Inconnu'
        ],
        'stats' => [
            'total' => $recipientsCount,
            'read' => $readCount,
            'unread' => $unreadCount,
            'percentage' => $readPercentage
        ],
        'formations' => $formations,
        'recipients' => $recipients
    ]);
}
}