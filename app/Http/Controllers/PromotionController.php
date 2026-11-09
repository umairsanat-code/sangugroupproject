<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        // Frontend: only active, sorted by display_order then latest
        $active = request()->query('all');
        if ($active) {
            return response()->json(Promotion::orderBy('display_order')->orderBy('created_at', 'desc')->get());
        }
        return response()->json(Promotion::where('is_active', true)->orderBy('display_order')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'button_text'   => 'nullable|string|max:100',
            'button_url'    => 'nullable|string|max:500',
            'is_active'     => 'nullable|boolean',
            'display_order' => 'nullable|integer',
            'image'         => 'nullable|image|max:5120',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('promotions', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $promotion = Promotion::create([
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'button_text'   => $data['button_text'] ?? 'Learn More',
            'button_url'    => $data['button_url'] ?? null,
            'is_active'     => $data['is_active'] ?? true,
            'display_order' => $data['display_order'] ?? 0,
            'image_url'     => $imageUrl,
        ]);

        return response()->json(['success' => true, 'promotion' => $promotion], 201);
    }

    public function update(Request $request, $id)
    {
        $promo = Promotion::findOrFail($id);

        $data = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'description'   => 'nullable|string',
            'button_text'   => 'nullable|string|max:100',
            'button_url'    => 'nullable|string|max:500',
            'is_active'     => 'nullable|boolean',
            'display_order' => 'nullable|integer',
            'image'         => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($promo->image_url) {
                $oldPath = str_replace('/storage/', '', $promo->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('promotions', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        unset($data['image']);
        $promo->update($data);

        return response()->json(['success' => true, 'promotion' => $promo->fresh()]);
    }

    public function toggleActive(Request $request, $id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();
        return response()->json(['success' => true, 'is_active' => $promo->is_active]);
    }

    public function destroy($id)
    {
        $promo = Promotion::findOrFail($id);
        if ($promo->image_url) {
            $path = str_replace('/storage/', '', $promo->image_url);
            Storage::disk('public')->delete($path);
        }
        $promo->delete();
        return response()->json(['success' => true]);
    }
}
