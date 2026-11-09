<?php

namespace App\Http\Controllers;

use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisaController extends Controller
{
    public function index()
    {
        return response()->json(Visa::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country'    => 'required|string',
            'type'       => 'required|string',
            'title'      => 'nullable|string',
            'image_url'  => 'nullable|string',
            'requirements'=> 'nullable|array',
            'documents_required' => 'nullable|array',
            'checkout_settings' => 'nullable|array',
            'important_info' => 'nullable|string',
            'flag'       => 'nullable|string',
            'duration'   => 'nullable|string',
            'entry'      => 'nullable|string',
            'validity'   => 'nullable|string',
            'processing' => 'nullable|string',
            'price'      => 'required|numeric',
            'desc'       => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        // Auto-generate slug
        $data['slug'] = Str::slug($data['country'] . '-' . $data['type']) . '-' . Str::random(4);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('visas', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $visa = Visa::create($data);
        return response()->json($visa, 201);
    }

    public function show($slug)
    {
        $visa = Visa::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        return response()->json($visa);
    }

    public function update(Request $request, $id)
    {
        $visa = Visa::findOrFail($id);

        $data = $request->validate([
            'country'    => 'required|string',
            'type'       => 'required|string',
            'title'      => 'nullable|string',
            'image_url'  => 'nullable|string',
            'requirements'=> 'nullable|array',
            'documents_required' => 'nullable|array',
            'checkout_settings' => 'nullable|array',
            'important_info' => 'nullable|string',
            'flag'       => 'nullable|string',
            'duration'   => 'nullable|string',
            'entry'      => 'nullable|string',
            'validity'   => 'nullable|string',
            'processing' => 'nullable|string',
            'price'      => 'required|numeric',
            'desc'       => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('visas', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $visa->update($data);
        return response()->json($visa);
    }

    public function destroy($id)
    {
        $visa = Visa::findOrFail($id);
        $visa->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
