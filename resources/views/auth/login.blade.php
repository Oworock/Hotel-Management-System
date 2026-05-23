<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Aetheria HMS</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    {!! \App\Models\Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria') !!}
                @endif
            </a>
            <p style="color: var(--text-secondary);">Premium Hotel Management System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="name@hotel.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
                <label class="form-checkbox" style="display: flex; align-items: center; gap: 0.25rem;">
                    <input type="checkbox" name="remember">
                    <span style="font-size: 0.875rem; color: var(--text-secondary);">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" style="font-size: 0.875rem; font-weight: 500;">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                Sign In
            </button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.875rem; color: var(--text-secondary);">
            Don't have an account? <a href="{{ route('register') }}" style="font-weight: 600;">Create Guest Account</a>
        </div>

        <!-- Testing Credentials Helper Quick Fill Grid -->
        <div style="margin-top: 2.5rem; border-top: 1px dashed var(--border-color); padding-top: 1.5rem;">
            <p style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem; text-align: center; letter-spacing: 0.05em;">
                Quick Demo Accounts (Password: <span style="font-family: monospace;">password</span>)
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('super@hotel.com')">
                    Super Admin
                </button>
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('admin@hotel.com')">
                    Admin
                </button>
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('receptionist@hotel.com')">
                    Receptionist
                </button>
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('chef@hotel.com')">
                    Kitchen Chef
                </button>
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('staff1@hotel.com')">
                    Staff Shift
                </button>
                <button type="button" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.4rem 0.5rem;" onclick="prefill('customer@hotel.com')">
                    Guest Book
                </button>
            </div>
        </div>
    </div>

    <script>
        // Set Theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function prefill(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
