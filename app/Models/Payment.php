<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'amount',
        'payment_method',
        'status',
        'payment_date',
        'transaction_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
