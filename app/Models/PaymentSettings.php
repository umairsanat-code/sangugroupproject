<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = [
        'method',
        'account_holder',
        'account_number',
        'sort_code',
        'iban',
        'bic_swift',
        'currency',
        'payment_link',
        'instructions',
    ];
}