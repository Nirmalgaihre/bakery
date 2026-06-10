<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm() {
        return view('auth.login');
    }

    // Step 1: Handle password check & internal code creation
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();

            // Strict authorization checkpoint
            if (!$user->is_admin) {
                throw ValidationException::withMessages([
                    'email' => __('Access Denied. Unauthorized terminal entry.'),
                ]);
            }

            // Generate secure offline verification code
            $otp = random_int(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            // Store user id and code directly into the local session for easy development reading
            session([
                'otp_user_id' => $user->id,
                'dev_otp_bypass' => $otp // <-- Storing here so we can see it on the screen
            ]);

            return redirect()->route('otp.view');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our corporate records.'),
        ]);
    }

    public function showOtpForm() {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    // Step 2: Validate the code challenge
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $user = User::findOrFail($userId);

        // Security check: Match code structure & lifetime expiration
        if ($user->otp_code !== $request->otp || Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'The token is invalid or expired.']);
        }

        // Clean database state instantly 
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Establish the authorized session
        Auth::login($user);
        $request->session()->regenerate();
        
        // Clean session memory cache
        $request->session()->forget(['otp_user_id', 'dev_otp_bypass']);

        return redirect()->intended('/inventory/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}