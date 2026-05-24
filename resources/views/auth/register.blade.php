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
                    {!! \App\Models\Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria') !!}
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
