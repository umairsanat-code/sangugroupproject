<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Services\FlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    protected $flightService;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    public function index()
    {
        return response()->json(Flight::with('airline', 'departureAirport', 'arrivalAirport')->orderBy('id', 'desc')->get());
    }

    public function search(Request $request)
    {
        $params = $request->only(['from', 'to', 'date', 'adults']);
        $results = $this->flightService->searchFlights($params);
        
        return response()->json($results);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'flight_number' => 'required|string',
            'departure_city' => 'nullable|string',
            'arrival_city' => 'nullable|string',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
        ]);
        return response()->json(Flight::create($data));
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
