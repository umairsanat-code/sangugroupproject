<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialLink;

class SocialLinkController extends Controller
{
    public function record(): SocialLink
    {
        return SocialLink::firstOrCreate(
            ['id' => 1],
            ['facebook' => 'https://www.facebook.com/profile.php?id=61593463514392']
        );
    }

    // Public — social links shown in the website footer
    public function index()
    {
        return response()->json($this->record());
    }

    // Admin — load current social links
    public function adminGet()
    {
        return response()->json($this->record());
    }

    // Admin — save social links
    public function adminSave(Request $request)
    {
        $validated = $request->validate([
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
        ]);

        $settings = $this->record();
        $settings->update($validated);

        return response()->json(['message' => 'Social links saved successfully', 'social_links' => $settings]);
    }
}