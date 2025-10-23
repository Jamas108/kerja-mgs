<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    /**
     * Display a listing of the signatures.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $signatures = Signature::with('user')
            ->latest()
            ->paginate(10);

        $directors = User::whereHas('role', function($query) {
            $query->where('name', 'direktur');
        })->get();

        return view('director.signatures.index', compact('signatures', 'directors'));
    }

    /**
     * Store a newly created signature in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:100',
            'signature_data' => 'required|string',
        ]);

        Signature::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'signature_data' => $request->signature_data,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified signature.
     *
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\View\View
     */
    public function edit(Signature $signature)
    {
        $directors = User::whereHas('role', function($query) {
            $query->where('name', 'direktur');
        })->get();

        return view('director.signatures.edit', compact('signature', 'directors'));
    }

    /**
     * Update the specified signature in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Signature $signature)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:100',
            'signature_data' => 'nullable|string',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'title' => $request->title,
            'is_active' => $request->has('is_active'),
        ];

        // Only update signature_data if it's provided
        if ($request->filled('signature_data')) {
            $data['signature_data'] = $request->signature_data;
        }

        $signature->update($data);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil diperbarui.');
    }

    /**
     * Remove the specified signature from storage.
     *
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Signature $signature)
    {
        // Delete the image file if it exists
        if ($signature->image_path && Storage::disk('public')->exists($signature->image_path)) {
            Storage::disk('public')->delete($signature->image_path);
        }

        $signature->delete();

        return redirect()->route('director.signatures.index')
            ->with('success', 'Tanda tangan berhasil dihapus.');
    }

    /**
     * Toggle the active status of the signature.
     *
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Signature $signature)
    {
        $signature->update(['is_active' => !$signature->is_active]);

        return redirect()->route('director.signatures.index')
            ->with('success', 'Status tanda tangan berhasil diubah.');
    }
}