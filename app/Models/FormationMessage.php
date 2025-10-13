<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
// 💡 AJOUTER CES DEUX USES :
use Illuminate\Support\Facades\Mail; 
use App\Mail\NewFormationMessage;    
use App\Models\Inscription; // Assurez-vous d'importer le modèle Inscription

class FormationMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'message',
        'audio_duration',
        'audio_path',
        'priority',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_by',
        'recipients_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // العلاقات
    
    /**
     * التكوينات المرتبطة بهذه الرسالة
     */
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formation_message_formations');
    }

    /**
     * المستخدم الذي أرسل الرسالة
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * المستلمون (الطلاب)
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'formation_message_recipients')
            ->withPivot(['inscription_id', 'is_read', 'read_at'])
            ->withTimestamps();
    }

    /**
     * سجلات الاستلام التفصيلية
     */
    public function recipientRecords()
    {
        return $this->hasMany(FormationMessageRecipient::class);
    }

    // دوال مساعدة

    /**
     * إرسال الرسالة للطلاب المسجلين في التكوينات المحددة
     */
    public function sendToFormations(array $formationIds)
    {
        DB::beginTransaction();
        try {
            // ربط الرسالة بالتكوينات
            $this->formations()->sync($formationIds);

            // جلب جميع الطلاب المسجلين في هذه التكوينات
            $inscriptions = Inscription::whereIn('formation_id', $formationIds)
                ->whereIn('status', ['active', 'pending', 'completed']) 
                ->with('user:id,email') // 💡 Optimisation: charger seulement l'email
                ->get();

            $recipientsData = [];
            $uniqueUsers = [];
            $studentEmails = []; // 💡 Tableau pour stocker les emails uniques

            foreach ($inscriptions as $inscription) {
                $userId = $inscription->user_id;
                
                // تجنب إرسال رسالة مكررة لنفس الطالب إذا كان مسجل في أكثر من تكوين
                if (!in_array($userId, $uniqueUsers)) {
                    $uniqueUsers[] = $userId;
                    
                    $recipientsData[] = [
                        'formation_message_id' => $this->id,
                        'user_id' => $userId,
                        'inscription_id' => $inscription->id,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    // 💡 Stocker l'email de l'étudiant
                    if ($inscription->user && $inscription->user->email) {
                        $studentEmails[] = $inscription->user->email;
                    }
                }
            }

            // إدراج المستلمين دفعة واحدة (أسرع)
            if (!empty($recipientsData)) {
                DB::table('formation_message_recipients')->insert($recipientsData);
            }

            // تحديث حالة الرسالة وعدد المستلمين
            $this->update([
                'status' => 'sent',
                'sent_at' => now(),
                'recipients_count' => count($uniqueUsers),
            ]);

            DB::commit();

            // 💡 ÉTAPE CRUCIALE : ENVOI DU MAIL
            if (!empty($studentEmails)) {
                 // Utiliser Mail::bcc pour envoyer à tous en une seule fois (meilleur pour les listes)
                 // Assurez-vous d'avoir créé le Mailable 'NewFormationMessage' (voir les étapes ci-dessous)
                 Mail::bcc($studentEmails)
                      ->send(new NewFormationMessage($this));
            }

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error sending formation message: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * عدد الرسائل المقروءة
     */
    public function getReadCountAttribute()
    {
        return $this->recipientRecords()->where('is_read', true)->count();
    }

    /**
     * عدد الرسائل غير المقروءة
     */
    public function getUnreadCountAttribute()
    {
        return $this->recipientRecords()->where('is_read', false)->count();
    }

    /**
     * نسبة القراءة
     */
    public function getReadPercentageAttribute()
    {
        if ($this->recipients_count == 0) {
            return 0;
        }
        return round(($this->read_count / $this->recipients_count) * 100, 2);
    }
}