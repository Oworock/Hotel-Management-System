<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            $intendedUrl = $request->session()->pull('url.intended');
            if ($user->role === 'customer' && $intendedUrl) {
                return redirect()->to($intendedUrl);
            }

            return $this->redirectUserByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectUserByRole($user)
    {
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('super_admin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'receptionist':
                return redirect()->route('receptionist.dashboard');
            case 'kitchen_manager':
                return \Illuminate\Support\Facades\Route::has('kitchen.dashboard') 
                    ? redirect()->route('kitchen.dashboard') 
                    : redirect()->route('home');
            case 'tuck_shop_manager':
            case 'restaurant_manager':
                return \Illuminate\Support\Facades\Route::has('admin.products') 
                    ? redirect()->route('admin.products') 
                    : redirect()->route('home');
            case 'staff':
                return redirect()->route('staff.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
                if ($user->hasFunction('manage_channel_manager') && \Illuminate\Support\Facades\Route::has('admin.channel_manager.index')) {
                    return redirect()->route('admin.channel_manager.index');
                }
                if ($user->hasFunction('kitchen_dashboard') && \Illuminate\Support\Facades\Route::has('kitchen.dashboard')) {
                    return redirect()->route('kitchen.dashboard');
                }
                if ($user->hasFunction('manage_bookings')) {
                    return redirect()->route('receptionist.dashboard');
                }
                if (($user->hasFunction('manage_tuck_shop') || $user->hasFunction('manage_restaurant')) && \Illuminate\Support\Facades\Route::has('admin.products')) {
                    return redirect()->route('admin.products');
                }
                if ($user->hasFunction('manage_rooms')) {
                    return redirect()->route('admin.rooms');
                }
                if ($user->hasFunction('manage_guests')) {
                    return redirect()->route('admin.guests');
                }
                if ($user->hasFunction('manage_payments')) {
                    return redirect()->route('admin.payments');
                }
                if ($user->hasFunction('manage_reports')) {
                    return redirect()->route('admin.reports');
                }
                if ($user->hasFunction('manage_users')) {
                    return redirect()->route('admin.users');
                }
                if ($user->hasFunction('manage_settings')) {
                    return redirect()->route('admin.settings');
                }
                return redirect()->route('customer.dashboard');
        }
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Simulating email dispatch
        return back()->with('success', 'A password reset link has been dispatched to your email address (Simulated Feedback).');
    }
}
