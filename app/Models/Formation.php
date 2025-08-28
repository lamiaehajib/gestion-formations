<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'duration_hours',
        
        'capacity',
        'status',
        'start_date',
        'end_date',
        'category_id',
        'consultant_id',
        'prerequisites',
        'documents_required',
        'available_payment_options', 
        'duration_unit',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'prerequisites' => 'array', // Laravel will automatically decode JSON from DB to array
        'documents_required' => 'array', // Laravel will automatically decode JSON from DB to array
        'available_payment_options' => 'array', // <<< CRITICAL FIX: ADD THIS CAST!
    ];

    // Optional: Set a default for new model instances before saving.
    // This is useful if you want `[1]` even if the form submits an empty array or no selection.
    // If you always require at least one option to be selected in the form,
    // and your form's default ensures '1' is always selected if nothing else is,
    // you might not strictly need this 'attributes' property.
    protected $attributes = [
        'available_payment_options' => '[]', // Or '[1]' if you want default to be full payment
    ];

    // REMOVE THIS ACCESSOR: It's no longer needed if `available_payment_options` is in `$casts`.
    // The cast handles automatic decoding from JSON string to PHP array.
    /*
    public function getAvailablePaymentOptionsAttribute($value)
    {
        // This logic is now handled by the 'array' cast.
        // If the value from DB is null, the 'array' cast would convert it to null.
        // If you need a fallback to [1] for null values after casting, you can do:
        // return $value ?? [1];
        // But typically, the 'default' in protected $attributes or migration handles this.
        return $value; // Or $value ?? [1]; if you want a default PHP array for null DB entries
    }
    */

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function documents()
    {
        // Assuming this is a hasMany relationship to a 'documents' table,
        // distinct from the 'documents_required' JSON field on this model.
        return $this->hasMany(Document::class);
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

     public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }
}