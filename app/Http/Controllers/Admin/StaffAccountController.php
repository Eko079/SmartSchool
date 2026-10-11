<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffAccountController extends Controller
{
    private function isSuper(): bool
    {
        return (auth()->user()->role ?? '') === 'superadmin';
    }

    /** Bendahara hanya boleh menyentuh sesama bendahara; wali bukan staf. */
    private function guardTarget(User $target): void
    {
        abort_if($target->role === 'wali', 403);
        if (! $this->isSuper()) {
            abort_if($target->role !== 'admin', 403, 'Hanya sesama Bendahara.');
        }
    }

    public function index()
    {
        $roles = $this->isSuper() ? ['superadmin', 'admin'] : ['admin'];
        $staff = User::whereIn('role', $roles)->orderBy('name')->get();

        return view('admin.akun.index', ['staff' => $staff, 'isSuper' => $this->isSuper()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:30',
            'role' => $this->isSuper() ? 'required|in:superadmin,admin' : 'nullable|in:admin',
        ]);
        $role = $this->isSuper() ? $validated['role'] : 'admin';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'role' => $role,
            'is_active' => true,
        ]);
        \App\Models\AuditLog::log('akun.tambah', "Buat akun {$user->role_label} {$user->name} ({$user->email})");

        return back()->with('success', "Akun {$user->role_label} {$user->name} dibuat.");
    }

    /** Sandi baru acak, ditampilkan sekali via flash. */
    public function reset(User $user)
    {
        $this->guardTarget($user);
        $plain = Str::random(10);
        $user->update(['password' => $plain]);
        \App\Models\AuditLog::log('akun.reset', "Reset sandi {$user->name} ({$user->email})");

        return back()->with('success', "Sandi baru {$user->name}: {$plain} — catat dan teruskan sekarang, tidak ditampilkan lagi.");
    }

    public function toggle(User $user)
    {
        $this->guardTarget($user);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }
        if ($user->role === 'superadmin' && $user->is_active
            && User::where('role', 'superadmin')->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Minimal 1 Super Admin aktif harus tersisa.');
        }
        $user->update(['is_active' => ! $user->is_active]);
        $aksi = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        \App\Models\AuditLog::log('akun.status', "Akun {$user->name} {$aksi}");

        return back()->with('success', "Akun {$user->name} {$aksi}.");
    }
}
