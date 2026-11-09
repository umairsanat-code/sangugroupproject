<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmrahGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'departure_date', 'return_date', 'image_url', 'seats', 'price', 'hotel', 'transport', 'group_details', 'discount_amount', 'discount_percent', 'discount_label'
    ];

    protected $casts = [
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
