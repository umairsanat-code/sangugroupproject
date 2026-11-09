<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Destination::query();
        
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }
        
        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:destinations,slug',
            'country' => 'required|string',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);
        return response()->json(Destination::create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $destination = Destination::where('slug', $slug)->firstOrFail();
        return response()->json($destination);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'slug' => 'sometimes|required|string|unique:destinations,slug,' . $destination->id,
            'country' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);
        $destination->update($data);
        return response()->json($destination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
