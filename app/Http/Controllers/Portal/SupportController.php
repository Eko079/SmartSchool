<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Concerns\RendersPortalMobile;
use App\Models\Faq;
use App\Models\HelpTicket;
use App\Models\TicketReply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    use RendersPortalMobile;
    public function index(Request $request)
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        $user = Auth::guard('wali')->user() ?? Auth::user();
        $tickets = HelpTicket::with('replies')->where('user_id', $user->id)->latest()->take(10)->get();

        if ($request->expectsJson() || ! view()->exists('portal.bantuan')) {
            return response()->json(['faqs' => $faqs, 'tickets' => $tickets]);
        }

        return $this->portalView($request, 'portal.bantuan', 'portal-mobile.bantuan', compact('faqs', 'tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::guard('wali')->user() ?? Auth::user();
        $ticket = HelpTicket::create([
            'ticket_number' => 'TKT-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
            'sla_due_at' => Carbon::now()->addDay(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ticket' => $ticket], 201);
        }

        return back()->with('status', 'Tiket ' . $ticket->ticket_number . ' terkirim. SLA 1x24 jam.');
    }

    public function reply(Request $request, HelpTicket $ticket)
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();
        abort_if($ticket->user_id !== $user->id, 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $reply = TicketReply::create([
            'help_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['reply' => $reply], 201);
        }

        return back()->with('status', 'Balasan terkirim.');
    }
}
