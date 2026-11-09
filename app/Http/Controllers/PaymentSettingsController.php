<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSettings;

class PaymentSettingsController extends Controller
{
    public function record(): PaymentSettings
    {
        return PaymentSettings::firstOrCreate(
            ['id' => 1],
            ['method' => 'revolut', 'currency' => 'EUR']
        );
    }

    // Public — shown to clients so they can pay directly to the Revolut account
    public function index()
    {
        return response()->json($this->record());
    }

    // Admin — load current settings
    public function adminGet()
    {
        return response()->json($this->record());
    }

    // Admin — save real payment / Revolut account details
    public function adminSave(Request $request)
    {
        $validated = $request->validate([
            'account_holder' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'bic_swift' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'payment_link' => 'nullable|string|max:255',
            'instructions' => 'nullable|string'
        ]);

        $settings = $this->record();
        $settings->update($validated);

        return response()->json(['message' => 'Payment settings saved successfully', 'payment' => $settings]);
    }
}