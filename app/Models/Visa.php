<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visa extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'slug', 'country', 'type', 'flag', 'duration', 'entry', 'validity',
        'processing', 'price', 'desc', 'docs', 'title', 'image_url', 'requirements', 'documents_required', 'checkout_settings', 'important_info', 'discount_amount', 'discount_percent', 'discount_label'
    ];

    protected $casts = [
        'docs' => 'array',
        'requirements' => 'array',
        'documents_required' => 'array',
        'checkout_settings' => 'array',
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
