<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();
        $user->load('student.classRoom');

        if ($request->expectsJson() || ! view()->exists('portal.pengaturan')) {
            return response()->json(['user' => $user]);
        }

        return view('portal.pengaturan', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'notify_wa' => ['nullable', 'boolean'],
            'notify_email' => ['nullable', 'boolean'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'notify_wa' => $validated['notify_wa'] ?? $user->notify_wa,
            'notify_email' => $validated['notify_email'] ?? $user->notify_email,
        ])->save();

        if ($request->expectsJson()) {
            return response()->json(['user' => $user->fresh()]);
        }

        return back()->with('status', 'Profil tersimpan.');
    }

    public function password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::guard('wali')->user() ?? Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi lama salah.']);
        }

        $user->forceFill(['password' => $validated['password']])->save();

        return back()->with('status', 'Kata sandi diperbarui.');
    }

    public function profile(Request $request)
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();
        $student = $user->student;
        abort_if(! $student, 422);
        $student->load(['classRoom', 'bills.feeCategory', 'payments']);

        if ($request->expectsJson() || ! view()->exists('portal.profil')) {
            return response()->json(['student' => $student]);
        }

        return view('portal.profil', compact('student'));
    }
}
