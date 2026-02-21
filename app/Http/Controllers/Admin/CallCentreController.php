<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\IspClient;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallCentreController extends Controller {
    private function auth() {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return null;
    }

    public function index(Request $req) {
        if ($r = $this->auth()) return $r;
        $query  = CallLog::with(['client','agent','ticket'])->latest();
        if ($req->status)    $query->where('status',$req->status);
        if ($req->agent)     $query->where('agent_id',$req->agent);
        if ($req->direction) $query->where('direction',$req->direction);
        if ($req->search)    $query->where('caller_number','like','%'.$req->search.'%')
            ->orWhereHas('client',fn($q)=>$q->where('username','like','%'.$req->search.'%'));
        $calls  = $query->paginate(25)->withQueryString();
        $agents = User::whereIn('role',['operator','technician'])->orderBy('name')->get();
        $stats  = [
            'total_today'    => CallLog::whereDate('created_at',today())->count(),
            'answered_today' => CallLog::whereDate('created_at',today())->where('status','answered')->count(),
            'missed_today'   => CallLog::whereDate('created_at',today())->where('status','missed')->count(),
            'avg_duration'   => round(CallLog::where('status','answered')->avg('duration_seconds') ?? 0),
        ];
        $settings = [
            'url'      => SystemSetting::get('callcentre','voip_url',''),
            'username' => SystemSetting::get('callcentre','voip_username',''),
            'provider' => SystemSetting::get('callcentre','provider','3CX'),
        ];
        return view('admin.callcentre.index', compact('calls','agents','stats','settings'));
    }

    public function settings() {
        if ($r = $this->auth()) return $r;
        return view('admin.callcentre.settings');
    }

    public function create() {
        if ($r = $this->auth()) return $r;
        $clients = IspClient::select('id','first_name','last_name','username','phone')->orderBy('username')->get();
        $agents  = User::whereIn('role',['operator','technician','admin'])->orderBy('name')->get();
        return view('admin.callcentre.create', compact('clients','agents'));
    }

    public function store(Request $req) {
        if ($r = $this->auth()) return $r;
        $v = $req->validate([
            'caller_number'   => 'required|max:30',
            'direction'       => 'required|in:inbound,outbound',
            'status'          => 'required|in:answered,missed,voicemail,dropped',
            'client_id'       => 'nullable|exists:clients,id',
            'agent_id'        => 'nullable|exists:users,id',
            'duration_seconds'=> 'nullable|integer|min:0',
            'notes'           => 'nullable|string',
            'disposition'     => 'nullable|in:resolved,follow_up,escalated,no_action',
        ]);
        $v['answered_at'] = $v['status'] === 'answered' ? now() : null;
        $call = CallLog::create($v);
        if (in_array($req->disposition,['escalated','follow_up']) && $req->notes) {
            $ticket = SupportTicket::create([
                'client_id'  => $req->client_id,
                'subject'    => 'Follow-up from call #'.$call->id,
                'description'=> $req->notes,
                'status'     => 'open',
                'priority'   => $req->disposition === 'escalated' ? 'high' : 'medium',
                'source'     => 'call_centre',
                'call_id'    => (string)$call->id,
            ]);
            $call->update(['ticket_id'=>$ticket->id]);
        }
        return redirect()->route('admin.callcentre.index')->with('success','Call logged!');
    }

    public function show($id) {
        if ($r = $this->auth()) return $r;
        $call = CallLog::with(['client','agent','ticket.technician'])->findOrFail($id);
        return view('admin.callcentre.show', compact('call'));
    }

    public function destroy($id) {
        if ($r = $this->auth()) return $r;
        CallLog::findOrFail($id)->delete();
        return redirect()->route('admin.callcentre.index')->with('success','Call log deleted.');
    }

    public function lookupPhone(Request $req) {
        $phone  = $req->phone;
        $client = IspClient::where('phone','like','%'.ltrim(str_replace(['+254','254'],'',$phone),'0'))->with('plan','fat')->first();
        if (!$client) return response()->json(['found'=>false]);
        return response()->json([
            'found'        => true,
            'id'           => $client->id,
            'name'         => $client->first_name.' '.$client->last_name,
            'username'     => $client->username,
            'plan'         => $client->plan?->name,
            'status'       => $client->status,
            'expiry'       => $client->expiry_date?->format('d M Y'),
            'fat'          => $client->fat?->code,
            'open_tickets' => SupportTicket::where('client_id',$client->id)->whereIn('status',['open','in_progress'])->count(),
        ]);
    }

    /**
     * 3CX Webhook — receives call events from 3CX and auto-logs them
     */
    public function webhook(Request $req) {
        Log::info('[3CX Webhook]', $req->all());
        try {
            // 3CX sends various JSON formats; handle common ones
            $data = $req->all();
            $callerNum = $data['CallerNumber'] ?? $data['From'] ?? $data['caller_number'] ?? '';
            $callId    = $data['CallID']       ?? $data['call_id']     ?? null;
            $event     = strtolower($data['Event'] ?? $data['event'] ?? 'unknown');
            $duration  = (int)($data['Duration'] ?? $data['duration'] ?? 0);
            $agent     = $data['AgentName'] ?? $data['agent'] ?? null;

            $status = 'answered';
            if (str_contains($event,'miss'))    $status = 'missed';
            if (str_contains($event,'voicemail')) $status = 'voicemail';
            if (str_contains($event,'end') || str_contains($event,'hangup')) $status = 'answered';

            // Find client by caller number
            $client = IspClient::where('phone','like','%'.substr($callerNum,-9))->first();

            // Find agent user
            $agentUser = $agent ? User::where('name','like','%'.$agent.'%')->first() : null;

            CallLog::create([
                'call_id'          => $callId,
                'client_id'        => $client?->id,
                'agent_id'         => $agentUser?->id,
                'caller_number'    => $callerNum,
                'direction'        => 'inbound',
                'status'           => $status,
                'duration_seconds' => $duration,
                'notes'            => '3CX auto-logged: '.$event,
                'answered_at'      => $status === 'answered' ? now() : null,
                'ended_at'         => $duration ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('[3CX Webhook] '.$e->getMessage());
        }
        return response()->json(['ok'=>true]);
    }
}