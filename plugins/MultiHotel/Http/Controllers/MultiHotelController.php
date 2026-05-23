<?php

namespace Plugins\MultiHotel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\MultiHotel\Models\Hotel;

class MultiHotelController extends Controller
{
    /**
     * Display a listing of the hotels.
     */
    public function index()
    {
        $hotels = Hotel::all();
        return view('multi_hotel.index', compact('hotels'));
    }

    /**
     * Store a newly created hotel in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
        ]);

        Hotel::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('super_admin.hotels')->with('success', 'Hotel created successfully.');
    }

    /**
     * Update the specified hotel in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        $hotel->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $hotel->is_active,
        ]);

        return redirect()->route('super_admin.hotels')->with('success', 'Hotel updated successfully.');
    }

    /**
     * Switch active hotel context in session.
     */
    public function select(Request $request)
    {
        $hotelId = $request->input('hotel_id');

        if (empty($hotelId)) {
            session()->forget('active_hotel_id');
        } else {
            session(['active_hotel_id' => (int) $hotelId]);
        }

        return redirect()->back()->with('success', 'Active hotel context updated.');
    }
}
