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
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body style='margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif;'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%' style='min-height: 100vh; padding: 40px 20px;'>
        <tr>
            <td align='center' valign='middle'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 520px; background-color: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);'>
                    
                    <!-- Header -->
                    <tr>
                        <td style='padding: 36px 36px 24px 36px; text-align: center; border-bottom: 1px solid #334155;'>
                            <img src='https://deuralichemicals.com.np/storage/img/dcl.png' alt='Deurali Chemicals Logo' style='height: 48px; width: auto; margin-bottom: 12px; display: inline-block;' />
                            <h1 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.025em;'>Deurali Chemicals</h1>
                            <p style='color: #60a5fa; margin: 4px 0 0 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;'>Security Verification</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style='padding: 36px;'>
                            <h2 style='color: #f8fafc; margin-top: 0; font-size: 18px; font-weight: 600;'>Your Verification Code</h2>
                            <p style='color: #94a3b8; line-height: 1.6; font-size: 14px; margin-bottom: 24px;'>
                                Hello,<br><br>
                                You are attempting to sign in to your Deurali Chemicals portal account. Please use the verification code below to authorize your session:
                            </p>

                            <!-- OTP Box -->
                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <td align='center' style='padding: 12px 0 28px 0;'>
                                        <div style='display: inline-block; font-family: Monospace, -apple-system, BlinkMacSystemFont, sans-serif; font-size: 32px; font-weight: 800; color: #38bdf8; background-color: #0f172a; padding: 18px 36px; border-radius: 12px; border: 1px dashed #38bdf8; letter-spacing: 10px; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.4);'>
                                            {$otp}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style='color: #94a3b8; font-size: 13px; line-height: 1.5; margin-bottom: 20px;'>
                                This code is valid for <strong style='color: #f8fafc;'>10 minutes</strong>. For account security, <strong style='color: #f8fafc;'>never share this code</strong> with anyone.
                            </p>

                            <div style='border-top: 1px solid #334155; padding-top: 20px; margin-top: 24px;'>
                                <p style='color: #64748b; font-size: 12px; line-height: 1.5; margin: 0;'>
                                    If you did not initiate this request, you can safely ignore this email. Your credentials remain safe and no unauthorized changes were made.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #0f172a; padding: 20px; text-align: center; border-top: 1px solid #334155;'>
                            <p style='color: #64748b; font-size: 12px; margin: 0;'>
                                &copy; " . date('Y') . " Deurali Chemicals Pvt Ltd. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>";

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

            // *** FIX ***
            // Previously this always did: redirect('/admin/dashboard')
            // That route is role:admin only, so every accountant login
            // succeeded here and then got bounced with a 403
            // "User does not have the right roles." on the very next request.
            // Redirect based on the user's actual role instead.
            return redirect($this->redirectPathForUser($user))
                ->with('status', 'Login successful! Welcome back.');
        }
    }

    return redirect()->route('otp.view')->withErrors(['otp' => 'Invalid or expired OTP. Please try again.'])->withInput();
    }

    /**
     * Decide the post-login landing URL based on the user's role.
     *
     * Dynamic: reads the role straight from Spatie (hasRole), so any
     * new staff member created via the Staff form works immediately —
     * no manual php artisan tinker fix-ups required.
     */
    private function redirectPathForUser(User $user): string
    {
        if ($user->hasRole('admin')) {
            return '/admin/dashboard';
        }

        if ($user->hasRole('accountant')) {
            // Accountants don't have route-level access to /admin/dashboard.
            // Send them to a route inside the shared admin+accountant zone instead.
            return route('admin.invoices.index');
        }

        // Fallback for any future role not yet wired to a landing page.
        return '/login';
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    // SECURE: Always return success, even if user doesn't exist to prevent account enumeration.
    if ($user) {
        // Plain raw token for the email link
        $token = Str::random(60);
        
        // Save SHA-256 hashed version in DB for safe string matching
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => hash('sha256', $token), 
                'created_at' => Carbon::now()
            ]
        );

        $resetLink = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        // Professional Email Body matching new design system
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif;'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='min-height: 100vh; padding: 40px 20px;'>
                <tr>
                    <td align='center' valign='middle'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 520px; background-color: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);'>
                            <!-- Header -->
                            <tr>
                                <td style='padding: 36px 36px 24px 36px; text-align: center; border-bottom: 1px solid #334155;'>
                                    <img src='https://deuralichemicals.com.np/storage/img/dcl.png' alt='Deurali Chemicals Logo' style='height: 48px; width: auto; margin-bottom: 12px; display: inline-block;' />
                                    <h1 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; tracking-tight: -0.025em;'>Deurali Chemicals</h1>
                                    <p style='color: #60a5fa; margin: 4px 0 0 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;'>Enterprise Portal</p>
                                </td>
                            </tr>
                            <!-- Content -->
                            <tr>
                                <td style='padding: 36px;'>
                                    <h2 style='color: #f8fafc; margin-top: 0; font-size: 18px; font-weight: 600;'>Password Reset Request</h2>
                                    <p style='color: #94a3b8; line-height: 1.6; font-size: 14px; margin-bottom: 28px;'>
                                        We received a request to reset the password for your Deurali Chemicals portal account. Click the button below to establish new credentials:
                                    </p>
                                    
                                    <!-- Button -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                        <tr>
                                            <td align='center' style='padding-bottom: 28px;'>
                                                <a href='{$resetLink}' target='_blank' style='background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px; display: inline-block; shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);'>Reset Password</a>
                                            </td>
                                        </tr>
                                    </table>

                                    <div style='border-top: 1px solid #334155; padding-top: 20px;'>
                                        <p style='color: #64748b; font-size: 12px; line-height: 1.5; margin-bottom: 8px;'>
                                            This reset link is valid for <strong>30 minutes</strong>. If you did not initiate this request, you can safely disregard this message.
                                        </p>
                                        <p style='color: #64748b; font-size: 11px; line-height: 1.4; word-break: break-all; margin: 0;'>
                                            Direct URL: <a href='{$resetLink}' style='color: #60a5fa; text-decoration: underline;'>{$resetLink}</a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #0f172a; padding: 20px; text-align: center; border-top: 1px solid #334155;'>
                                    <p style='color: #64748b; font-size: 12px; margin: 0;'>
                                        &copy; " . date('Y') . " Deurali Chemicals Pvt Ltd. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

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