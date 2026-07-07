<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        // Logic to fetch deleted items (e.g., using SoftDeletes)
        return view('admin.trash.index');
    }
}