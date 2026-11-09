<?php

namespace App\Http\Controllers;

use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UmrahPackageController extends Controller
{
    public function index()
    {
        return response()->json(UmrahPackage::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string',
            'nights'      => 'nullable|string',
            'city'        => 'nullable|string',
            'hotel'       => 'nullable|string',
            'price'       => 'required|numeric',
            'rating'      => 'nullable|numeric',
            'group'       => 'nullable|boolean',
            'desc'        => 'nullable|string',
            'duration_days' => 'nullable|integer',
            'includes'    => 'nullable|array',
            'duration_type' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'makkah_hotel'=> 'nullable|string',
            'madinah_hotel'=> 'nullable|string',
            'transport'   => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        // Auto-generate slug
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(4);
        if (!isset($data['duration_days'])) {
            $data['duration_days'] = 10;
        }
        if (!isset($data['city'])) {
            $data['city'] = 'Makkah & Madinah';
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('umrah_packages', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $package = UmrahPackage::create($data);
        return response()->json($package, 201);
    }

    public function show($id)
    {
        $package = UmrahPackage::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        return response()->json($package);
    }

    public function update(Request $request, UmrahPackage $umrahPackage)
    {
        $data = $request->validate([
            'title'       => 'required|string',
            'nights'      => 'nullable|string',
            'city'        => 'nullable|string',
            'hotel'       => 'nullable|string',
            'price'       => 'required|numeric',
            'rating'      => 'nullable|numeric',
            'group'       => 'nullable|boolean',
            'desc'        => 'nullable|string',
            'duration_days' => 'nullable|integer',
            'includes'    => 'nullable|array',
            'duration_type' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'makkah_hotel'=> 'nullable|string',
            'madinah_hotel'=> 'nullable|string',
            'transport'   => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('umrah_packages', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $umrahPackage->update($data);
        return response()->json($umrahPackage);
    }

    public function destroy($id)
    {
        $pkg = UmrahPackage::findOrFail($id);
        $pkg->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
