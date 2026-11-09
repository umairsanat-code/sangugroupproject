<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    public function index()
    {
        return response()->json(Hotel::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string',
            'city'      => 'required|string',
            'stars'     => 'nullable|integer',
            'distance'  => 'nullable|string',
            'price'     => 'required|numeric',
            'rating'    => 'nullable|numeric',
            'desc'      => 'nullable|string',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|string',
            'gallery'   => 'nullable|array',
            'room_types'=> 'nullable|array',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        // Auto-generate slug
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        if (!isset($data['stars'])) {
            $data['stars'] = 3;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $hotel = Hotel::create($data);
        return response()->json($hotel, 201);
    }

    public function show($id)
    {
        $hotel = Hotel::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        return response()->json($hotel);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'name'  => 'sometimes|required|string',
            'city'  => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);
        $hotel->update($data);
        return response()->json($hotel);
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
