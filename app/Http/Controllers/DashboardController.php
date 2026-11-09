<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'total_customers' => Customer::count(),
            'total_revenue' => Payment::where('type', 'Customer Payment')->sum('amount'),
            'total_bookings' => Booking::count(),
            'tickets_available' => Ticket::where('status', 'Available')->count(),
            'tickets_booked' => Ticket::where('status', 'Booked')->count(),
            'recent_bookings' => Booking::with('customer')->orderBy('id', 'desc')->take(5)->get()
        ]);
    }
}
