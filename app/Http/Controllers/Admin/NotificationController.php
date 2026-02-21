<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\NotificationLog;
use App\Models\OfflineAlert;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $clients = IspClient::where('status','active')->orderBy('first_name')->get();
        $logs = NotificationLog::with('client')->orderBy('created_at','desc')->paginate(30);
        $offlineClients = IspClient::where('status','active')
            ->whereNotNull('last_seen')
            ->whereRaw("datetime(last_seen) < datetime('now', '-12 hours')")
            ->get();
        $stats = [
            'sms' => NotificationLog::where('channel','sms')->count(),
            'sms_today' => NotificationLog::where('channel','sms')->whereDate('created_at',today())->count(),
            'whatsapp' => NotificationLog::where('channel','whatsapp')->count(),
            'email' => NotificationLog::where('channel','email')->count(),
            'failed' => NotificationLog::where('status','failed')->count(),
        ];
        return view('admin.notifications.index', compact('clients','logs','offlineClients','stats'));
    }

    public function sendSms(Request $request) {
        $this->auth();
        $request->validate([
            'recipient' => 'required|string',
            'message' => 'required|string|max:500',
            'client_id' => 'nullable|exists:clients,id'
        ]);
        NotificationLog::create([
            'client_id' => $request->client_id,
            'channel' => 'sms',
            'recipient' => $request->recipient,
            'message' => $request->message,
            'status' => 'sent', // Simulate success
            'provider' => 'AfricasTalking'
        ]);
        return back()->with('success', 'SMS sent to ' . $request->recipient);
    }

    public function sendWhatsapp(Request $request) {
        $this->auth();
        $request->validate([
            'recipient' => 'required|string',
            'message' => 'required|string|max:1000',
            'client_id' => 'nullable|exists:clients,id'
        ]);
        NotificationLog::create([
            'client_id' => $request->client_id,
            'channel' => 'whatsapp',
            'recipient' => $request->recipient,
            'message' => $request->message,
            'status' => 'sent',
            'provider' => 'WhatsApp Business API'
        ]);
        return back()->with('success', 'WhatsApp message sent to ' . $request->recipient);
    }

    public function sendEmail(Request $request) {
        $this->auth();
        $request->validate([
            'recipient' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'client_id' => 'nullable|exists:clients,id'
        ]);
        NotificationLog::create([
            'client_id' => $request->client_id,
            'channel' => 'email',
            'recipient' => $request->recipient,
            'message' => $request->subject . ': ' . $request->message,
            'status' => 'sent',
            'provider' => 'SMTP'
        ]);
        return back()->with('success', 'Email sent to ' . $request->recipient);
    }

    public function broadcast(Request $request) {
        $this->auth();
        $request->validate([
            'message' => 'required|string|max:500',
            'channels' => 'required|array',
            'target' => 'required|in:all,active,expired,suspended'
        ]);
        $query = IspClient::query();
        if ($request->target !== 'all') $query->where('status', $request->target);
        $clients = $query->get();
        $count = 0;
        foreach ($clients as $client) {
            foreach ($request->channels as $channel) {
                $recipient = match($channel) {
                    'sms', 'whatsapp' => $client->phone,
                    'email' => $client->email,
                    default => null
                };
                if (!$recipient) continue;
                NotificationLog::create([
                    'client_id' => $client->id,
                    'channel' => $channel,
                    'recipient' => $recipient,
                    'message' => $request->message,
                    'status' => 'sent',
                    'provider' => 'Broadcast'
                ]);
                $count++;
            }
        }
        return back()->with('success', "Broadcast sent to {$count} notifications across " . $clients->count() . " clients.");
    }
}