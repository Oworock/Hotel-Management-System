@extends('layouts.app')

@section('title', 'System Configuration')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Tab Navigation Bar -->
    <div class="tabs-nav" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2.5rem; overflow-x: auto; white-space: nowrap;">
        <button type="button" class="tab-btn active" onclick="switchTab(event, 'general')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--primary); cursor: pointer; border-bottom: 3px solid var(--primary); transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-sliders"></i> General
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'branding')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-palette"></i> Branding & Colors
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'email')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-envelope"></i> SMTP Email
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'payments')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-credit-card"></i> Gateways
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'sms')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-comment-sms"></i> SMS Gateway
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'slider')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-images"></i> Hero Slider
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'cms')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-file-lines"></i> Pages CMS
        </button>
        <button type="button" class="tab-btn" onclick="switchTab(event, 'coupons')" style="background: none; border: none; padding: 0.75rem 1.25rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 3px solid transparent; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-tags"></i> Coupons & Marketing
        </button>
    </div>

    <!-- Master Update Form -->
    <form action="{{ route('super_admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- TAB: General Config -->
        <div id="tab-general" class="tab-content" style="display: block;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-circle-info" style="color: var(--primary); margin-right: 0.5rem;"></i> General Platform Settings
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="hotel_name" class="form-label">Hotel Brand Name</label>
                    <input type="text" name="hotel_name" id="hotel_name" class="form-control" value="{{ $settings['hotel_name'] }}" required>
                </div>
                
                <div class="form-group">
                    <label for="currency" class="form-label">Currency Symbol / Code</label>
                    <input type="text" name="currency" id="currency" class="form-control" value="{{ $settings['currency'] }}" placeholder="USD, EUR, £, ₦" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" class="form-control" value="{{ $settings['tax_rate'] }}" required>
                </div>
                
                <div class="form-group">
                    <label for="check_in_time" class="form-label">Default Check-In Time</label>
                    <input type="text" name="check_in_time" id="check_in_time" class="form-control" value="{{ $settings['check_in_time'] }}" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out_time" class="form-label">Default Check-Out Time</label>
                    <input type="text" name="check_out_time" id="check_out_time" class="form-control" value="{{ $settings['check_out_time'] }}" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_email" class="form-label">Public Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ $settings['contact_email'] }}" required>
                </div>
                
                <div class="form-group">
                    <label for="contact_phone" class="form-label">Public Phone Number</label>
                    <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ $settings['contact_phone'] }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="map_address" class="form-label">Google Maps Embed URL / Address</label>
                <textarea name="map_address" id="map_address" class="form-control" rows="3" placeholder="Embed code link or raw text address...">{{ $settings['map_address'] }}</textarea>
            </div>
        </div>

        <!-- TAB: Branding & Design -->
        <div id="tab-branding" class="tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-brush" style="color: var(--secondary); margin-right: 0.5rem;"></i> Theme & Custom Styling Options
            </h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color" class="form-label" style="display: flex; align-items: center; justify-content: space-between;">
                        Primary Accent Color
                        <span style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;">{{ $settings['primary_color'] }}</span>
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="color" id="primary_picker" class="form-control" style="width: 60px; height: 42px; padding: 0.2rem; cursor: pointer;" value="{{ $settings['primary_color'] }}" oninput="document.getElementById('primary_color').value = this.value">
                        <input type="text" name="primary_color" id="primary_color" class="form-control" value="{{ $settings['primary_color'] }}" oninput="document.getElementById('primary_picker').value = this.value">
                    </div>
                </div>

                <div class="form-group">
                    <label for="secondary_color" class="form-label" style="display: flex; align-items: center; justify-content: space-between;">
                        Secondary Glow Color
                        <span style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;">{{ $settings['secondary_color'] }}</span>
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="color" id="secondary_picker" class="form-control" style="width: 60px; height: 42px; padding: 0.2rem; cursor: pointer;" value="{{ $settings['secondary_color'] }}" oninput="document.getElementById('secondary_color').value = this.value">
                        <input type="text" name="secondary_color" id="secondary_color" class="form-control" value="{{ $settings['secondary_color'] }}" oninput="document.getElementById('secondary_picker').value = this.value">
                    </div>
                </div>
            </div>

            <!-- Logo Configuration Section -->
            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">
            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem;">Platform Logo Configuration</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="logo_type" class="form-label">Logo Type</label>
                    <select name="logo_type" id="logo_type" class="form-control">
                        <option value="text" {{ $settings['logo_type'] === 'text' ? 'selected' : '' }}>Text / HTML Logo</option>
                        <option value="image" {{ $settings['logo_type'] === 'image' ? 'selected' : '' }}>Custom Image Logo</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="logo_text" class="form-label">Logo Text / HTML</label>
                    <input type="text" name="logo_text" id="logo_text" class="form-control" value="{{ $settings['logo_text'] }}">
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">For Text logos (e.g. <code>&lt;i class="fa-solid fa-hotel"&gt;&lt;/i&gt; Aetheria</code>)</p>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="logo_image" class="form-label">Upload Image Logo</label>
                <div style="display: flex; gap: 1.5rem; align-items: center;">
                    @if(!empty($settings['logo_image']))
                        <div style="background: var(--background); padding: 0.5rem; border-radius: 4px; border: 1px solid var(--border-color);">
                            <img src="{{ $settings['logo_image'] }}" alt="Current Logo" style="max-height: 40px; object-fit: contain;">
                        </div>
                    @endif
                    <input type="file" name="logo_image" id="logo_image" class="form-control" accept="image/*">
                </div>
            </div>

            <input type="hidden" name="platform_logo" value="{{ $settings['platform_logo'] }}">

            <div class="form-row" style="margin-top: 1.5rem;">
                <div class="form-group">
                    <label for="global_header" class="form-label">Global Header Announcement</label>
                    <input type="text" name="global_header" id="global_header" class="form-control" value="{{ $settings['global_header'] }}">
                </div>

                <div class="form-group">
                    <label for="global_footer" class="form-label">Global Footer Details</label>
                    <input type="text" name="global_footer" id="global_footer" class="form-control" value="{{ $settings['global_footer'] }}">
                </div>
            </div>
        </div>

        <!-- TAB: SMTP Server Email Configuration -->
        <div id="tab-email" class="tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-envelope-open-text" style="color: var(--info); margin-right: 0.5rem;"></i> SMTP Email Server Settings
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="mail_host" class="form-label">SMTP Host</label>
                    <input type="text" name="mail_host" id="mail_host" class="form-control" value="{{ $settings['mail_host'] }}" placeholder="smtp.example.com">
                </div>
                <div class="form-group">
                    <label for="mail_port" class="form-label">SMTP Port</label>
                    <input type="text" name="mail_port" id="mail_port" class="form-control" value="{{ $settings['mail_port'] }}" placeholder="587">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_username" class="form-label">SMTP Username</label>
                    <input type="text" name="mail_username" id="mail_username" class="form-control" value="{{ $settings['mail_username'] }}">
                </div>
                <div class="form-group">
                    <label for="mail_password" class="form-label">SMTP Password</label>
                    <input type="password" name="mail_password" id="mail_password" class="form-control" value="{{ $settings['mail_password'] }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_encryption" class="form-label">Encryption Protocol</label>
                    <select name="mail_encryption" id="mail_encryption" class="form-control">
                        <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                        <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ $settings['mail_encryption'] == 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mail_from_address" class="form-label">Sender Email Address</label>
                    <input type="email" name="mail_from_address" id="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] }}" placeholder="noreply@hotel.com">
                </div>
            </div>
        </div>

        <!-- TAB: Payment Gateways -->
        <div id="tab-payments" class="tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-wallet" style="color: var(--success); margin-right: 0.5rem;"></i> Payment Gateway Config
            </h3>

            <div class="form-group">
                <label class="form-label" style="font-weight:700;">Active Platform Payment System</label>
                <div style="display: flex; gap: 2rem; margin: 1rem 0; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="active_payment_gateway" value="card_simulation" {{ $settings['active_payment_gateway'] == 'card_simulation' ? 'checked' : '' }}>
                        <span>Interactive 3D Card Simulator</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="active_payment_gateway" value="paystack" {{ $settings['active_payment_gateway'] == 'paystack' ? 'checked' : '' }}>
                        <span style="color:#09a5db; font-weight:600;"><i class="fa-solid fa-money-bill-transfer"></i> Paystack Portal</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="active_payment_gateway" value="flutterwave" {{ $settings['active_payment_gateway'] == 'flutterwave' ? 'checked' : '' }}>
                        <span style="color:#f5a623; font-weight:600;"><i class="fa-solid fa-money-bill-wave"></i> Flutterwave Portal</span>
                    </label>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: #09a5db;"><i class="fa-solid fa-shield-halved"></i> Paystack Credentials</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="paystack_public_key" class="form-label">Paystack Public Key</label>
                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{ $settings['paystack_public_key'] }}" placeholder="pk_test_...">
                    </div>
                    <div class="form-group">
                        <label for="paystack_secret_key" class="form-label">Paystack Secret Key</label>
                        <input type="password" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{ $settings['paystack_secret_key'] }}" placeholder="sk_test_...">
                    </div>
                </div>
            </div>

            <div style="border-top: 1px dashed var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: #f5a623;"><i class="fa-solid fa-shield-halved"></i> Flutterwave Credentials</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="flutterwave_public_key" class="form-label">Flutterwave Public Key</label>
                        <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{ $settings['flutterwave_public_key'] }}" placeholder="FLWPUBK_TEST-...">
                    </div>
                    <div class="form-group">
                        <label for="flutterwave_secret_key" class="form-label">Flutterwave Secret Key</label>
                        <input type="password" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{ $settings['flutterwave_secret_key'] }}" placeholder="FLWSECK_TEST-...">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: SMS Gateway Settings -->
        <div id="tab-sms" class="tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-comment-sms" style="color: var(--primary); margin-right: 0.5rem;"></i> SMS Gateway Configuration
            </h3>
            
            <div class="form-group">
                <label for="sms_gateway_provider" class="form-label">SMS Provider</label>
                <select name="sms_gateway_provider" id="sms_gateway_provider" class="form-control" onchange="toggleSmsFields()">
                    <option value="disabled" {{ $settings['sms_gateway_provider'] == 'disabled' ? 'selected' : '' }}>Disabled</option>
                    <option value="twilio" {{ $settings['sms_gateway_provider'] == 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="vonage" {{ $settings['sms_gateway_provider'] == 'vonage' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                    <option value="custom" {{ $settings['sms_gateway_provider'] == 'custom' ? 'selected' : '' }}>Custom HTTP API</option>
                </select>
            </div>

            <!-- Twilio Credentials -->
            <div id="sms-fields-twilio" class="sms-provider-fields" style="display: none; margin-top: 1.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; background: rgba(255, 255, 255, 0.02);">
                <h4 style="margin-bottom: 1rem; color: #ff002b;"><i class="fa-solid fa-shield-halved"></i> Twilio API Settings</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="twilio_sid" class="form-label">Twilio Account SID</label>
                        <input type="text" name="twilio_sid" id="twilio_sid" class="form-control" value="{{ $settings['twilio_sid'] }}" placeholder="AC...">
                    </div>
                    <div class="form-group">
                        <label for="twilio_token" class="form-label">Twilio Auth Token</label>
                        <input type="password" name="twilio_token" id="twilio_token" class="form-control" value="{{ $settings['twilio_token'] }}">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="twilio_from" class="form-label">Twilio Sender Number (From)</label>
                    <input type="text" name="twilio_from" id="twilio_from" class="form-control" value="{{ $settings['twilio_from'] }}" placeholder="+15551234567">
                </div>
            </div>

            <!-- Vonage Credentials -->
            <div id="sms-fields-vonage" class="sms-provider-fields" style="display: none; margin-top: 1.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; background: rgba(255, 255, 255, 0.02);">
                <h4 style="margin-bottom: 1rem; color: #6e44ff;"><i class="fa-solid fa-shield-halved"></i> Vonage API Settings</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="vonage_api_key" class="form-label">Vonage API Key</label>
                        <input type="text" name="vonage_api_key" id="vonage_api_key" class="form-control" value="{{ $settings['vonage_api_key'] }}">
                    </div>
                    <div class="form-group">
                        <label for="vonage_api_secret" class="form-label">Vonage API Secret</label>
                        <input type="password" name="vonage_api_secret" id="vonage_api_secret" class="form-control" value="{{ $settings['vonage_api_secret'] }}">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="vonage_from" class="form-label">Vonage Sender Identity (From)</label>
                    <input type="text" name="vonage_from" id="vonage_from" class="form-control" value="{{ $settings['vonage_from'] }}" placeholder="Aetheria">
                </div>
            </div>

            <!-- Custom HTTP API Gateway -->
            <div id="sms-fields-custom" class="sms-provider-fields" style="display: none; margin-top: 1.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; background: rgba(255, 255, 255, 0.02);">
                <h4 style="margin-bottom: 1rem; color: #f44496;"><i class="fa-solid fa-gear"></i> Custom HTTP Gateway API</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="custom_sms_url" class="form-label">Gateway API Endpoint URL</label>
                        <input type="text" name="custom_sms_url" id="custom_sms_url" class="form-control" value="{{ $settings['custom_sms_url'] }}" placeholder="https://api.sms-gateway.com/send?to={to}&message={message}">
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Use <code>{to}</code> and <code>{message}</code> placeholders.</p>
                    </div>
                    <div class="form-group">
                        <label for="custom_sms_method" class="form-label">HTTP Method</label>
                        <select name="custom_sms_method" id="custom_sms_method" class="form-control">
                            <option value="POST" {{ $settings['custom_sms_method'] == 'POST' ? 'selected' : '' }}>POST</option>
                            <option value="GET" {{ $settings['custom_sms_method'] == 'GET' ? 'selected' : '' }}>GET</option>
                        </select>
                    </div>
                </div>
                <div class="form-row" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label for="custom_sms_headers" class="form-label">HTTP Headers (JSON Format)</label>
                        <textarea name="custom_sms_headers" id="custom_sms_headers" class="form-control" rows="3" style="font-family: monospace; font-size: 0.9rem;">{{ $settings['custom_sms_headers'] }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="custom_sms_payload" class="form-label">HTTP Body/Payload Template (JSON Format)</label>
                        <textarea name="custom_sms_payload" id="custom_sms_payload" class="form-control" rows="3" style="font-family: monospace; font-size: 0.9rem;">{{ $settings['custom_sms_payload'] }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Test SMS Form Action via formaction -->
            <div style="margin-top: 2rem; border-top: 1px dashed var(--border-color); padding-top: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);"><i class="fa-solid fa-paper-plane"></i> Dispatch Test SMS</h4>
                <div class="form-row" style="align-items: flex-end;">
                    <div class="form-group">
                        <label for="test_phone_number" class="form-label">Test Recipient Phone Number</label>
                        <input type="text" name="phone_number" id="test_phone_number" class="form-control" placeholder="+1234567890">
                    </div>
                    <div class="form-group">
                        <label for="test_sms_message" class="form-label">Test Message Content</label>
                        <input type="text" name="message" id="test_sms_message" class="form-control" placeholder="Aetheria System Test SMS.">
                    </div>
                    <div class="form-group" style="flex: 0 0 auto;">
                        <button type="submit" formaction="{{ route('super_admin.settings.sms_test') }}" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">
                            <i class="fa-solid fa-paper-plane"></i> Send Test SMS
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: Coupons & Marketing -->
        <div id="tab-marketing" class="tab-content" style="display: none;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-bullhorn" style="color: var(--primary); margin-right: 0.5rem;"></i> Marketing & Search Engine Optimization
            </h3>

            <!-- SEO Configuration Section -->
            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--primary);">Platform SEO Metadata Settings</h4>
            <div class="form-group">
                <label for="meta_title" class="form-label">Global Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ $settings['meta_title'] }}" placeholder="StayFlow - Premium Hotel Management System">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="meta_description" class="form-label">Global Meta Description</label>
                    <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="Description for search engines...">{{ $settings['meta_description'] }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords" class="form-label">Meta Keywords (Comma separated)</label>
                    <textarea name="meta_keywords" id="meta_keywords" class="form-control" rows="3" placeholder="hotel, booking, resort, luxury suite...">{{ $settings['meta_keywords'] }}</textarea>
                </div>
            </div>

            <!-- Front-end Marketing Welcome Banner Content -->
            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">
            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--secondary);">Homepage Welcome Banner</h4>
            <div class="form-row">
                <div class="form-group">
                    <label for="welcome_title" class="form-label">Marketing Welcome Header</label>
                    <input type="text" name="welcome_title" id="welcome_title" class="form-control" value="{{ $settings['welcome_title'] }}">
                </div>
                <div class="form-group">
                    <label for="welcome_description" class="form-label">Marketing Welcome Description</label>
                    <textarea name="welcome_description" id="welcome_description" class="form-control" rows="2">{{ $settings['welcome_description'] }}</textarea>
                </div>
            </div>

            <!-- Homepage Promo Popup Section -->
            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">
            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--primary);">Homepage Promo Pop-up Modal</h4>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem; margin-bottom: 1rem;">
                    <input type="checkbox" name="promo_popup_enabled" id="promo_popup_enabled" value="1" {{ $settings['promo_popup_enabled'] == '1' ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    Enable Pop-up Promotion on Homepage
                </label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="promo_popup_title" class="form-label">Promo Title</label>
                    <input type="text" name="promo_popup_title" id="promo_popup_title" class="form-control" value="{{ $settings['promo_popup_title'] }}" placeholder="e.g. Special Holiday Discount!">
                </div>
                <div class="form-group">
                    <label for="promo_popup_coupon" class="form-label">Target Coupon Code</label>
                    <select name="promo_popup_coupon" id="promo_popup_coupon" class="form-control">
                        <option value="">-- No Coupon --</option>
                        @foreach($coupons as $coupon)
                            <option value="{{ $coupon->code }}" {{ $settings['promo_popup_coupon'] === $coupon->code ? 'selected' : '' }}>
                                {{ $coupon->code }} ({{ $coupon->discount_percentage }}% Off)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="promo_popup_content" class="form-label">Promo Content / Description</label>
                    <textarea name="promo_popup_content" id="promo_popup_content" class="form-control" rows="3" placeholder="Get 15% off your next booking by using coupon...">{{ $settings['promo_popup_content'] }}</textarea>
                </div>
                <div class="form-group">
                    <label for="promo_popup_image" class="form-label">Promo Image URL (Optional)</label>
                    <input type="text" name="promo_popup_image" id="promo_popup_image" class="form-control" value="{{ $settings['promo_popup_image'] }}" placeholder="e.g. https://images.unsplash.com/photo-...">
                    <small style="color: var(--text-secondary); font-size: 0.75rem;">Leave empty to use a standard modern background.</small>
                </div>
            </div>
        </div>

        <!-- Master Save Bar (Sticky footer style at the bottom of the form card) -->
        <div style="margin-top: 3rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; justify-content: flex-end;" id="save-button-container">
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <i class="fa-solid fa-floppy-disk"></i> Save Configurations
            </button>
        </div>
    </form>

    <!-- TAB: Hero Slider -->
    <div id="tab-slider" class="tab-content" style="display: none; margin-top: 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 2rem; align-items: start;">
            <!-- Create Slide Form -->
            <div class="glass-panel" style="padding: 1.5rem; border-color: var(--border-color); height: fit-content;">
                <h3 style="font-size: 1.15rem; margin-bottom: 1.5rem; font-weight: 700;">
                    <i class="fa-solid fa-plus" style="color: var(--primary);"></i> Add New Hero Slide
                </h3>
                <form action="{{ route('super_admin.slider.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
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

                    <div class="form-row">
                        <div class="form-group">
                            <label for="slide_btn_text" class="form-label">Button Text</label>
                            <input type="text" name="button_text" id="slide_btn_text" class="form-control" value="Book Now">
                        </div>
                        <div class="form-group">
                            <label for="slide_btn_link" class="form-label">Button Link</label>
                            <input type="text" name="button_link" id="slide_btn_link" class="form-control" value="#rooms">
                        </div>
                    </div>

                    <div class="form-row">
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

            <!-- Existing Slides List -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Current Hero Slides
                </h3>

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
                        <form action="{{ route('super_admin.slider.update', $slide) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
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

                            <div class="form-row" style="margin-top: 0.75rem; align-items: flex-end;">
                                <div class="form-group">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="button_text" class="form-control" value="{{ $slide->button_text }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Button Link</label>
                                    <input type="text" name="button_link" class="form-control" value="{{ $slide->button_link }}">
                                </div>
                                <div class="form-group" style="flex: 0 0 100px;">
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
                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1.25rem;" name="_method" value="POST" onclick="this.form.action='{{ route('super_admin.slider.delete', $slide) }}'; return confirm('Are you sure you want to delete this slide?');">
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

    <!-- TAB: Frontend CMS Pages -->
    <div id="tab-cms" class="tab-content" style="display: none;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
            <i class="fa-solid fa-file-invoice" style="color: var(--primary); margin-right: 0.5rem;"></i> Frontend Pages Management
        </h3>
        
        <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--primary);">About Page Content</h4>
        <form action="{{ route('super_admin.settings.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="about_title" class="form-label">About Page Title</label>
                <input type="text" name="about_title" id="about_title" class="form-control" value="{{ $settings['about_title'] }}">
            </div>
            
            <div class="form-group">
                <label for="about_description" class="form-label">About Page Description</label>
                <textarea name="about_description" id="about_description" class="form-control" rows="2">{{ $settings['about_description'] }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="about_history_text" class="form-label">About Page History Text</label>
                <textarea name="about_history_text" id="about_history_text" class="form-control" rows="4">{{ $settings['about_history_text'] }}</textarea>
            </div>
            
            <input type="hidden" name="hotel_name" value="{{ $settings['hotel_name'] }}">
            <input type="hidden" name="currency" value="{{ $settings['currency'] }}">
            <input type="hidden" name="tax_rate" value="{{ $settings['tax_rate'] }}">
            <input type="hidden" name="check_in_time" value="{{ $settings['check_in_time'] }}">
            <input type="hidden" name="check_out_time" value="{{ $settings['check_out_time'] }}">
            <input type="hidden" name="contact_email" value="{{ $settings['contact_email'] }}">
            <input type="hidden" name="contact_phone" value="{{ $settings['contact_phone'] }}">

            <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update About Info</button>
            </div>
        </form>

        <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">
        <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--secondary);">Testimonials Content (JSON format)</h4>
        <form action="{{ route('super_admin.settings.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="testimonials_list" class="form-label">Guest Testimonials List</label>
                <textarea name="testimonials_list" id="testimonials_list" class="form-control" rows="5" style="font-family: monospace; font-size: 0.9rem;">{{ $settings['testimonials_list'] }}</textarea>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Must be a valid JSON array of objects with keys: <code>name</code>, <code>location</code>, <code>rating</code>, <code>comment</code>, <code>avatar_url</code>.</p>
            </div>

            <input type="hidden" name="hotel_name" value="{{ $settings['hotel_name'] }}">
            <input type="hidden" name="currency" value="{{ $settings['currency'] }}">
            <input type="hidden" name="tax_rate" value="{{ $settings['tax_rate'] }}">
            <input type="hidden" name="check_in_time" value="{{ $settings['check_in_time'] }}">
            <input type="hidden" name="check_out_time" value="{{ $settings['check_out_time'] }}">
            <input type="hidden" name="contact_email" value="{{ $settings['contact_email'] }}">
            <input type="hidden" name="contact_phone" value="{{ $settings['contact_phone'] }}">

            <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Testimonials</button>
            </div>
        </form>

        <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2.5rem 0;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--primary);">Custom Dynamic Pages</h4>
            <button type="button" class="btn btn-primary" onclick="openCreatePageModal()" style="font-size: 0.85rem; padding: 0.5rem 1.25rem;">
                <i class="fa-solid fa-plus"></i> Create New Page
            </button>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Show in Nav</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td><strong>{{ $page->title }}</strong></td>
                            <td><code>/pages/{{ $page->slug }}</code></td>
                            <td>
                                <span class="badge {{ $page->is_active ? 'badge-success' : 'badge-danger' }}" style="padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem;">
                                    {{ $page->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $page->show_in_nav ? 'badge-info' : 'badge-warning' }}" style="padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.75rem;">
                                    {{ $page->show_in_nav ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" class="btn btn-outline" onclick="openEditPageModal({{ json_encode($page) }})" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="triggerDeletePage('{{ route('super_admin.pages.delete', $page) }}')" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                    <a href="/pages/{{ $page->slug }}" target="_blank" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2.5rem 0;">
                                <i class="fa-regular fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: var(--text-secondary);"></i>
                                No custom pages created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: Coupons & Marketing (Promo coupons list and forms) -->
    <div id="tab-coupons" class="tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 2rem; align-items: start; margin-top: 1.5rem;">
            <!-- Create Coupon Form -->
            <div class="glass-panel" style="padding: 1.5rem; border-color: var(--border-color); height: fit-content;">
                <h3 style="font-size: 1.15rem; margin-bottom: 1.5rem; font-weight: 700;">
                    <i class="fa-solid fa-plus" style="color: var(--primary);"></i> Add Discount Coupon
                </h3>
                <form action="{{ route('super_admin.coupons.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @csrf
                    <div class="form-group">
                        <label for="coupon_code" class="form-label">Promo Code</label>
                        <input type="text" name="code" id="coupon_code" class="form-control" placeholder="e.g. STAYFLOW20" required style="text-transform: uppercase;">
                        <small style="color: var(--text-secondary); font-size: 0.75rem;">Uppercase letters and numbers only.</small>
                    </div>

                    <div class="form-group">
                        <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="discount_percentage" class="form-control" placeholder="e.g. 15.00" required>
                    </div>

                    <div class="form-group">
                        <label for="expire_at" class="form-label">Expiry Date</label>
                        <input type="date" name="expire_at" id="expire_at" class="form-control">
                        <small style="color: var(--text-secondary); font-size: 0.75rem;">Leave empty for perpetual validity.</small>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Active Coupon</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; justify-content: center;">
                        <i class="fa-solid fa-tag"></i> Create Promo Coupon
                    </button>
                </form>
            </div>

            <!-- Existing Coupons List -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-tags" style="color: var(--primary);"></i> Active System Promo Codes
                </h3>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td><strong style="color: var(--primary); font-family: monospace; font-size: 1.1rem;">{{ $coupon->code }}</strong></td>
                                    <td><strong>{{ number_format($coupon->discount_percentage, 1) }}%</strong></td>
                                    <td>
                                        @if($coupon->expire_at)
                                            <span style="font-size: 0.85rem; color: {{ $coupon->expire_at->isPast() ? 'var(--danger)' : 'var(--text-primary)' }}">
                                                {{ $coupon->expire_at->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="badge badge-muted">Perpetual</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->isValid())
                                            <span class="badge badge-success">Valid</span>
                                        @else
                                            <span class="badge badge-danger">Expired/Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" class="btn btn-outline" onclick="openEditCouponModal({{ json_encode($coupon) }})" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <form action="{{ route('super_admin.coupons.delete', $coupon) }}" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2.5rem 0;">
                                        <i class="fa-solid fa-tags" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: var(--text-secondary);"></i>
                                        No active discount coupons found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div class="modal" id="createPageModal">
    <div class="modal-content glass-panel" style="max-width: 650px;">
        <div class="modal-header">
            <h3 style="font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fa-solid fa-file-circle-plus" style="color: var(--primary);"></i> Create New Custom Page
            </h3>
            <button type="button" class="theme-toggle" onclick="closeCreatePageModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('super_admin.pages.store') }}" method="POST">
            @csrf
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label for="create_page_title" class="form-label">Page Title</label>
                    <input type="text" name="title" id="create_page_title" class="form-control" placeholder="e.g. Terms of Service" required oninput="generateSlug('create_page_title', 'create_page_slug')">
                </div>
                
                <div class="form-group">
                    <label for="create_page_slug" class="form-label">URL Slug</label>
                    <input type="text" name="slug" id="create_page_slug" class="form-control" placeholder="e.g. terms-of-service" required>
                    <small style="color: var(--text-secondary); font-size: 0.75rem;">Letters, numbers, and hyphens only. Unique URL segment.</small>
                </div>
                
                <div class="form-group">
                    <label for="create_page_content" class="form-label">Page Content</label>
                    <textarea name="content" id="create_page_content" class="form-control" rows="8" placeholder="Write page content in HTML or plain text..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="create_page_meta_title" class="form-label">SEO Meta Title</label>
                    <input type="text" name="meta_title" id="create_page_meta_title" class="form-control" placeholder="e.g. Terms of Service | StayFlow">
                </div>
                <div class="form-group">
                    <label for="create_page_meta_description" class="form-label">SEO Meta Description</label>
                    <textarea name="meta_description" id="create_page_meta_description" class="form-control" rows="2" placeholder="Brief SEO description..."></textarea>
                </div>
                <div class="form-group">
                    <label for="create_page_meta_keywords" class="form-label">SEO Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="create_page_meta_keywords" class="form-control" placeholder="e.g. terms, policy, stayflow">
                </div>
                
                <div style="display: flex; gap: 2rem; align-items: center; margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem;">
                        <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Publish Page (Active)
                    </label>
                    
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem;">
                        <input type="checkbox" name="show_in_nav" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Show in Navigation/Footer
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem; background: rgba(0,0,0,0.02);">
                <button type="button" class="btn btn-outline" onclick="closeCreatePageModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Page</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Page Modal -->
<div class="modal" id="editPageModal">
    <div class="modal-content glass-panel" style="max-width: 650px;">
        <div class="modal-header">
            <h3 style="font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fa-solid fa-file-pen" style="color: var(--secondary);"></i> Edit Custom Page
            </h3>
            <button type="button" class="theme-toggle" onclick="closeEditPageModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editPageForm" method="POST">
            @csrf
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label for="edit_page_title" class="form-label">Page Title</label>
                    <input type="text" name="title" id="edit_page_title" class="form-control" placeholder="e.g. Terms of Service" required oninput="generateSlug('edit_page_title', 'edit_page_slug')">
                </div>
                
                <div class="form-group">
                    <label for="edit_page_slug" class="form-label">URL Slug</label>
                    <input type="text" name="slug" id="edit_page_slug" class="form-control" placeholder="e.g. terms-of-service" required>
                    <small style="color: var(--text-secondary); font-size: 0.75rem;">Letters, numbers, and hyphens only. Unique URL segment.</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_page_content" class="form-label">Page Content</label>
                    <textarea name="content" id="edit_page_content" class="form-control" rows="8" placeholder="Write page content..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_page_meta_title" class="form-label">SEO Meta Title</label>
                    <input type="text" name="meta_title" id="edit_page_meta_title" class="form-control" placeholder="e.g. Terms of Service | StayFlow">
                </div>
                <div class="form-group">
                    <label for="edit_page_meta_description" class="form-label">SEO Meta Description</label>
                    <textarea name="meta_description" id="edit_page_meta_description" class="form-control" rows="2" placeholder="Brief SEO description..."></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_page_meta_keywords" class="form-label">SEO Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="edit_page_meta_keywords" class="form-control" placeholder="e.g. terms, policy, stayflow">
                </div>
                
                <div style="display: flex; gap: 2rem; align-items: center; margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem;">
                        <input type="checkbox" name="is_active" id="edit_page_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Publish Page (Active)
                    </label>
                    
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem;">
                        <input type="checkbox" name="show_in_nav" id="edit_page_show_in_nav" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Show in Navigation/Footer
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem; background: rgba(0,0,0,0.02);">
                <button type="button" class="btn btn-outline" onclick="closeEditPageModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Coupon Modal -->
<div class="modal" id="editCouponModal">
    <div class="modal-content glass-panel" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="font-weight: 700; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                <i class="fa-solid fa-tag" style="color: var(--secondary);"></i> Edit Coupon Code
            </h3>
            <button type="button" class="theme-toggle" onclick="closeEditCouponModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editCouponForm" method="POST">
            @csrf
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label for="edit_coupon_code" class="form-label">Promo Code</label>
                    <input type="text" name="code" id="edit_coupon_code" class="form-control" required style="text-transform: uppercase;">
                </div>
                
                <div class="form-group">
                    <label for="edit_coupon_percentage" class="form-label">Discount Percentage (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="edit_coupon_percentage" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_coupon_expiry" class="form-label">Expiry Date</label>
                    <input type="date" name="expire_at" id="edit_coupon_expiry" class="form-control">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-primary); font-size: 0.95rem;">
                        <input type="checkbox" name="is_active" id="edit_coupon_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Active Coupon
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem; background: rgba(0,0,0,0.02);">
                <button type="button" class="btn btn-outline" onclick="closeEditCouponModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deletePageForm" method="POST" style="display: none;">
    @csrf
</form>

@section('scripts')
<script>
    function switchTab(evt, tabId) {
        // Hide all tab contents
        const tabContents = document.getElementsByClassName("tab-content");
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = "none";
        }
        
        // Deactivate all tab buttons
        const tabBtns = document.getElementsByClassName("tab-btn");
        for (let i = 0; i < tabBtns.length; i++) {
            tabBtns[i].classList.remove("active");
            tabBtns[i].style.color = "var(--text-secondary)";
            tabBtns[i].style.borderBottomColor = "transparent";
        }
        
        // Show target tab content
        if (tabId === 'coupons') {
            document.getElementById("tab-marketing").style.display = "block";
            document.getElementById("tab-coupons").style.display = "block";
        } else {
            const targetTab = document.getElementById("tab-" + tabId);
            if (targetTab) {
                targetTab.style.display = "block";
            }
        }
        
        // Activate clicked tab button
        evt.currentTarget.classList.add("active");
        evt.currentTarget.style.color = "var(--primary)";
        evt.currentTarget.style.borderBottomColor = "var(--primary)";

        // Hide save button container in slider or CMS list tab where things are inline
        const saveBar = document.getElementById("save-button-container");
        if (tabId === 'slider' || tabId === 'cms') {
            saveBar.style.display = "none";
        } else {
            saveBar.style.display = "flex";
        }
    }

    function toggleSmsFields() {
        const provider = document.getElementById('sms_gateway_provider').value;
        document.querySelectorAll('.sms-provider-fields').forEach(el => {
            el.style.display = 'none';
        });
        if (provider !== 'disabled') {
            const activeFields = document.getElementById('sms-fields-' + provider);
            if (activeFields) {
                activeFields.style.display = 'block';
            }
        }
    }

    function generateSlug(titleId, slugId) {
        const titleVal = document.getElementById(titleId).value;
        const slugVal = titleVal.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById(slugId).value = slugVal;
    }

    // Modal Control
    function openCreatePageModal() {
        document.getElementById('createPageModal').classList.add('active');
    }

    function closeCreatePageModal() {
        document.getElementById('createPageModal').classList.remove('active');
    }

    function openEditPageModal(page) {
        const form = document.getElementById('editPageForm');
        form.action = '/super-admin/pages/' + page.id + '/update';
        document.getElementById('edit_page_title').value = page.title;
        document.getElementById('edit_page_slug').value = page.slug;
        document.getElementById('edit_page_content').value = page.content;
        document.getElementById('edit_page_meta_title').value = page.meta_title || '';
        document.getElementById('edit_page_meta_description').value = page.meta_description || '';
        document.getElementById('edit_page_meta_keywords').value = page.meta_keywords || '';
        document.getElementById('edit_page_active').checked = !!page.is_active;
        document.getElementById('edit_page_show_in_nav').checked = !!page.show_in_nav;
        document.getElementById('editPageModal').classList.add('active');
    }

    function closeEditPageModal() {
        document.getElementById('editPageModal').classList.remove('active');
    }

    function triggerDeletePage(deleteUrl) {
        if (confirm('Are you sure you want to delete this custom page?')) {
            const form = document.getElementById('deletePageForm');
            form.action = deleteUrl;
            form.submit();
        }
    }

    // Coupons Modals
    function openEditCouponModal(coupon) {
        const form = document.getElementById('editCouponForm');
        form.action = '/super-admin/coupons/' + coupon.id + '/update';
        document.getElementById('edit_coupon_code').value = coupon.code;
        document.getElementById('edit_coupon_percentage').value = coupon.discount_percentage;
        if (coupon.expire_at) {
            // format expire_at from ISO to YYYY-MM-DD
            const expDate = new Date(coupon.expire_at);
            const yyyy = expDate.getFullYear();
            let mm = expDate.getMonth() + 1;
            let dd = expDate.getDate();
            if (mm < 10) mm = '0' + mm;
            if (dd < 10) dd = '0' + dd;
            document.getElementById('edit_coupon_expiry').value = yyyy + '-' + mm + '-' + dd;
        } else {
            document.getElementById('edit_coupon_expiry').value = '';
        }
        document.getElementById('edit_coupon_active').checked = !!coupon.is_active;
        document.getElementById('editCouponModal').classList.add('active');
    }

    function closeEditCouponModal() {
        document.getElementById('editCouponModal').classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleSmsFields();
        
        // Click outside to close modals
        window.addEventListener('click', function(e) {
            const createModal = document.getElementById('createPageModal');
            const editModal = document.getElementById('editPageModal');
            const couponModal = document.getElementById('editCouponModal');
            if (e.target === createModal) closeCreatePageModal();
            if (e.target === editModal) closeEditPageModal();
            if (e.target === couponModal) closeEditCouponModal();
        });
    });
</script>
@endsection
@endsection
