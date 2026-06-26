<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // प्रोफाइल देखाउने
    public function edit() {
        return view('admin.profile.edit', ['user' => Auth::user()]);
    }

    // प्रोफाइल अपडेट गर्ने
    public function update(Request $request) {
        $user = Auth::user();
        $user->update($request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]));
        return back()->with('success', 'Profile updated successfully!');
    }

    // पासवर्ड परिवर्तनको पेज
    public function passwordEdit() {
        return view('admin.profile.password');
    }

    // पासवर्ड अपडेट गर्ने
    public function passwordUpdate(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Old password does not match.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated successfully!');
    }
}