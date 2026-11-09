<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $all = request()->query('all');
        if ($all) {
            return response()->json(GalleryItem::orderBy('display_order')->orderBy('created_at', 'desc')->get());
        }
        return response()->json(GalleryItem::where('is_active', true)->orderBy('display_order')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
            'display_order' => 'nullable|integer',
            'image'         => 'required|image|max:8192',
        ]);

        $path = $request->file('image')->store('gallery', 'public');
        $imageUrl = '/storage/' . $path;

        $item = GalleryItem::create([
            'title'         => $data['title'] ?? null,
            'category'      => $data['category'] ?? 'General',
            'is_active'     => $data['is_active'] ?? true,
            'display_order' => $data['display_order'] ?? 0,
            'image_url'     => $imageUrl,
        ]);

        return response()->json(['success' => true, 'item' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $item = GalleryItem::findOrFail($id);

        $data = $request->validate([
            'title'         => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
            'display_order' => 'nullable|integer',
            'image'         => 'nullable|image|max:8192',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image_url) {
                $oldPath = str_replace('/storage/', '', $item->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('gallery', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        unset($data['image']);
        $item->update($data);

        return response()->json(['success' => true, 'item' => $item->fresh()]);
    }

    public function toggleActive($id)
    {
        $item = GalleryItem::findOrFail($id);
        $item->is_active = !$item->is_active;
        $item->save();
        return response()->json(['success' => true, 'is_active' => $item->is_active]);
    }

    public function destroy($id)
    {
        $item = GalleryItem::findOrFail($id);
        if ($item->image_url) {
            $path = str_replace('/storage/', '', $item->image_url);
            Storage::disk('public')->delete($path);
        }
        $item->delete();
        return response()->json(['success' => true]);
    }
}
