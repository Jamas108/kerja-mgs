<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::all();
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('admin.divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        Division::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.divisions.index')->with('success', 'Division created successfully');
    }

    public function edit(Division $division)
    {
        return view('admin.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
        ]);

        $division->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.divisions.index')->with('success', 'Division updated successfully');
    }

    public function destroy(Division $division)
    {
        // Check if there are users in this division
        if ($division->users()->count() > 0) {
            return redirect()->route('admin.divisions.index')->with('error', 'Cannot delete division with associated users');
        }

        $division->delete();

        return redirect()->route('admin.divisions.index')->with('success', 'Division deleted successfully');
    }
}