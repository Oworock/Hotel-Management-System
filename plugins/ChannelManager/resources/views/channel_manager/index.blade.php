@extends('layouts.app')

@section('title', 'Channel Manager (OTA Sync)')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 2rem;">
    <!-- Dashboard Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary);"></i> Channel Manager Integration
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0.25rem 0 0 0;">Synchronize room inventory, pricing, and bookings in real-time with Booking.com, Airbnb, and Expedia.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session()->has('success'))
        <div class="glass-panel" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid var(--success); color: var(--success); padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--radius-sm); font-weight: 500;">
            <i class="fa-solid fa-circle-check" style="margin-right: 0.5rem;"></i> {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="glass-panel" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); color: var(--danger); padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--radius-sm); font-weight: 500;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Part 1: Channel Connection Settings & Manual Sync -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @foreach($otaChannels as $channel)
            <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: space-between; border-color: {{ $channel->is_connected ? 'var(--success)' : 'var(--border-color)' }}; transition: border-color 0.3s ease; position: relative; overflow: hidden; padding: 1.5rem;">
                @if($channel->is_connected)
                    <div style="position: absolute; top: 0; right: 0; background: var(--success); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 0.25rem 0.75rem; border-bottom-left-radius: 6px; text-transform: uppercase;">
                        Connected
                    </div>
                @endif

                <div>
                    <!-- Channel Title -->
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        @switch($channel->name)
                            @case('Booking.com')
                                <i class="fa-solid fa-square-envelope" style="font-size: 1.75rem; color: #003580;"></i>
                                @break
                            @case('Airbnb')
                                <i class="fa-brands fa-airbnb" style="font-size: 1.75rem; color: #FF5A5F;"></i>
                                @break
                            @case('Expedia')
                                <i class="fa-solid fa-plane-departure" style="font-size: 1.75rem; color: #FFC000;"></i>
                                @break
                            @default
                                <i class="fa-solid fa-circle-nodes" style="font-size: 1.75rem; color: var(--primary);"></i>
                        @endswitch
                        <div>
                            <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $channel->name }}</h3>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">
                                {{ $channel->is_connected ? 'Syncing active' : 'Credentials required' }}
                            </span>
                        </div>
                    </div>

                    <!-- Configuration Form -->
                    <form action="{{ route('admin.channel_manager.settings') }}" method="POST" style="margin-bottom: 1rem;">
                        @csrf
                        <input type="hidden" name="channel_id" value="{{ $channel->id }}">
                        
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">Hotel ID / Property Key</label>
                                <input type="text" name="hotel_id" value="{{ old('hotel_id', $channel->hotel_id) }}" class="input-field" placeholder="e.g. PROP-12345" style="width: 100%; font-size: 0.85rem; padding: 0.4rem 0.75rem;">
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">API Key</label>
                                <input type="password" name="api_key" value="{{ old('api_key', $channel->api_key ? '********' : '') }}" class="input-field" placeholder="Secret Token" style="width: 100%; font-size: 0.85rem; padding: 0.4rem 0.75rem;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">API Secret</label>
                                <input type="password" name="api_secret" value="{{ old('api_secret', $channel->api_secret ? '********' : '') }}" class="input-field" placeholder="Secret Passphrase" style="width: 100%; font-size: 0.85rem; padding: 0.4rem 0.75rem;">
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                                <input type="checkbox" name="is_connected" value="1" id="connect_{{ $channel->id }}" {{ $channel->is_connected ? 'checked' : '' }} style="cursor: pointer;">
                                <label for="connect_{{ $channel->id }}" style="font-size: 0.8rem; font-weight: 600; color: var(--text-primary); cursor: pointer;">Enable connection & sync</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-outline btn-block" style="margin-top: 1rem; font-size: 0.8rem;">
                            <i class="fa-solid fa-floppy-disk"></i> Save Settings
                        </button>
                    </form>
                </div>

                <!-- Sync Control Section -->
                @if($channel->is_connected)
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 0.5rem;">
                        <form action="{{ route('admin.channel_manager.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="channel_name" value="{{ $channel->name }}">
                            
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <select name="sync_type" class="input-field" style="flex: 1; padding: 0.35rem 0.5rem; font-size: 0.8rem;">
                                    <option value="all">Full Sync (Push & Pull)</option>
                                    <option value="push">Push Room Availability</option>
                                    <option value="pull">Pull Guests Reservations</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.45rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="fa-solid fa-rotate"></i> Sync
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Part 2: Room Mappings Section -->
    <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
        
        <!-- Add Room Mapping Form -->
        <div class="glass-panel" style="padding: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-link" style="color: var(--primary);"></i> Map Room Type
            </h3>
            
            <form action="{{ route('admin.channel_manager.mappings.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">stayFlow Room Type</label>
                        <select name="room_type_id" class="input-field" style="width: 100%; padding: 0.5rem;" required>
                            <option value="">Select Room Type...</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} (Base: {{ $currency }} {{ number_format($type->base_price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">OTA Channel Name</label>
                        <select name="channel_name" class="input-field" style="width: 100%; padding: 0.5rem;" required>
                            <option value="">Select Channel...</option>
                            <option value="Booking.com">Booking.com</option>
                            <option value="Airbnb">Airbnb</option>
                            <option value="Expedia">Expedia</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">OTA Listing / Room ID</label>
                        <input type="text" name="ota_room_id" class="input-field" placeholder="e.g. room_deluxe_101" style="width: 100%; padding: 0.5rem;" required>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">
                            Rate Multiplier 
                            <span style="font-weight: 400; font-size: 0.7rem; color: var(--text-secondary);">(for markup or markdown rates, e.g. 1.10 = +10% price)</span>
                        </label>
                        <input type="number" step="0.01" name="rate_multiplier" value="1.00" min="0.50" max="3.00" class="input-field" style="width: 100%; padding: 0.5rem;" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 0.5rem; display: inline-flex; justify-content: center; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-plus"></i> Add Mapping Link
                    </button>
                </div>
            </form>
        </div>

        <!-- Mappings Table -->
        <div class="glass-panel" style="padding: 1.5rem; display: flex; flex-direction: column;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Mapped Room Connections
            </h3>
            
            <div class="table-container" style="flex: 1; border: none; margin: 0;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>stayFlow Room Type</th>
                            <th>OTA Channel</th>
                            <th>OTA Room ID</th>
                            <th>Multiplier</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $mapping)
                            <tr>
                                <td style="font-weight: 600;">{{ $mapping->roomType->name ?? 'Deleted Type' }}</td>
                                <td>
                                    @switch($mapping->channel_name)
                                        @case('Booking.com')
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-square-envelope" style="color: #003580;"></i> Booking.com</span>
                                            @break
                                        @case('Airbnb')
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem;"><i class="fa-brands fa-airbnb" style="color: #FF5A5F;"></i> Airbnb</span>
                                            @break
                                        @case('Expedia')
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-plane-departure" style="color: #FFC000;"></i> Expedia</span>
                                            @break
                                        @default
                                            {{ $mapping->channel_name }}
                                    @endswitch
                                </td>
                                <td><code style="font-size: 0.8rem; background: var(--primary-glow); padding: 0.15rem 0.35rem; border-radius: 4px; color: var(--primary);">{{ $mapping->ota_room_id }}</code></td>
                                <td>{{ number_format($mapping->rate_multiplier, 2) }}x</td>
                                <td style="text-align: right;">
                                    <form action="{{ route('admin.channel_manager.mappings.delete', $mapping->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this mapping connection?')" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--danger); color: var(--danger); font-size: 0.75rem; padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="fa-solid fa-trash"></i> Unlink
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">
                                    <i class="fa-solid fa-circle-info" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i> No active room mapping links found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Part 3: Sync Activity Logs -->
    <div class="glass-panel" style="padding: 1.5rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Sync Operations Log
        </h3>
        
        <div class="table-container" style="max-height: 350px; overflow-y: auto; border: none; margin: 0;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Channel</th>
                        <th>Sync Operation</th>
                        <th>Status</th>
                        <th>Detail Summary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">{{ $log->created_at->format('M d, H:i:s') }}</td>
                            <td style="font-weight: 600;">{{ $log->channel_name }}</td>
                            <td>
                                @switch($log->sync_type)
                                    @case('push_availability')
                                        <span class="badge badge-info" style="font-size: 0.75rem;">Push Availability</span>
                                        @break
                                    @case('pull_bookings')
                                        <span class="badge badge-success" style="font-size: 0.75rem;">Pull Bookings</span>
                                        @break
                                    @case('save_settings')
                                        <span class="badge badge-warning" style="font-size: 0.75rem; background-color: var(--primary-glow); color: var(--primary);">Config Saved</span>
                                        @break
                                    @case('delete_mapping')
                                        <span class="badge badge-danger" style="font-size: 0.75rem;">Mapping Deleted</span>
                                        @break
                                    @default
                                        <span class="badge" style="font-size: 0.75rem;">{{ $log->sync_type }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($log->status === 'success')
                                    <span style="color: var(--success); font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="fa-solid fa-circle-check"></i> Success
                                    </span>
                                @else
                                    <span style="color: var(--danger); font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="fa-solid fa-circle-xmark"></i> Failure
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-primary); max-width: 400px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $log->message }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">
                                No synchronization activity logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
