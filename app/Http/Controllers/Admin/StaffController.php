<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Spatie\Permission\Models\Role; 
use Spatie\Permission\PermissionRegistrar;
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
        // Fetch valid system roles
        $roles = class_exists(Role::class) 
            ? Role::whereIn('name', ['admin', 'accountant'])->get() 
            : [];

        return view('admin.staff.create', compact('roles'));
    }

    /**
     * Store a newly created staff operator inside database & dispatch notification email.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,accountant'
        ]);

        // 2. Create User
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'], // Plain string database column
        ]);

        // 3. Ensure Spatie Role Exists and Assign it with explicit guard
        if (class_exists(Role::class)) {
            $spatieRole = Role::firstOrCreate([
                'name'       => $validated['role'],
                'guard_name' => 'web'
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole($spatieRole);
            }

            // 4. Force clear Spatie cache so permission updates take effect instantly
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        // 5. Send Credentials via PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls') === 'ssl' 
                                ? PHPMailer::ENCRYPTION_SMTPS 
                                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = env('MAIL_PORT', 587);

            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($user->email, $user->name);

            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Deurali Chemicals Portal';
            
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #334155; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                    <h2>Hello, {$user->name}!</h2>
                    <p>Your account has been created. Use the following credentials to access the system:</p>
                    <div style='background: #f8fafc; padding: 15px; border-radius: 6px;'>
                        <p><strong>Email:</strong> {$user->email}</p>
                        <p><strong>Password:</strong> {$validated['password']}</p>
                        <p><strong>Role:</strong> " . strtoupper($validated['role']) . "</p>
                    </div>
                    <p style='color: red;'><strong>Security Tip:</strong> Please log in and change your password immediately.</p>
                    <p>Login URL: <a href='" . url('/login') . "'>" . url('/login') . "</a></p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            logger("PHPMailer Error: {$mail->ErrorInfo}");
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff added and mail sent.');
    }

    /**
     * Show the form for editing the specified staff profile.
     */
    public function edit($id)
    {
        $staff = User::findOrFail($id);
        $roles = class_exists(Role::class) 
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
            'role'     => 'required|in:admin,accountant'
        ]);

        $staff->name  = $validated['name'];
        $staff->email = $validated['email'];
        $staff->role  = $validated['role'];

        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }
        $staff->save();

        // Ensure Spatie role exists and sync
        if (class_exists(Role::class)) {
            $spatieRole = Role::firstOrCreate([
                'name'       => $validated['role'],
                'guard_name' => 'web'
            ]);

            if (method_exists($staff, 'syncRoles')) {
                $staff->syncRoles([$spatieRole]);
            }

            // Force clear Spatie cache
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
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

        // Clear cached permissions on deletion
        if (class_exists(Role::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff system account terminated.');
    }
}