<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'passport_number' => 'nullable|string',
            'passport_expiry' => 'nullable|date',
        ]);
        return response()->json(Customer::create($data));
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'passport_number' => 'nullable|string',
            'passport_expiry' => 'nullable|date',
        ]);
        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
