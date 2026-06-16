<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Spatie\Permission\Models\Role; 
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff operators.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $staffs = $query->latest()->paginate(15);
        return view('admin.staff.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new staff operator.
     */
    public function create()
    {
        // Only fetch admin and accountant roles if Spatie is used, else fallback to empty array
        $roles = class_exists(\Spatie\Permission\Models\Role::class) 
            ? Role::whereIn('name', ['admin', 'accountant'])->get() 
            : [];

        return view('admin.staff.create', compact('roles'));
    }

    /**
     * Store a newly created staff operator inside database & dispatch notification email.
     */
    public function store(Request $request)
    {
        // Validating payload with strict group access rules
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,accountant' // Restricts input to admin and accountant only
        ]);

        // Creating User Node
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assigning Role via Spatie or custom mechanism safely
        if (!empty($validated['role']) && method_exists($user, 'assignRole')) {
            $user->assignRole($validated['role']);
        }

        // ======================================================================
        // NATIVE PHPMAILER ENGINE DISPATCH PIPELINE
        // ======================================================================
        $mail = new PHPMailer(true);

        try {
            // SMTP Server Connection Profiles Configuration
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.mailtrap.io');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = env('MAIL_PORT', 587);

            // Sender & Receiver Identity Headers Setup
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'system@portal.com'), env('MAIL_FROM_NAME', 'ERP System'));
            $mail->addAddress($user->email, $user->name);

            // Content Specifications Nodes
            $mail->isHTML(true);
            $mail->Subject = 'Secure Portal Profile Activated - Welcome to Staff Directory';
            
            // Clean Inline UI Framework Render Body
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #334155; padding: 20px; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                    <h2 style='color: #0f172a; font-size: 18px; margin-bottom: 10px;'>Hello, {$user->name}!</h2>
                    <p style='font-size: 13px; line-height: 1.5;'>Your management system operator login node has been established successfully by the master console administrator.</p>
                    <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 12px;'><strong>Access Node Email:</strong> {$user->email}</p>
                        <p style='margin: 5px 0 0 0; font-size: 12px;'><strong>Assigned Baseline Role:</strong> " . strtoupper($validated['role']) . "</p>
                    </div>
                    <p style='font-size: 12px; color: #64748b;'>Please utilize your designated credentials to authenticate directly via the secured administrative routing platform gateway.</p>
                    <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                    <p style='font-size: 11px; color: #94a3b8; text-align: center;'>Automated Notification System Node - Do Not Direct Reply.</p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Logs system runtime transmission error failure contexts without throwing 500 error
            logger("PHPMailer Execution Engine Fault Traces: {$mail->ErrorInfo}");
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff operator registered, validation mail successfully dispatched.');
    }

    /**
     * Show the form for editing the specified staff profile.
     */
    public function edit($id)
    {
        $staff = User::findOrFail($id);
        $roles = class_exists(\Spatie\Permission\Models\Role::class) 
            ? Role::whereIn('name', ['admin', 'accountant'])->get() 
            : [];

        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified staff operator record inside database.
     */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:admin,accountant' // Restricts modification rules too
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];

        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }
        $staff->save();

        if (isset($validated['role']) && method_exists($staff, 'syncRoles')) {
            $staff->syncRoles($validated['role']);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff profile updated successfully.');
    }

    /**
     * Remove the specified staff operator account entirely.
     */
    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff system account terminated.');
    }
}