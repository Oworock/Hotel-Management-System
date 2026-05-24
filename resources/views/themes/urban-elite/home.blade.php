@extends('layouts.frontend')

@section('title', 'Home')

@section('styles')
<style>
    .theme-urban { background:#eef1f5; color:#111827; }
    .theme-urban .wrap { width:min(1220px,100%); margin:0 auto; padding:0 1.25rem; }
    .urban-hero { min-height:82vh; display:grid; grid-template-columns:92px 1fr; background:#111827; color:#fff; }
    .urban-rail { border-right:1px solid rgba(255,255,255,.12); display:grid; place-items:center; color:#f59e0b; writing-mode:vertical-rl; text-transform:uppercase; letter-spacing:.14em; font-weight:900; }
    .urban-stage { position:relative; display:grid; align-items:end; padding:5rem 0; overflow:hidden; }
    .urban-stage:before { content:""; position:absolute; inset:0; background:linear-gradient(90deg,rgba(17,24,39,.88),rgba(17,24,39,.35)),url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80') center/cover; }
    .urban-copy { position:relative; z-index:1; max-width:820px; padding-left:clamp(1.25rem,5vw,5rem); }
    .urban-copy h1 { color:#fff; font-size:clamp(3rem,7vw,7rem); line-height:.9; margin:0 0 1rem; }
    .urban-copy p { color:rgba(255,255,255,.78); max-width:640px; line-height:1.75; font-size:1.1rem; }
    .urban-booking { margin-top:-3rem; position:relative; z-index:2; }
    .urban-booking form { width:min(1060px,100%); margin:0 auto; display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:#cfd5dd; border:1px solid #cfd5dd; }
    .urban-booking label { display:block; font-size:.75rem; font-weight:900; text-transform:uppercase; color:#6b7280; margin-bottom:.35rem; }
    .urban-booking div { background:#fff; padding:1rem; }
    .urban-booking input,.urban-booking select { width:100%; border:0; background:#f8fafc; min-height:42px; padding:0 .75rem; color:#111827; }
    .urban-booking button,.urban-btn { border:0; background:#f59e0b; color:#111827; font-weight:900; min-height:42px; padding:0 1rem; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .urban-main { padding:5rem 0; }
    .urban-grid { display:grid; grid-template-columns:360px 1fr; gap:1.25rem; align-items:start; }
    .urban-brief { background:#111827; color:#fff; padding:1.4rem; position:sticky; top:6rem; }
    .urban-brief h2 { color:#fff; font-size:clamp(2rem,4vw,3.8rem); line-height:.95; margin:0 0 1rem; }
    .urban-brief p { color:rgba(255,255,255,.72); line-height:1.7; }
    .urban-room-table { display:grid; gap:.75rem; }
    .urban-room { display:grid; grid-template-columns:170px 1fr 130px; gap:1rem; align-items:center; background:#fff; border:1px solid #d9dee7; padding:.8rem; }
    .urban-room-image { height:116px; background-size:cover; background-position:center; }
    .urban-room h3 { margin:0 0 .35rem; color:#111827; }
    .urban-room p { color:#667085; line-height:1.55; margin:0; }
    .urban-price { color:#111827; font-size:1.45rem; font-weight:900; text-align:right; }
    [data-theme="dark"] .theme-urban { background:#0b1220; color:#e5e7eb; }
    [data-theme="dark"] .urban-booking div,[data-theme="dark"] .urban-room { background:#111827; border-color:rgba(255,255,255,.12); }
    [data-theme="dark"] .urban-room h3,[data-theme="dark"] .urban-price { color:#f9fafb; }
    [data-theme="dark"] .urban-room p { color:#cbd5e1; }
    @media(max-width:900px){.urban-hero,.urban-grid,.urban-booking form,.urban-room{grid-template-columns:1fr}.urban-rail{display:none}.urban-brief{position:static}.urban-price{text-align:left}}
</style>
@endsection

@section('content')
<div class="theme-urban">
    <section class="urban-hero">
        <aside class="urban-rail">{{ $hotelName }}</aside>
        <div class="urban-stage">
            <div class="urban-copy">
                <h1>{{ $heroTitle }}</h1>
                <p>{{ $heroSubtitle }}</p>
                <a class="urban-btn" href="{{ route('rooms') }}">Check Rooms</a>
            </div>
        </div>
    </section>
    <div class="urban-booking"><form method="GET" action="{{ route('home') }}">
        <div><label>Check-in</label><input type="date" name="check_in_date" value="{{ $checkIn }}"></div>
        <div><label>Check-out</label><input type="date" name="check_out_date" value="{{ $checkOut }}"></div>
        <div><label>Guests</label><select name="guests">@for($i=1;$i<=6;$i++)<option value="{{ $i }}" {{ (int)$guestsCount===$i?'selected':'' }}>{{ $i }} Guest{{ $i>1?'s':'' }}</option>@endfor</select></div>
        <div style="display:grid;align-items:end;"><button type="submit">Search</button></div>
    </form></div>
    <section class="urban-main"><div class="wrap urban-grid">
        <aside class="urban-brief"><h2>{{ $welcomeTitle }}</h2><p>{{ $welcomeDescription }}</p></aside>
        <div class="urban-room-table">
            @forelse($roomTypes as $roomType)
                @php $roomImage=collect($roomType->images ?? [])->first(); $roomImageUrl=$roomImage ? (\Illuminate\Support\Str::startsWith($roomImage,['http://','https://','/'])?$roomImage:asset('storage/'.$roomImage)) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=900&q=80'; @endphp
                <article class="urban-room"><div class="urban-room-image" style="background-image:url('{{ $roomImageUrl }}')"></div><div><h3>{{ $roomType->name }}</h3><p>{{ \Illuminate\Support\Str::limit($roomType->description, 120) }}</p></div><div class="urban-price">{{ $currency }}{{ number_format((float)($roomType->base_price ?? $roomType->price ?? 0),0) }}</div></article>
            @empty <p>No rooms available at the moment.</p> @endforelse
        </div>
    </div></section>
</div>
@endsection
