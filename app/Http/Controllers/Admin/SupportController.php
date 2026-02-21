<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\IspClient;
use App\Models\User;
use App\Models\FatNode;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SupportController extends Controller {
    private function auth() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query = SupportTicket::with(['client','technician','replies'])->latest();
        if ($req->status)   $query->where('status',$req->status);
        if ($req->priority) $query->where('priority',$req->priority);
        if ($req->tech)     $query->where('technician_id',$req->tech);
        if ($req->source)   $query->where('source',$req->source);
        if ($req->search)   $query->where(function($q) use ($req) {
            $q->where('subject','like','%'.$req->search.'%')
              ->orWhereHas('client',fn($c)=>$c->where('username','like','%'.$req->search.'%'));
        });
        $tickets      = $query->paginate(20)->withQueryString();
        $technicians  = User::whereIn('role',['operator','technician'])->orderBy('name')->get();
        $stats = [
            'open'        => SupportTicket::where('status','open')->count(),
            'in_progress' => SupportTicket::where('status','in_progress')->count(),
            'resolved'    => SupportTicket::where('status','resolved')->count(),
            'urgent'      => SupportTicket::where('priority','urgent')->where('status','!=','resolved')->count(),
        ];
        return view('admin.support.index', compact('tickets','technicians','stats'));
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $clients     = IspClient::select('id','first_name','last_name','username','phone')->orderBy('username')->get();
        $technicians = User::whereIn('role',['operator','technician'])->orderBy('name')->get();
        $fatNodes    = FatNode::orderBy('name')->get();
        $categories  = SupportTicket::categories();
        return view('admin.support.create', compact('clients','technicians','fatNodes','categories'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'subject'       => 'required|max:255',
            'description'   => 'required',
            'client_id'     => 'nullable|exists:clients,id',
            'technician_id' => 'nullable|exists:users,id',
            'priority'      => 'required|in:low,medium,high,urgent',
            'category'      => 'nullable',
            'fat_node_id'   => 'nullable|exists:fat_nodes,id',
            'source'        => 'nullable|in:call_centre,portal,admin,email,walk_in',
        ]);
        $v['assigned_by'] = session('admin_user_id');
        $v['status']      = 'open';
        $ticket = SupportTicket::create($v);

        // Notify client via SMS
        if ($ticket->client) {
            (new NotificationService)->notifyClient($ticket->client,'ticket_opened',[
                'ticket_id' => $ticket->id,
                'subject'   => $ticket->subject,
            ]);
        }
        return redirect()->route('admin.support.show',$ticket->id)->with('success','Ticket #'.$ticket->id.' created and assigned!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $ticket      = SupportTicket::with(['client','technician','assignedBy','fatNode','replies.user','replies.client','callLog'])->findOrFail($id);
        $technicians = User::whereIn('role',['operator','technician'])->orderBy('name')->get();
        $categories  = SupportTicket::categories();
        return view('admin.support.show', compact('ticket','technicians','categories'));
    }

    public function update(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $ticket = SupportTicket::findOrFail($id);
        $req->validate([
            'status'        => 'required|in:open,in_progress,resolved,closed',
            'technician_id' => 'nullable|exists:users,id',
            'priority'      => 'nullable|in:low,medium,high,urgent',
            'resolution'    => 'nullable|string',
        ]);
        $update = $req->only(['status','technician_id','priority','category','resolution']);
        if ($req->status === 'resolved' && !$ticket->resolved_at) {
            $update['resolved_at'] = now();
        }
        $ticket->update($update);
        return back()->with('success','Ticket updated!');
    }

    public function reply(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $req->validate(['message'=>'required']);
        $ticket = SupportTicket::findOrFail($id);
        TicketReply::create([
            'ticket_id' => $id,
            'user_id'   => session('admin_user_id'),
            'message'   => $req->message,
        ]);
        // Auto-set to in_progress if still open
        if ($ticket->status === 'open') $ticket->update(['status'=>'in_progress']);
        return back()->with('success','Reply posted!');
    }

    public function assign(Request $req, $id) {
        if ($r = $this->auth()) return $r;
        $req->validate(['technician_id'=>'required|exists:users,id']);
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['technician_id'=>$req->technician_id,'assigned_by'=>session('admin_user_id'),'status'=>'in_progress']);
        return back()->with('success','Ticket assigned to '.User::find($req->technician_id)?->name);
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        SupportTicket::findOrFail($id)->delete();
        return redirect()->route('admin.support.index')->with('success','Ticket deleted.');
    }
}