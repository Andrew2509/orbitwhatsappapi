<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function tickets()
    {
        // TODO: Implement support tickets
        return view('admin.support.tickets', [
            'tickets' => collect(),
        ]);
    }

    public function showTicket($ticket)
    {
        return view('admin.support.ticket-detail');
    }

    public function replyTicket(Request $request, $ticket)
    {
        return back()->with('success', 'Balasan terkirim.');
    }

    public function announcements()
    {
        // TODO: Implement announcements
        return view('admin.support.announcements', [
            'announcements' => collect(),
        ]);
    }

    public function createAnnouncement(Request $request)
    {
        return back()->with('info', 'Fitur announcement akan segera tersedia.');
    }
}
