<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();
        $student = $user->student;
        if (! $student) {
            return response()->json(['message' => 'Siswa tidak tertaut ke akun wali.'], 422);
        }

        $bills = Bill::with('feeCategory')->where('student_id', $student->id);
        $activeBills = (clone $bills)->whereIn('status', ['unpaid', 'partial', 'overdue']);
        $waitingAmount = (float) (clone $activeBills)->sum('amount') - (float) (clone $activeBills)->sum('paid_amount');
        $paidBills = (clone $bills)->where('status', 'paid');
        $overdueBills = (clone $bills)->where('status', 'overdue');

        $nearest = (clone $activeBills)->orderBy('due_date')->first();

        $perCategory = (clone $bills)->with('feeCategory')->get()->groupBy(fn ($b) => $b->feeCategory->name ?? 'Lainnya')
            ->map(fn ($g) => (float) $g->sum('amount'));

        $latest = Bill::with('feeCategory')->where('student_id', $student->id)->latest()->take(6)->get();
        $events = $student->bills()->with('events')->latest()->take(1)->first()?->events()->latest()->take(5)->get() ?? [];

        $payload = [
            'student' => $student->load('classRoom'),
            'stats' => [
                'active_count' => (clone $activeBills)->count(),
                'waiting_amount' => max(0, $waitingAmount),
                'paid_count' => (clone $paidBills)->count(),
                'overdue_count' => (clone $overdueBills)->count(),
                'nearest_due' => $nearest,
            ],
            'per_category' => $perCategory,
            'latest_bills' => $latest,
            'activities' => $events,
        ];

        if ($request->expectsJson() || $request->is('api/*') || ! view()->exists('portal.dashboard')) {
            return response()->json($payload);
        }

        return view('portal.dashboard', $payload);
    }
}
