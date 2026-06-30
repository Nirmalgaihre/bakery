<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReleaseNote;
use Illuminate\Http\Request;

class ReleaseNoteController extends Controller
{
    public function index() {
        $notes = ReleaseNote::latest()->get();
        return view('admin.release-notes.index', compact('notes'));
    }

    public function store(Request $request) {
        ReleaseNote::create($request->validate([
            'version' => 'required',
            'features' => 'nullable',
            'fixes' => 'nullable'
        ]));
        return redirect()->route('admin.release-notes.index')->with('success', 'Note added!');
    }
}