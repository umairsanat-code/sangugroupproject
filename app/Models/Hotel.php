<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'slug', 'name', 'city', 'stars', 'distance', 'price', 'rating', 'desc', 'amenities', 'image_url', 'gallery', 'room_types', 'discount_amount', 'discount_percent', 'discount_label'
    ];

    protected $casts = [
        'amenities' => 'array',
        'room_types' => 'array',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    protected $appends = ['discounted_price', 'savings'];

    public function getDiscountedPriceAttribute() {
        $price = $this->price;
        if ($this->discount_amount > 0) return max(0, $price - $this->discount_amount);
        if ($this->discount_percent > 0) return max(0, $price - ($price * $this->discount_percent / 100));
        return $price;
    }

    public function getSavingsAttribute() {
        return $this->price - $this->discounted_price;
    }

    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function rooms() { return $this->hasMany(HotelRoom::class); }
}
