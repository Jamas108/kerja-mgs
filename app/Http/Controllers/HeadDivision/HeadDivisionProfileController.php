<?php

namespace App\Http\Controllers\HeadDivision;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class HeadDivisionProfileController extends Controller
{
/**
     * Display the user's head_division.head_division.profile.
     */
    public function index()
    {
        $user = Auth::user();

        return view('head_division.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the head_division.profile.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('head_division.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        try {
            // Update user
            $user->update($validated);

            return redirect()->route('head_division.head_division_profile.index')
                ->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->back()
                    ->with('error', 'Password saat ini tidak sesuai')
                    ->withInput();
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return redirect()->route('head_division.head_division_profile.index')
                ->with('success', 'Password berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }
}
