<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'amount',
        'currency',
        'type',
        'status',
        'payment_method',
        'sepay_data',
        'description',
        'reference_code',
        'processed_at',
        'reference',
        'processed'
    ];

    protected $casts = [
        'sepay_data' => 'array',
        'processed_at' => 'datetime',
    ];

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->processed = true;
        $this->completed_at = now();
        $this->save();
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}