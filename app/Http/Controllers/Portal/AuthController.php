<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('wali')->check()) {
            return redirect()->route('portal.dashboard');
        }

        return response()->json(['view' => 'portal.login', 'message' => 'Portal login placeholder. Frontend menyusul.']);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($validated['identifier']);

        $user = User::where('role', 'wali')
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->orWhereHas('student', function ($sq) use ($identifier) {
                        $sq->where('nis', $identifier);
                    });
            })->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            return back()->withErrors(['identifier' => 'Akun dikunci hingga ' . $user->locked_until->format('H:i') . '. Coba lagi nanti.'])->onlyInput('identifier');
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            if ($user) {
                $user->increment('failed_attempts');
                if ($user->failed_attempts >= 5) {
                    $user->forceFill([
                        'failed_attempts' => 0,
                        'locked_until' => Carbon::now()->addMinutes(15),
                    ])->save();
                }
            }

            return back()->withErrors(['identifier' => 'NIS / Email atau kata sandi salah.'])->onlyInput('identifier');
        }

        $user->forceFill(['failed_attempts' => 0, 'locked_until' => null])->save();

        Auth::guard('wali')->login($user, $request->boolean('remember'));
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('portal.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('wali')->logout();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }

    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'channel' => ['nullable', 'in:email,wa'],
        ]);

        $channel = $validated['channel'] ?? 'email';
        $code = (string) random_int(100000, 999999);

        OtpCode::create([
            'identifier' => $validated['identifier'],
            'channel' => $channel,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        if (app()->environment('testing', 'local')) {
            return response()->json(['message' => 'OTP terkirim.', 'debug_code' => $code]);
        }

        return response()->json(['message' => 'OTP terkirim. Berlaku 5 menit.']);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $otp = OtpCode::where('identifier', $validated['identifier'])
            ->whereNull('consumed_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (! $otp) {
            return back()->withErrors(['code' => 'OTP kedaluwarsa. Minta kode baru.']);
        }

        if ($otp->attempts >= 5) {
            return back()->withErrors(['code' => 'OTP terkunci 15 menit karena salah 5x.']);
        }

        if (! Hash::check($validated['code'], $otp->code_hash)) {
            $otp->increment('attempts');

            return back()->withErrors(['code' => 'Kode OTP salah.']);
        }

        $otp->forceFill(['consumed_at' => Carbon::now()])->save();

        $user = User::where('role', 'wali')
            ->where(function ($q) use ($validated) {
                $q->where('email', $validated['identifier'])
                    ->orWhere('phone', $validated['identifier']);
            })->first();

        if (! $user && Student::where('nis', $validated['identifier'])->exists()) {
            $student = Student::where('nis', $validated['identifier'])->first();
            $user = User::where('role', 'wali')->where('student_id', $student->id)->first();
        }

        if (! $user) {
            return back()->withErrors(['identifier' => 'Akun wali tidak ditemukan.']);
        }

        $user->forceFill(['password' => $validated['password']])->save();

        return redirect()->route('portal.login')->with('status', 'Kata sandi baru tersimpan. Silakan masuk.');
    }
}
