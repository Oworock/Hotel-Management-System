<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Aetheria HMS</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('auth.partials.theme-vars')
</head>
<body class="auth-page">
    <div class="glass-panel auth-card animate-fade-in">
        <div class="auth-header">
            <a href="/" class="auth-logo" style="text-decoration: none;">
                @php
                    $logoType = \App\Models\Setting::getValue('logo_type', 'text');
                    $logoImage = \App\Models\Setting::getValue('logo_image');
                @endphp
                @if($logoType === 'image' && !empty($logoImage))
                    <img src="{{ $logoImage }}" alt="{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria') }}" style="max-height: 50px; object-fit: contain; display: inline-block;">
                @else
                    {!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria')) !!}
                @endif
            </a>
            <p style="color: var(--text-secondary);">Register Guest Account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="display: flex; flex-direction: column;">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="john@domain.com" value="{{ old('email') }}" required>
            </div>

            @include('partials.phone-input', ['field' => 'phone', 'label' => 'Mobile Number', 'value' => old('phone'), 'required' => false])

            <details style="margin-top: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.9rem; background: rgba(255,255,255,0.03);">
                <summary style="cursor: pointer; font-weight: 700; color: var(--text-primary);">Optional stay profile</summary>
                <p style="margin: 0.75rem 0 1rem; color: var(--text-secondary); font-size: 0.85rem; line-height: 1.55;">You can skip this now. These details will be required before your first room booking.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <select name="title" id="title" class="form-control form-select">
                            <option value="">Select title</option>
                            @foreach(['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof'] as $title)
                                <option value="{{ $title }}" {{ old('title') === $title ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control form-select">
                            <option value="">Prefer not to say</option>
                            @foreach(['female' => 'Female', 'male' => 'Male', 'non_binary' => 'Non-binary', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" {{ old('gender') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                    </div>
                    <div class="form-group">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality') }}" placeholder="e.g. Nigerian">
                    </div>
                </div>

                <div class="form-group">
                    <label for="country_of_residence" class="form-label">Country of Residence</label>
                    <input type="text" name="country_of_residence" id="country_of_residence" class="form-control" value="{{ old('country_of_residence') }}">
                </div>

                <div class="form-group">
                    <label for="address_line1" class="form-label">Residential Address</label>
                    <input type="text" name="address_line1" id="address_line1" class="form-control" value="{{ old('address_line1') }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                    </div>
                    <div class="form-group">
                        <label for="state" class="form-label">State / Province</label>
                        <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="id_type" class="form-label">Identification Type</label>
                        <select name="id_type" id="id_type" class="form-control form-select">
                            <option value="">Select ID type</option>
                            @foreach(['passport' => 'Passport', 'national_id' => 'National ID', 'drivers_license' => 'Driver License', 'residence_permit' => 'Residence Permit', 'voter_card' => 'Voter Card', 'other' => 'Other Government ID'] as $value => $label)
                                <option value="{{ $value }}" {{ old('id_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_number" class="form-label">Identification Number</label>
                        <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                </div>

                <div class="form-group">
                    <label for="emergency_contact_relationship" class="form-label">Emergency Contact Relationship</label>
                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control" value="{{ old('emergency_contact_relationship') }}">
                </div>

                @include('partials.phone-input', ['field' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'value' => old('emergency_contact_phone'), 'required' => false])

                <label style="display:flex;align-items:flex-start;gap:0.65rem;margin-top:0.5rem;color:var(--text-secondary);font-size:0.86rem;line-height:1.5;">
                    <input type="checkbox" name="marketing_consent" value="1" {{ old('marketing_consent') ? 'checked' : '' }} style="margin-top:0.2rem;">
                    <span>Send me stay updates, direct-booking offers, and hotel news.</span>
                </label>
            </details>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                Create Account
            </button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.875rem; color: var(--text-secondary);">
            Already have an account? <a href="{{ route('login') }}" style="font-weight: 600;">Sign In</a>
        </div>
    </div>

    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
