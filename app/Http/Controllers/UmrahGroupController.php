<?php

namespace App\Http\Controllers;

use App\Models\UmrahGroup;
use Illuminate\Http\Request;

class UmrahGroupController extends Controller
{
    public function index()
    {
        return response()->json(UmrahGroup::orderBy('departure_date', 'asc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string',
            'departure_date' => 'nullable|date',
            'return_date'    => 'nullable|date',
            'image_url'      => 'nullable|string',
            'seats'          => 'nullable|integer',
            'price'          => 'nullable|numeric',
            'hotel'          => 'nullable|string',
            'transport'      => 'nullable|string',
            'group_details'  => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('umrah_groups', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $group = UmrahGroup::create($data);
        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = UmrahGroup::findOrFail($id);
        return response()->json($group);
    }

    public function update(Request $request, UmrahGroup $umrahGroup)
    {
        $data = $request->validate([
            'name'           => 'sometimes|required|string',
            'departure_date' => 'nullable|date',
            'return_date'    => 'nullable|date',
            'image_url'      => 'nullable|string',
            'seats'          => 'nullable|integer',
            'price'          => 'nullable|numeric',
            'hotel'          => 'nullable|string',
            'transport'      => 'nullable|string',
            'group_details'  => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_label' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('umrah_groups', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $umrahGroup->update($data);
        return response()->json($umrahGroup);
    }

    public function destroy($id)
    {
        $group = UmrahGroup::findOrFail($id);
        $group->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
