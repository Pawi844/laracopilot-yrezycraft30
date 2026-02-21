<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() { $this->auth(); $tickets = SupportTicket::with('customer')->orderBy('created_at','desc')->paginate(20); return view('admin.support.index', compact('tickets')); }
    public function show($id) { $this->auth(); $ticket = SupportTicket::with('customer')->findOrFail($id); return view('admin.support.show', compact('ticket')); }
    public function update(Request $request, $id) {
        $this->auth();
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update($request->validate(['status'=>'required|in:open,in_progress,resolved,closed','priority'=>'required|in:low,medium,high,urgent','admin_notes'=>'nullable|string']));
        return redirect()->route('admin.support.index')->with('success','Ticket updated!');
    }
    public function destroy($id) { $this->auth(); SupportTicket::findOrFail($id)->delete(); return redirect()->route('admin.support.index')->with('success','Ticket deleted!'); }
}