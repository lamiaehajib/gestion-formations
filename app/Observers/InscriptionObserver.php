<?php

namespace App\Observers;

use App\Models\Inscription;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Log;

class InscriptionObserver
{
    /**
     * Handle the Inscription "created" event.
     * ✅ Automatically assign student to promotion when inscription is created.
     */
    public function created(Inscription $inscription)
    {
        try {
            PromotionController::autoAssignStudentToPromotion($inscription);
        } catch (\Exception $e) {
            Log::error("Error auto-assigning student to promotion: " . $e->getMessage());
        }
    }

    /**
     * Handle the Inscription "updated" event.
     * ✅ Remove student from promotion if inscription is cancelled.
     */
    public function updated(Inscription $inscription)
    {
        // If inscription status changed to cancelled
        if ($inscription->status === 'cancelled' && $inscription->user) {
            try {
                PromotionController::autoRemoveStudentFromPromotion($inscription->user);
            } catch (\Exception $e) {
                Log::error("Error removing student from promotion: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Inscription "deleted" event.
     * ✅ Remove student from promotion if inscription is deleted.
     */
    public function deleted(Inscription $inscription)
    {
        if ($inscription->user) {
            try {
                PromotionController::autoRemoveStudentFromPromotion($inscription->user);
            } catch (\Exception $e) {
                Log::error("Error removing student from promotion on delete: " . $e->getMessage());
            }
        }
    }
}