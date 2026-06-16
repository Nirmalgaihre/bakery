<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // Note: If you use Spatie Laravel-Permission, update models here accordingly.
    public function index()
    {
        // Fetching system roles dynamically
        $roles = DB::table('roles')->get(); 
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = DB::table('permissions')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        DB::transaction(function () use ($request) {
            $roleId = DB::table('roles')->insertGetId([
                'name'       => $request->name,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($request->permissions)) {
                foreach ($request->permissions as $permissionId) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id'       => $roleId
                    ]);
                }
            }
    });

        return redirect()->route('admin.roles.index')->with('success', 'Security privileges role configured successfully.');
    }

    public function edit($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();
        $permissions = DB::table('permissions')->get();
        $attachedPermissions = DB::table('role_has_permissions')
                                 ->where('role_id', $id)
                                 ->pluck('permission_id')
                                 ->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'attachedPermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array'
        ]);

        DB::transaction(function () use ($request, $id) {
            DB::table('roles')->where('id', $id)->update([
                'name'       => $request->name,
                'updated_at' => now(),
            ]);

            // Sync matrix nodes
            DB::table('role_has_permissions')->where('role_id', $id)->delete();

            if (!empty($request->permissions)) {
                foreach ($request->permissions as $permissionId) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id'       => $id
                    ]);
                }
            }
    });

        return redirect()->route('admin.roles.index')->with('success', 'Privileges map altered securely.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            DB::table('role_has_permissions')->where('role_id', $id)->delete();
            DB::table('roles')->where('id', $id)->delete();
        });

        return redirect()->route('admin.roles.index')->with('success', 'Security role removed from ecosystem.');
    }
}