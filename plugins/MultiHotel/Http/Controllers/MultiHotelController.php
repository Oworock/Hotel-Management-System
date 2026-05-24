<?php

namespace Plugins\MultiHotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Plugins\MultiHotel\Models\Hotel;

class MultiHotelController extends Controller
{
    /**
     * Display a listing of the hotels.
     */
    public function index()
    {
        $hotels = Hotel::query()
            ->withCount([
                'roomTypes',
                'rooms',
                'staff',
            ])
            ->orderBy('name')
            ->get();

        return view('multi_hotel.index', compact('hotels'));
    }

    public function manage(Hotel $hotel)
    {
        $this->ensureHotelColumnsExist();

        $roomTypes = RoomType::withoutGlobalScopes()
            ->where('hotel_id', $hotel->id)
            ->withCount(['rooms' => fn ($query) => $query->withoutGlobalScopes()->where('hotel_id', $hotel->id)])
            ->orderBy('name')
            ->get();

        $rooms = Room::withoutGlobalScopes()
            ->with('roomType')
            ->where('hotel_id', $hotel->id)
            ->orderBy('room_number')
            ->paginate(12);

        $staff = User::withoutGlobalScopes()
            ->where('hotel_id', $hotel->id)
            ->whereIn('role', $this->hotelStaffRoles())
            ->orderBy('name')
            ->get();

        $availableStaff = User::withoutGlobalScopes()
            ->whereIn('role', $this->hotelStaffRoles())
            ->where(function ($query) use ($hotel) {
                $query->whereNull('hotel_id')->orWhere('hotel_id', '!=', $hotel->id);
            })
            ->orderBy('name')
            ->get();

        return view('multi_hotel.manage', compact('hotel', 'roomTypes', 'rooms', 'staff', 'availableStaff'));
    }

    /**
     * Store a newly created hotel in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'map_embed_url' => 'nullable|string',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
        ]);

        Hotel::create([
            'name' => $request->name,
            'address' => $request->address,
            'map_embed_url' => $request->map_embed_url,
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
            'map_embed_url' => 'nullable|string',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        $hotel->update([
            'name' => $request->name,
            'address' => $request->address,
            'map_embed_url' => $request->map_embed_url,
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

    public function selectPublic(Request $request)
    {
        $request->validate([
            'hotel_id' => 'nullable|integer',
        ]);

        $hotelId = $request->input('hotel_id');

        if (empty($hotelId)) {
            session()->forget('active_hotel_id');

            return redirect()->back();
        }

        $hotel = Hotel::where('is_active', true)->findOrFail($hotelId);
        session(['active_hotel_id' => $hotel->id]);

        return redirect()->back();
    }

    public function storeRoomType(Request $request, Hotel $hotel)
    {
        $this->ensureHotelColumnsExist();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
            'image_url' => 'nullable|url|max:1000',
        ]);

        $roomType = new RoomType();
        $roomType->name = $validated['name'];
        $roomType->description = $validated['description'] ?? null;
        $roomType->base_price = $validated['base_price'];
        $roomType->capacity = $validated['capacity'];
        $roomType->amenities = $this->csvToArray($validated['amenities'] ?? '');
        $roomType->images = filled($validated['image_url'] ?? null) ? [$validated['image_url']] : [];
        $roomType->hotel_id = $hotel->id;
        $roomType->save();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Room type added to hotel successfully.');
    }

    public function storeRoom(Request $request, Hotel $hotel)
    {
        $this->ensureHotelColumnsExist();

        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', Rule::unique('rooms', 'room_number')],
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where(fn ($query) => $query->where('hotel_id', $hotel->id)),
            ],
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        $room = new Room();
        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'];
        $room->status = $validated['status'];
        $room->hotel_id = $hotel->id;
        $room->save();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Room added to hotel successfully.');
    }

    public function updateRoomStatus(Request $request, Hotel $hotel, $room)
    {
        $room = Room::withoutGlobalScopes()->findOrFail($room);
        $this->ensureHotelRecord($hotel, $room);

        $validated = $request->validate([
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        $room->status = $validated['status'];
        $room->save();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Room status updated successfully.');
    }

    public function deleteRoom(Hotel $hotel, $room)
    {
        $room = Room::withoutGlobalScopes()->findOrFail($room);
        $this->ensureHotelRecord($hotel, $room);
        $room->delete();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Room deleted successfully.');
    }

    public function assignStaff(Request $request, Hotel $hotel)
    {
        $this->ensureHotelColumnsExist();

        $validated = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', $this->hotelStaffRoles())),
            ],
        ]);

        $user = User::withoutGlobalScopes()->findOrFail($validated['user_id']);
        $user->hotel_id = $hotel->id;
        $user->save();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Staff member assigned to hotel successfully.');
    }

    public function removeStaff(Hotel $hotel, $user)
    {
        $user = User::withoutGlobalScopes()->findOrFail($user);
        $this->ensureHotelRecord($hotel, $user);
        $user->hotel_id = null;
        $user->save();

        return redirect()->route('super_admin.hotels.manage', $hotel)->with('success', 'Staff member removed from hotel successfully.');
    }

    protected function ensureHotelColumnsExist(): void
    {
        abort_unless(
            Schema::hasColumn('rooms', 'hotel_id')
            && Schema::hasColumn('room_types', 'hotel_id')
            && Schema::hasColumn('users', 'hotel_id'),
            503,
            'MultiHotel database columns are not migrated yet. Please run migrations after enabling the plugin.'
        );
    }

    protected function ensureHotelRecord(Hotel $hotel, object $record): void
    {
        $this->ensureHotelColumnsExist();
        abort_unless((int) ($record->hotel_id ?? 0) === (int) $hotel->id, 404);
    }

    protected function csvToArray(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    protected function hotelStaffRoles(): array
    {
        return ['admin', 'staff', 'receptionist', 'kitchen_manager', 'tuck_shop_manager', 'restaurant_manager'];
    }
}
