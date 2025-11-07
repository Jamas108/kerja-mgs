<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    /**
     * Display a listing of the director's signatures.
     */
    public function index()
    {
        $signatures = Signature::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('director.signatures.index', compact('signatures'));
    }

    /**
     * Store a newly created signature in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'signature_data' => 'required|string',
        ]);

        // Decode base64 image
        $imageData = $request->signature_data;
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'signatures/' . Str::uuid() . '.png';

        // Save image to storage
        Storage::disk('public')->put($imageName, base64_decode($image));

        Signature::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'image_path' => $imageName,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified signature.
     */
    public function edit(Signature $signature)
    {
        if ($signature->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Solution 1: Add this line to pass $directors to the view
        $directors = User::where('role_id', 'director')->orWhere('id', $signature->user_id)->get();

        return view('director.signatures.edit', compact('signature', 'directors'));
    }

    /**
     * Update the specified signature in storage.
     */
    public function update(Request $request, Signature $signature)
    {
        if ($signature->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:100',
        ]);

        $data = [
            'title' => $request->title,
            'is_active' => $request->has('is_active'),
        ];

        // Update image if new signature provided
        if ($request->filled('signature_data')) {
            // Delete old image
            if ($signature->image_path && Storage::disk('public')->exists($signature->image_path)) {
                Storage::disk('public')->delete($signature->image_path);
            }

            // Save new image
            $imageData = $request->signature_data;
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'signatures/' . Str::uuid() . '.png';

            Storage::disk('public')->put($imageName, base64_decode($image));
            $data['image_path'] = $imageName;
        }

        $signature->update($data);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil diperbarui.');
    }

    /**
     * Remove the specified signature from storage.
     */
    public function destroy(Signature $signature)
    {
        if ($signature->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the image file
        if ($signature->image_path && Storage::disk('public')->exists($signature->image_path)) {
            Storage::disk('public')->delete($signature->image_path);
        }

        $signature->delete();

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil dihapus.');
    }

    /**
     * Toggle the active status of the signature.
     */
    public function toggleActive(Signature $signature)
    {
        if ($signature->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $signature->update(['is_active' => !$signature->is_active]);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Status tanda tangan berhasil diubah.');
    }
}