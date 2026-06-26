<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            $otp = rand(100000, 999999);
            session(['otp' => $otp, 'email' => $request->email, 'otp_expires_at' => now()->addMinutes(10)]);

            $this->sendOtpEmail($request->email, $otp);
            return redirect()->route('otp.view')->with('status', 'Verification code sent.');
        }
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
        $mail->Body    = "Your Deurali Chemicals verification code is: <b>{$otp}</b>. Expires in 10 minutes.";
        $mail->send();
    }

    public function showOtpForm() { return view('auth.otp'); }

    public function verifyOtp(Request $request)
    {
        if ($request->otp == session('otp') && now()->lt(session('otp_expires_at'))) {
            Auth::login(User::where('email', session('email'))->first());
            session()->forget(['otp', 'email', 'otp_expires_at']);
            return redirect('/dashboard');
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