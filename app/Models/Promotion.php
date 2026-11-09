<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title', 'description', 'button_text', 'button_url',
        'image_url', 'is_active', 'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
