<?php

namespace App\Observers;

use App\Models\Inscription;
use App\Models\Promotion;
use Illuminate\Support\Facades\Log;

class InscriptionObserver
{
    /**
     * Handle the Inscription "created" event.
     */
    public function created(Inscription $inscription)
    {
        $this->assignToPromotion($inscription);
    }

    /**
     * Handle the Inscription "updated" event.
     */
    public function updated(Inscription $inscription)
    {
        // Si le statut change ou la formation change
        if ($inscription->isDirty('status') || $inscription->isDirty('formation_id')) {
            $this->assignToPromotion($inscription);
        }
    }

    /**
     * Assign user to the appropriate promotion
     */
    private function assignToPromotion(Inscription $inscription)
    {
        // Skip if inscription is cancelled
        if ($inscription->status === 'cancelled') {
            return;
        }

        // Find the most recent promotion for this formation
        $promotion = Promotion::where('formation_id', $inscription->formation_id)
            ->orderBy('year', 'desc')
            ->first();

        if ($promotion && $inscription->user) {
            // Update user's promotion_id
            $inscription->user->update(['promotion_id' => $promotion->id]);
            
            Log::info("Student {$inscription->user->id} auto-assigned to promotion {$promotion->id}");
        }
    }
}