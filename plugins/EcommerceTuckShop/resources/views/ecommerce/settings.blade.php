@extends('layouts.app')

@section('title', 'E-commerce Settings')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Title and Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-sliders" style="color: var(--primary); margin-right: 0.5rem;"></i> E-commerce Store Settings
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Configure custom shipping fees, taxing rules, and storefront status.</p>
        </div>
        <div>
            <a href="{{ route('admin.ecommerce.dashboard') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session()->has('success'))
        <div style="background-color: var(--success-glow); border: 1px solid var(--success); color: var(--success); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: var(--danger-glow); border: 1px solid var(--danger); color: var(--danger); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
            <p style="font-weight: 700; margin: 0 0 0.5rem 0;"><i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:</p>
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Panel -->
    <form action="{{ route('admin.ecommerce.settings.update') }}" method="POST" class="glass-panel" style="padding: 2rem; border-radius: var(--radius-md);">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Left Side: Basic Info -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 0; color: var(--text-primary);">
                    <i class="fa-solid fa-store" style="color: var(--primary); margin-right: 0.5rem;"></i> Store Identity
                </h3>

                <!-- Store Name -->
                <div class="form-group">
                    <label for="ecommerce_store_name" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Store Name</label>
                    <input type="text" id="ecommerce_store_name" name="ecommerce_store_name" class="form-control" value="{{ old('ecommerce_store_name', $settings['ecommerce_store_name']) }}" required placeholder="e.g. StayFlow Tuck Shop">
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Used as storefront banner title.</small>
                </div>

                <!-- Store Email -->
                <div class="form-group">
                    <label for="ecommerce_store_email" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Store Support Email</label>
                    <input type="email" id="ecommerce_store_email" name="ecommerce_store_email" class="form-control" value="{{ old('ecommerce_store_email', $settings['ecommerce_store_email']) }}" required placeholder="e.g. shop@stayflow.com">
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Contact email displayed on invoices.</small>
                </div>

                <!-- Store Status Toggle -->
                <div class="form-group">
                    <label for="ecommerce_store_status" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Operational Status</label>
                    <select id="ecommerce_store_status" name="ecommerce_store_status" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary);">
                        <option value="open" {{ old('ecommerce_store_status', $settings['ecommerce_store_status']) === 'open' ? 'selected' : '' }}>Open (Accepting Orders)</option>
                        <option value="closed" {{ old('ecommerce_store_status', $settings['ecommerce_store_status']) === 'closed' ? 'selected' : '' }}>Closed (Offline / Maintenance)</option>
                    </select>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Turn storefront catalog sales on or off instantly.</small>
                </div>
            </div>

            <!-- Right Side: Shipping & Taxes -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 0; color: var(--text-primary);">
                    <i class="fa-solid fa-truck" style="color: var(--primary); margin-right: 0.5rem;"></i> Financial Configurations
                </h3>

                <!-- Shipping Fee -->
                <div class="form-group">
                    <label for="ecommerce_shipping_fee" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Base Shipping / Delivery Fee</label>
                    <div style="display: flex; align-items: center; position: relative;">
                        <span style="position: absolute; left: 12px; font-weight: 600; color: var(--text-secondary);">$</span>
                        <input type="number" step="0.01" min="0" id="ecommerce_shipping_fee" name="ecommerce_shipping_fee" class="form-control" value="{{ old('ecommerce_shipping_fee', $settings['ecommerce_shipping_fee']) }}" required style="padding-left: 2rem;">
                    </div>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Standard flat rate added to delivery orders.</small>
                </div>

                <!-- Free Shipping Limit -->
                <div class="form-group">
                    <label for="ecommerce_free_shipping_limit" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Free Shipping Threshold</label>
                    <div style="display: flex; align-items: center; position: relative;">
                        <span style="position: absolute; left: 12px; font-weight: 600; color: var(--text-secondary);">$</span>
                        <input type="number" step="0.01" min="0" id="ecommerce_free_shipping_limit" name="ecommerce_free_shipping_limit" class="form-control" value="{{ old('ecommerce_free_shipping_limit', $settings['ecommerce_free_shipping_limit']) }}" required style="padding-left: 2rem;">
                    </div>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Set shipping fee to $0 when subtotal reaches this amount.</small>
                </div>

                <!-- Tax Rate -->
                <div class="form-group">
                    <label for="ecommerce_tax_rate" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Store Tax Rate (%)</label>
                    <div style="display: flex; align-items: center; position: relative;">
                        <input type="number" step="0.1" min="0" max="100" id="ecommerce_tax_rate" name="ecommerce_tax_rate" class="form-control" value="{{ old('ecommerce_tax_rate', $settings['ecommerce_tax_rate']) }}" required style="padding-right: 2rem;">
                        <span style="position: absolute; right: 12px; font-weight: 600; color: var(--text-secondary);">%</span>
                    </div>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Sales tax percentage applied to cart checkout. (Default matches global tax: {{ \App\Models\Setting::getValue('tax_rate', '12') }}%)</small>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); margin-top: 2rem; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn" style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 2rem; border-radius: var(--radius-sm); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <i class="fa-solid fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
