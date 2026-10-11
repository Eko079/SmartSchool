<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /** Profil staf yang sedang login (baca-saja + ganti sandi sendiri). */
    public function show()
    {
        $user = auth()->user();

        return view('admin.profil', compact('user'));
    }

    public function password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama salah.']);
        }

        $user->forceFill(['password' => Hash::make($validated['password'])])->save();

        return back()->with('status', 'Kata sandi diperbarui.');
    }
}
