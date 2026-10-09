<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpTicket;
use App\Models\TicketReply;
use App\Support\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = HelpTicket::with(['user.student', 'replies'])->latest();

        if ($request->filled('status') && in_array($request->status, ['open', 'answered', 'closed'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('ticket_number', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        $tickets = $query->paginate(15)->withQueryString();
        $stats = [
            'open' => HelpTicket::where('status', 'open')->count(),
            'answered' => HelpTicket::where('status', 'answered')->count(),
            'closed' => HelpTicket::where('status', 'closed')->count(),
        ];

        if (Device::isPhone($request)) {
            return view('mobile.tiket', compact('tickets', 'stats'));
        }

        return view('admin.tiket.index', compact('tickets', 'stats'));
    }

    public function reply(Request $request, HelpTicket $ticket)
    {
        $validated = $request->validate(['message' => 'required|string|max:2000']);

        TicketReply::create([
            'help_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        $ticket->update(['status' => 'answered']);

        return back()->with('success', 'Balasan terkirim ke wali.');
    }

    public function close(HelpTicket $ticket)
    {
        $ticket->update(['status' => 'closed']);

        return back()->with('success', 'Tiket ' . $ticket->ticket_number . ' ditutup.');
    }

    public function reopen(HelpTicket $ticket)
    {
        $ticket->update(['status' => 'open']);

        return back()->with('success', 'Tiket dibuka kembali.');
    }
}
