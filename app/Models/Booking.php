<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'customer_id',
        'booking_type',
        'item_id',
        'item_name',
        'base_price',
        'surcharge',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'booking_status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'passport_number',
        'passport_expiry',
        'dob',
        'nationality',
        'destination_country',
        'visa_type',
        'travel_date',
        'booking_date',
        'additional_data',
        'discount_amount',
        'discount_percent',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'travel_date' => 'date',
        'passport_expiry' => 'date',
        'dob' => 'date',
        'additional_data' => 'array',
        'base_price' => 'decimal:2',
        'surcharge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    protected $appends = [
        'ref',
        'type',
        'name',
        'total',
        'status',
        'full_name',
    ];

    public function getRefAttribute()
    {
        return $this->booking_reference;
    }

    public function getTypeAttribute()
    {
        return $this->booking_type;
    }

    public function getNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function getStatusAttribute()
    {
        return $this->booking_status;
    }

    public function getFullNameAttribute()
    {
        return $this->getNameAttribute();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
