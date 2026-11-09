<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmrahPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'slug', 'title', 'duration_days', 'price', 'hotel_details', 'transport_details', 'visa_included', 'image_path', 'description', 'nights', 'city', 'hotel', 'rating', 'group', 'desc', 'includes', 'duration_type', 'image_url', 'makkah_hotel', 'madinah_hotel', 'transport', 'discount_amount', 'discount_percent', 'discount_label'
    ];

    protected $casts = [
        'includes' => 'array',
        'group' => 'boolean',
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
}
