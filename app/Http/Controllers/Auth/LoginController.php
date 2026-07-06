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

// --- PLACE YOUR REQUIRE_ONCE STATEMENTS HERE ---
require_once app_path('Libraries/PHPMailer/Exception.php');
require_once app_path('Libraries/PHPMailer/PHPMailer.php');
require_once app_path('Libraries/PHPMailer/SMTP.php');

class LoginController extends Controller
{
    public function showLoginForm() { return view('auth.login'); }
public function login(Request $request)
{
    // 1. Rate Limiting (Prevent Brute Force)
    $throttleKey = 'login-attempt:' . $request->ip();
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        return back()->withErrors(['email' => 'Too many attempts. Please try again later.']);
    }

    $request->validate(['email' => 'required|email', 'password' => 'required']);

    // 2. Auth::validate uses Hashed passwords and secure bindings
    if (Auth::validate(['email' => $request->email, 'password' => $request->password])) {
        
        // Clear rate limiter on success
        RateLimiter::clear($throttleKey);

        // 3. Regenerate session to prevent fixation
        $request->session()->regenerate();

        $otp = random_int(100000, 999999); // Use random_int for crypto-secure numbers
        
        session([
            'otp' => $otp,
            'otp_email' => $request->email, // Use unique keys
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        $this->sendOtpEmail($request->email, $otp);
        return redirect()->route('otp.view')->with('status', 'Verification code sent.');
    }

    // Record failed attempt
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
        $mail->Subject = 'Security Verification Code';
        $mail->Body    = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
    <div style='background-color: #2c3e50; padding: 20px; text-align: center; color: #ffffff;'>
        <h2 style='margin: 0;'>Deurali Chemicals</h2>
    </div>
    <div style='padding: 30px;'>
        <p style='font-size: 16px; color: #333;'>Hello,</p>
        <p style='font-size: 16px; color: #333;'>We received a request to verify your account. Please use the following code to complete your verification:</p>
        
        <div style='text-align: center; margin: 30px 0;'>
            <span style='font-size: 32px; font-weight: bold; color: #2c3e50; background: #f4f4f4; padding: 10px 25px; border-radius: 5px; letter-spacing: 5px;'>{$otp}</span>
        </div>
        
        <p style='font-size: 14px; color: #777;'>This code will expire in <strong>10 minutes</strong>. If you did not initiate this request, please ignore this email or contact support immediately.</p>
    </div>
    <div style='background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 12px; color: #999;'>
        &copy; " . date("Y") . " Deurali Chemicals. All rights reserved.
    </div>
</div>";
        $mail->send();
    }

    public function showOtpForm() { return view('auth.otp'); }

    public function verifyOtp(Request $request)
    {
        if ($request->otp == session('otp') && now()->lt(session('otp_expires_at'))) {
            Auth::login(User::where('email', session('email'))->first());
            session()->forget(['otp', 'email', 'otp_expires_at']);
            return redirect('/admin/dashboard');
        }
        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }
    public function showForgotPasswordForm() {
    return view('auth.forgot-password');
}
public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    $token = Str::random(60);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => Carbon::now()]
    );

    $resetLink = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

    $this->sendMail($request->email, 'Reset Your Password', 
        "Click here to reset your password: <a href='{$resetLink}'>{$resetLink}</a>");

    // अब सिधै auth फोल्डर भित्रको फाइल कल गर्छ
    return view('auth.email-sent'); 
}

// Reusable Mailer Method
private function sendMail($to, $subject, $body)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = env('MAIL_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = env('MAIL_USERNAME');
    $mail->Password   = env('MAIL_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Changed for port 587
    $mail->Port       = env('MAIL_PORT');
    
    $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();
}

// १. रिसेट पेज देखाउने विधि
public function showResetForm($token) {
    return view('auth.reset-password', ['token' => $token]);
}
public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
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