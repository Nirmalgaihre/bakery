<?php

namespace App\Http\Controllers\Admin; // <-- Make sure this matches exactly!

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Your code to return the layout view
        return view('admin.dashboard'); 
    }
}