@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Header with Settings Tabs -->
    <div class="glass-panel" style="padding: 1.5rem 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-gears" style="color: var(--primary); margin-right: 0.5rem;"></i> System Configurations</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">Configure general metadata, brand styles, email engines, SMS notifications, and active checkout gateways.</p>
        </div>

        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-top: 1.5rem; overflow-x: auto; white-space: nowrap;">
            <button type="button" onclick="switchSettingTab('general')" id="tab-btn-general" class="setting-tab-btn active-tab" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--primary); border-bottom: 2px solid var(--primary);">
                General & Branding
            </button>
            <button type="button" onclick="switchSettingTab('hotel_settings')" id="tab-btn-hotel_settings" class="setting-tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                Hotel Settings
            </button>
            <button type="button" onclick="switchSettingTab('slider')" id="tab-btn-slider" class="setting-tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                Hero Slider
            </button>
            @if(auth()->user()->isSuperAdmin())
            <button type="button" onclick="switchSettingTab('smtp')" id="tab-btn-smtp" class="setting-tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                SMTP Email
            </button>
            <button type="button" onclick="switchSettingTab('payments')" id="tab-btn-payments" class="setting-tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                Payment Gateways
            </button>
            <button type="button" onclick="switchSettingTab('sms')" id="tab-btn-sms" class="setting-tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                SMS Gateway
            </button>
            @endif
            @if(Route::has('admin.products'))
            <a href="{{ route('admin.products') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                Shop & Restaurant Products
            </a>
            @endif
            @if(Route::has('admin.orders'))
            <a href="{{ route('admin.orders') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                E-commerce Orders
            </a>
            @endif
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-panel" style="padding: 2rem;">
        @csrf

        <!-- Tab 1: General & Branding -->
        <div id="setting-content-general" class="setting-tab-content">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--primary);"><i class="fa-solid fa-hotel"></i> General Settings & Branding</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Hotel/Brand Name</label>
                    <input type="text" name="hotel_name" class="form-control" value="{{ $settings['hotel_name'] }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Global Currency Symbol / Code</label>
                    <input type="text" name="currency" class="form-control" value="{{ $settings['currency'] }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ $settings['tax_rate'] }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Default Check-In Time</label>
                    <input type="text" name="check_in_time" class="form-control" value="{{ $settings['check_in_time'] }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Default Check-Out Time</label>
                    <input type="text" name="check_out_time" class="form-control" value="{{ $settings['check_out_time'] }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Contact Email Address</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] }}" required>
                </div>
                @include('partials.phone-input', ['field' => 'contact_phone', 'countryField' => 'contact_phone_country_code', 'label' => 'Contact Phone Number', 'value' => $settings['contact_phone'], 'required' => true])
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Physical Address</label>
                <textarea name="physical_address" class="form-control" rows="3" placeholder="Street address guests can follow or copy">{{ $settings['physical_address'] }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Google Maps Embed URL / Iframe Code</label>
                <textarea name="map_address" class="form-control" rows="3" placeholder="Paste a Google Maps embed URL or full iframe code">{{ $settings['map_address'] }}</textarea>
            </div>

            <div style="border-top: 1px solid var(--border-color); margin: 2rem 0; padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-palette"></i> Colors & Typography</h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Primary Color Theme</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="color" name="primary_color" class="form-control" value="{{ $settings['primary_color'] }}" style="width: 50px; padding: 2px; height: 38px;">
                        <input type="text" class="form-control" value="{{ $settings['primary_color'] }}" oninput="this.previousElementSibling.value = this.value" style="font-family: monospace;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Secondary Color Theme</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="color" name="secondary_color" class="form-control" value="{{ $settings['secondary_color'] }}" style="width: 50px; padding: 2px; height: 38px;">
                        <input type="text" class="form-control" value="{{ $settings['secondary_color'] }}" oninput="this.previousElementSibling.value = this.value" style="font-family: monospace;">
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); margin: 2rem 0; padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-icons"></i> Logo & Layout Elements</h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Logo Render Type</label>
                    <select name="logo_type" class="form-control form-select" onchange="toggleLogoFields(this.value)">
                        <option value="text" {{ $settings['logo_type'] === 'text' ? 'selected' : '' }}>Text Overrides</option>
                        <option value="image" {{ $settings['logo_type'] === 'image' ? 'selected' : '' }}>Image Upload</option>
                    </select>
                </div>

                <div class="form-group logo-text-group" style="display: {{ $settings['logo_type'] === 'text' ? 'block' : 'none' }};">
                    <label class="form-label">Logo Plain Text / HTML Icons</label>
                    <input type="text" name="logo_text" class="form-control" value="{{ $settings['logo_text'] }}">
                </div>

                <div class="form-group logo-image-group" style="display: {{ $settings['logo_type'] === 'image' ? 'block' : 'none' }};">
                    <label class="form-label">Upload Brand Image Logo</label>
                    <input type="file" name="logo_image" class="form-control">
                    @if($settings['logo_image'])
                        <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Current Logo:</span>
                            <img src="{{ $settings['logo_image'] }}" alt="Current logo" style="max-height: 30px; background: rgba(0,0,0,0.05); border-radius: 4px;">
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Login & Register Background Image</label>
                <div style="display: grid; grid-template-columns: 180px 1fr; gap: 1rem; align-items: center;">
                    <div style="height: 110px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); background: var(--background);">
                        <img src="{{ $settings['auth_background_image'] }}" alt="Auth Background" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <input type="file" name="auth_background_image" class="form-control" accept="image/*">
                        <p class="form-hint">Used behind login, register, and password reset pages.</p>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Global Header Notice (HTML allowed)</label>
                    <input type="text" name="global_header" class="form-control" value="{{ $settings['global_header'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Global Footer Copy (HTML allowed)</label>
                    <input type="text" name="global_footer" class="form-control" value="{{ $settings['global_footer'] }}">
                </div>
            </div>
        </div>

        <!-- Tab 2: SMTP Mail Setup -->
        @if(auth()->user()->isSuperAdmin())
        <div id="setting-content-smtp" class="setting-tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--primary);"><i class="fa-solid fa-envelope"></i> SMTP Server Configurations</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">SMTP Mail Host</label>
                    <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">SMTP Mail Port</label>
                    <input type="text" name="mail_port" class="form-control" value="{{ $settings['mail_port'] }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">SMTP Username</label>
                    <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">SMTP Password</label>
                    <input type="password" name="mail_password" class="form-control" value="{{ $settings['mail_password'] }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Mail Encryption (SSL/TLS)</label>
                    <input type="text" name="mail_encryption" class="form-control" value="{{ $settings['mail_encryption'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Mail Sender Email (From Address)</label>
                    <input type="email" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] }}">
                </div>
            </div>
        </div>

        <!-- Tab 3: Payment Gateways -->
        <div id="setting-content-payments" class="setting-tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--primary);"><i class="fa-solid fa-credit-card"></i> Paystack & Flutterwave Gateway Credentials</h3>
            
            <div class="form-group" style="margin-bottom: 1.5rem; max-width: 400px;">
                <label class="form-label">Active Customer Checkout Gateway</label>
                <select name="active_payment_gateway" class="form-control form-select">
                    <option value="disabled" {{ $settings['active_payment_gateway'] === 'disabled' ? 'selected' : '' }}>Disabled until gateway keys are configured</option>
                    <option value="paystack" {{ $settings['active_payment_gateway'] === 'paystack' ? 'selected' : '' }}>Paystack API Checkout</option>
                    <option value="flutterwave" {{ $settings['active_payment_gateway'] === 'flutterwave' ? 'selected' : '' }}>Flutterwave API Checkout</option>
                </select>
                <p style="margin-top:.6rem;color:var(--text-secondary);font-size:.85rem;">Customer bookings are only marked paid after Paystack or Flutterwave verifies the transaction reference on the server.</p>
            </div>

            <div style="border-top: 1px solid var(--border-color); margin: 2rem 0; padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-brands fa-stripe"></i> Paystack Parameters</h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Paystack Public Key</label>
                    <input type="text" name="paystack_public_key" class="form-control" value="{{ $settings['paystack_public_key'] }}" placeholder="pk_live_...">
                </div>
                <div class="form-group">
                    <label class="form-label">Paystack Secret Key</label>
                    <input type="password" name="paystack_secret_key" class="form-control" value="{{ $settings['paystack_secret_key'] }}" placeholder="sk_live_...">
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); margin: 2rem 0; padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-money-bill-transfer"></i> Flutterwave Parameters</h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Flutterwave Public Key</label>
                    <input type="text" name="flutterwave_public_key" class="form-control" value="{{ $settings['flutterwave_public_key'] }}" placeholder="FLWPUBK-...">
                </div>
                <div class="form-group">
                    <label class="form-label">Flutterwave Secret Key</label>
                    <input type="password" name="flutterwave_secret_key" class="form-control" value="{{ $settings['flutterwave_secret_key'] }}" placeholder="FLWSECK-...">
                </div>
            </div>
        </div>

        <!-- Tab 4: SMS Gateways -->
        <div id="setting-content-sms" class="setting-tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--primary);"><i class="fa-solid fa-message"></i> SMS Gateways & API Configurations</h3>
            
            <div class="form-group" style="margin-bottom: 1.5rem; max-width: 400px;">
                <label class="form-label">SMS Provider Service</label>
                <select name="sms_gateway_provider" class="form-control form-select" onchange="toggleSmsFields(this.value)">
                    <option value="disabled" {{ $settings['sms_gateway_provider'] === 'disabled' ? 'selected' : '' }}>Disabled</option>
                    <option value="twilio" {{ $settings['sms_gateway_provider'] === 'twilio' ? 'selected' : '' }}>Twilio Integration</option>
                    <option value="vonage" {{ $settings['sms_gateway_provider'] === 'vonage' ? 'selected' : '' }}>Vonage (Nexmo) Integration</option>
                    <option value="custom" {{ $settings['sms_gateway_provider'] === 'custom' ? 'selected' : '' }}>Custom Gateway API</option>
                </select>
            </div>

            <!-- Twilio Credentials -->
            <div class="sms-group-twilio" style="display: {{ $settings['sms_gateway_provider'] === 'twilio' ? 'block' : 'none' }}; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-message"></i> Twilio Credentials</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Twilio SID</label>
                        <input type="text" name="twilio_sid" class="form-control" value="{{ $settings['twilio_sid'] }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Twilio Auth Token</label>
                        <input type="password" name="twilio_token" class="form-control" value="{{ $settings['twilio_token'] }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Twilio Sender Number (From)</label>
                        <input type="text" name="twilio_from" class="form-control" value="{{ $settings['twilio_from'] }}">
                    </div>
                </div>
            </div>

            <!-- Vonage Credentials -->
            <div class="sms-group-vonage" style="display: {{ $settings['sms_gateway_provider'] === 'vonage' ? 'block' : 'none' }}; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-comment-dots"></i> Vonage Credentials</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Vonage API Key</label>
                        <input type="text" name="vonage_api_key" class="form-control" value="{{ $settings['vonage_api_key'] }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vonage API Secret</label>
                        <input type="password" name="vonage_api_secret" class="form-control" value="{{ $settings['vonage_api_secret'] }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vonage Sender Tag (From)</label>
                        <input type="text" name="vonage_from" class="form-control" value="{{ $settings['vonage_from'] }}">
                    </div>
                </div>
            </div>

            <!-- Custom SMS credentials -->
            <div class="sms-group-custom" style="display: {{ $settings['sms_gateway_provider'] === 'custom' ? 'block' : 'none' }}; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                @php
                    $customSmsHeaders = json_decode($settings['custom_sms_headers'] ?: '{}', true) ?: [];
                    $customSmsPayload = json_decode($settings['custom_sms_payload'] ?: '{}', true) ?: [];
                    if (empty($customSmsPayload)) {
                        $customSmsPayload = ['to' => '{to}', 'message' => '{message}'];
                    }
                @endphp
                <h4 style="font-size: 1.1rem; margin-bottom: 1rem;"><i class="fa-solid fa-code"></i> Custom API Parameters</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Custom Gateway Endpoint URL</label>
                        <input type="text" name="custom_sms_url" class="form-control" value="{{ $settings['custom_sms_url'] }}" placeholder="https://api.sms-sender.com/send">
                    </div>
                    <div class="form-group">
                        <label class="form-label">HTTP Method</label>
                        <select name="custom_sms_method" class="form-control form-select">
                            <option value="GET" {{ $settings['custom_sms_method'] === 'GET' ? 'selected' : '' }}>GET</option>
                            <option value="POST" {{ $settings['custom_sms_method'] === 'POST' ? 'selected' : '' }}>POST</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Request Headers</label>
                        <div id="admin-sms-header-rows" style="display:grid;gap:0.75rem;">
                            @forelse($customSmsHeaders as $key => $value)
                                <div class="sms-pair-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem;">
                                    <input type="text" name="custom_sms_header_keys[]" class="form-control" value="{{ $key }}" placeholder="Header key">
                                    <input type="text" name="custom_sms_header_values[]" class="form-control" value="{{ $value }}" placeholder="Header value">
                                    <button type="button" class="btn btn-outline" onclick="this.closest('.sms-pair-row').remove()"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @empty
                                <div class="sms-pair-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem;">
                                    <input type="text" name="custom_sms_header_keys[]" class="form-control" placeholder="Authorization">
                                    <input type="text" name="custom_sms_header_values[]" class="form-control" placeholder="Bearer token">
                                    <button type="button" class="btn btn-outline" onclick="this.closest('.sms-pair-row').remove()"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-outline" style="margin-top:0.75rem;" onclick="addSmsPairRow('admin-sms-header-rows', 'custom_sms_header_keys[]', 'custom_sms_header_values[]')"><i class="fa-solid fa-plus"></i> Add Header</button>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Body / Query Parameters</label>
                        <div id="admin-sms-payload-rows" style="display:grid;gap:0.75rem;">
                            @foreach($customSmsPayload as $key => $value)
                                <div class="sms-pair-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem;">
                                    <input type="text" name="custom_sms_payload_keys[]" class="form-control" value="{{ $key }}" placeholder="Parameter key">
                                    <input type="text" name="custom_sms_payload_values[]" class="form-control" value="{{ $value }}" placeholder="{to} or {message}">
                                    <button type="button" class="btn btn-outline" onclick="this.closest('.sms-pair-row').remove()"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline" style="margin-top:0.75rem;" onclick="addSmsPairRow('admin-sms-payload-rows', 'custom_sms_payload_keys[]', 'custom_sms_payload_values[]')"><i class="fa-solid fa-plus"></i> Add Parameter</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tab: Hotel Settings -->
        <div id="setting-content-hotel_settings" class="setting-tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--primary);"><i class="fa-solid fa-hotel"></i> Hotel Customization Settings</h3>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Hero Banner Subtitle / Tagline</label>
                <input type="text" name="hero_subtitle" class="form-control" value="{{ $settings['hero_subtitle'] ?? '' }}">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Marketing Welcome Description</label>
                <textarea name="welcome_description" class="form-control" rows="4">{{ $settings['welcome_description'] ?? '' }}</textarea>
            </div>

            <div class="glass-panel" style="padding: 1rem; margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 0.5rem; color: var(--text-primary);">Guest Testimonials</h4>
                <p style="color: var(--text-secondary); margin: 0 0 1rem;">Testimonials are managed with the dashboard form instead of raw JSON.</p>
                <a href="{{ route('admin.testimonials') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <i class="fa-solid fa-comments"></i> Manage Testimonials
                </a>
            </div>
        </div>

        <!-- Submit Button -->
        <div id="settings-submit-container" style="border-top: 1px solid var(--border-color); margin-top: 2rem; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> Save Configurations
            </button>
        </div>
    </form>

    <!-- Tab: Hero Slider (outside main form because of nested forms) -->
    <div id="setting-content-slider" class="setting-tab-content" style="display: none; margin-top: 1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
            <div>
                <h3 style="font-size:1.15rem;font-weight:700;margin:0;"><i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Current Hero Slides</h3>
                <p style="color:var(--text-secondary);margin:.35rem 0 0;">Manage homepage slide content and visual order.</p>
            </div>
            <button type="button" class="btn btn-primary" onclick="openCreateSlideModal()"><i class="fa-solid fa-plus"></i> Create Hero Slide</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 2rem; align-items: stretch;">
            <!-- Create Slide Form -->
            <div class="modal" id="createSlideModal">
                <div class="modal-content glass-panel modal-lg">
                    <div class="modal-header">
                        <h3 style="font-size: 1.15rem; font-weight: 700;">
                            <i class="fa-solid fa-plus" style="color: var(--primary);"></i> Add New Hero Slide
                        </h3>
                        <button type="button" class="theme-toggle" onclick="closeCreateSlideModal()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @csrf
                    <div class="form-group">
                        <label for="slide_image" class="form-label">Background Image</label>
                        <input type="file" name="image" id="slide_image" class="form-control" accept="image/*" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slide_title" class="form-label">Slide Title</label>
                        <input type="text" name="title" id="slide_title" class="form-control" placeholder="e.g. Luxury Awaits You">
                    </div>

                    <div class="form-group">
                        <label for="slide_subtitle" class="form-label">Slide Subtitle</label>
                        <textarea name="subtitle" id="slide_subtitle" class="form-control" rows="2" placeholder="Short descriptive paragraph..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="slide_btn_text" class="form-label">Button Text</label>
                            <input type="text" name="button_text" id="slide_btn_text" class="form-control" value="Book Now">
                        </div>
                        <div class="form-group">
                            <label for="slide_btn_link" class="form-label">Button Link</label>
                            <input type="text" name="button_link" id="slide_btn_link" class="form-control" value="#rooms">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="slide_sort" class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="slide_sort" class="form-control" value="1" min="0" required>
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span>Active Slide</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; justify-content: center;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Create Slide
                    </button>
                </form>
                </div>
            </div>

            <!-- Existing Slides List -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @forelse($slides as $slide)
                    <div class="glass-panel" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem; position: relative;">
                        <!-- Slide Header Preview -->
                        <div style="display: flex; gap: 1.5rem; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                            <div style="width: 120px; height: 75px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0; background: #222;">
                                <img src="{{ $slide->image_path }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $slide->title ?: '(No Title)' }}</h4>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $slide->subtitle ?: '(No Subtitle)' }}
                                </p>
                                <div style="display: flex; gap: 1rem; margin-top: 0.5rem; font-size: 0.8rem; font-weight: 600;">
                                    <span>Order: <mark style="background: var(--primary-glow); color: var(--primary); padding: 0.1rem 0.4rem; border-radius: 3px;">{{ $slide->sort_order }}</mark></span>
                                    <span>Status: 
                                        @if($slide->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Update Slide Form inline -->
                        <form action="{{ route('admin.slider.update', $slide) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Replace Slide Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Slide Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $slide->title }}">
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 0.75rem;">
                                <label class="form-label">Slide Subtitle</label>
                                <textarea name="subtitle" class="form-control" rows="2">{{ $slide->subtitle }}</textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; margin-top: 0.75rem; align-items: flex-end;">
                                <div class="form-group">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="button_text" class="form-control" value="{{ $slide->button_text }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Button Link</label>
                                    <input type="text" name="button_link" class="form-control" value="{{ $slide->button_link }}">
                                </div>
                                <div class="form-group" style="width: 100px;">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $slide->sort_order }}" min="0">
                                </div>
                            </div>

                            <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                                    <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }}>
                                    <span>Active Slide</span>
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="submit" class="btn btn-outline" style="padding: 0.5rem 1.25rem;">
                                        <i class="fa-solid fa-check"></i> Save Changes
                                    </button>
                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1.25rem;" name="_method" value="POST" onclick="this.form.action='{{ route('admin.slider.delete', $slide) }}'; return confirm('Are you sure you want to delete this slide?');">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="glass-panel" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        <i class="fa-regular fa-images" style="font-size: 2.5rem; margin-bottom: 1rem; display: block; color: var(--primary);"></i>
                        No slides found. Create one on the left pane!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function switchSettingTab(tab) {
        const tabs = ['general', 'hotel_settings', 'slider', 'smtp', 'payments', 'sms'];
        
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-btn-${t}`);
            const content = document.getElementById(`setting-content-${t}`);
            
            if (btn && content) {
                if (t === tab) {
                    btn.className = 'setting-tab-btn active-tab';
                    btn.style.color = 'var(--primary)';
                    btn.style.borderBottomColor = 'var(--primary)';
                    content.style.display = 'block';
                } else {
                    btn.className = 'setting-tab-btn';
                    btn.style.color = 'var(--text-secondary)';
                    btn.style.borderBottomColor = 'transparent';
                    content.style.display = 'none';
                }
            }
        });

        const submitBtnContainer = document.getElementById('settings-submit-container');
        if (submitBtnContainer) {
            if (tab === 'slider') {
                submitBtnContainer.style.display = 'none';
            } else {
                submitBtnContainer.style.display = 'flex';
            }
        }
    }

    function toggleLogoFields(type) {
        const textGroup = document.querySelector('.logo-text-group');
        const imgGroup = document.querySelector('.logo-image-group');

        if (textGroup && imgGroup) {
            if (type === 'text') {
                textGroup.style.display = 'block';
                imgGroup.style.display = 'none';
            } else {
                textGroup.style.display = 'none';
                imgGroup.style.display = 'block';
            }
        }
    }

    function toggleSmsFields(provider) {
        const twilio = document.querySelector('.sms-group-twilio');
        const vonage = document.querySelector('.sms-group-vonage');
        const custom = document.querySelector('.sms-group-custom');

        if (twilio) twilio.style.display = provider === 'twilio' ? 'block' : 'none';
        if (vonage) vonage.style.display = provider === 'vonage' ? 'block' : 'none';
        if (custom) custom.style.display = provider === 'custom' ? 'block' : 'none';
    }

    function addSmsPairRow(containerId, keyName, valueName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'sms-pair-row';
        row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr auto;gap:0.5rem;';
        row.innerHTML = `
            <input type="text" name="${keyName}" class="form-control" placeholder="Key">
            <input type="text" name="${valueName}" class="form-control" placeholder="Value">
            <button type="button" class="btn btn-outline" onclick="this.closest('.sms-pair-row').remove()"><i class="fa-solid fa-xmark"></i></button>
        `;
        container.appendChild(row);
    }

    function openCreateSlideModal() {
        document.getElementById('createSlideModal').classList.add('active');
    }

    function closeCreateSlideModal() {
        document.getElementById('createSlideModal').classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const slideModal = document.getElementById('createSlideModal');
        window.addEventListener('click', function(event) {
            if (event.target === slideModal) {
                closeCreateSlideModal();
            }
        });
    });
</script>

<style>
    .setting-tab-btn {
        transition: border-bottom var(--transition-fast), color var(--transition-fast);
    }
    .setting-tab-btn:hover {
        color: var(--primary) !important;
    }
</style>
@endsection
