<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Hash;

// --- PHPMailer requires ---
require_once app_path('Libraries/PHPMailer/Exception.php');
require_once app_path('Libraries/PHPMailer/PHPMailer.php');
require_once app_path('Libraries/PHPMailer/SMTP.php');

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $throttleKey = 'login-attempt:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['email' => 'Too many attempts. Please try again later.']);
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if (Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $otp = random_int(100000, 999999);

            session([
                'otp' => $otp,
                'otp_email' => $request->email,
                'otp_expires_at' => now()->addMinutes(10),
                'otp_verified' => false, // newly added flag
            ]);

            $this->sendOtpEmail($request->email, $otp);
            return redirect()->route('otp.view')->with('status', 'Verification code sent.');
        }

        RateLimiter::hit($throttleKey, 60);
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    private function sendOtpEmail($email, $otp)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = config('mail.mailers.smtp.host');
    $mail->SMTPAuth   = true;
    $mail->Username   = config('mail.mailers.smtp.username');
    $mail->Password   = config('mail.mailers.smtp.password');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Deurali Chemicals Account';
    
    $mail->Body = "
    <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; max-width: 500px; margin: 40px auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;'>
        <!-- Header -->
        <div style='background-color: #0f172a; padding: 30px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 20px; letter-spacing: 0.5px;'>Deurali Chemicals</h1>
        </div>
        
        <!-- Content -->
        <div style='padding: 40px;'>
            <h2 style='color: #1e293b; margin-top: 0;'>Verification Code</h2>
            <p style='color: #64748b; line-height: 1.6;'>Hello,</p>
            <p style='color: #64748b; line-height: 1.6;'>You are attempting to sign in to your Deurali Chemicals account. Please use the verification code below to proceed.</p>
            
            <div style='text-align: center; margin: 35px 0;'>
                <div style='display: inline-block; font-size: 32px; font-weight: 800; color: #0f172a; background: #f8fafc; padding: 15px 30px; border-radius: 8px; border: 2px dashed #cbd5e1; letter-spacing: 8px;'>{$otp}</div>
            </div>
            
            <p style='color: #64748b; font-size: 14px; line-height: 1.6;'>This code is valid for <strong>10 minutes</strong>. For your security, <strong>do not share this code</strong> with anyone.</p>
            
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9;'>
                <p style='color: #94a3b8; font-size: 13px;'>If you did not request this verification, please ignore this email. Your account remains secure and no changes have been made.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style='background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;'>
            &copy; " . date('Y') . " Deurali Chemicals Pvt Ltd. All rights reserved.
        </div>
    </div>";

    $mail->send();
}

    public function showOtpForm()
    {
        // Prevent direct access without OTP session
        if (!session('otp') || !session('otp_email') || !session('otp_expires_at')) {
            return redirect()->route('login')->withErrors(['otp' => 'Unauthorized access.']);
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
    if (!session('otp') || !session('otp_email') || !session('otp_expires_at')) {
        return redirect()->route('login')->withErrors(['otp' => 'Unauthorized access. Please login again.']);
    }

    $request->validate([
        'otp' => 'required|digits:6|numeric'
    ], [
        'otp.required' => 'Please enter the verification code.',
        'otp.digits' => 'OTP must be exactly 6 digits.',
        'otp.numeric' => 'OTP must contain only numbers.'
    ]);

    if ($request->otp == session('otp') && now()->lt(session('otp_expires_at'))) {
        $user = User::where('email', session('otp_email'))->first();

        if ($user) {
            Auth::login($user);
            
            session()->forget(['otp', 'otp_email', 'otp_expires_at']);
            
            return redirect('/admin/dashboard')->with('status', 'Login successful! Welcome back.');
        }
    }

    return redirect()->route('otp.view')->withErrors(['otp' => 'Invalid or expired OTP. Please try again.'])->withInput();
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    // SECURE: Always return success, even if user doesn't exist.
    // This prevents attackers from finding valid accounts (Account Enumeration).
    if ($user) {
        $token = Str::random(60);
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()]
        );

        $resetLink = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        // Integrated professional template
        $body = "
        <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; max-width: 500px; margin: 40px auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;'>
            <div style='background-color: #0f172a; padding: 30px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0; font-size: 20px;'>Deurali Chemicals</h1>
            </div>
            <div style='padding: 40px;'>
                <h2 style='color: #1e293b; margin-top: 0;'>Password Reset Request</h2>
                <p style='color: #64748b; line-height: 1.6;'>We received a request to reset the password for your Deurali Chemicals account. If you did not make this request, please ignore this email.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' style='background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Reset Password</a>
                </div>

                <p style='color: #64748b; font-size: 13px;'>This link will expire in 30 minutes. If the button doesn't work, copy and paste this link into your browser:</p>
                <p style='color: #3b82f6; font-size: 12px; word-break: break-all;'>{$resetLink}</p>
            </div>
            <div style='background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;'>
                &copy; " . date('Y') . " Deurali Chemicals Pvt Ltd. All rights reserved.
            </div>
        </div>";

        $this->sendMail($request->email, 'Reset Your Deurali Chemicals Password', $body);
    }

    return view('auth.email-sent');
}

    private function sendMail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = env('MAIL_PORT');
        
        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
    }

    public function showResetForm($token)
    {
        $email = request('email');
        $record = DB::table('password_resets')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$record) {
            abort(403, 'Invalid or expired reset link.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}