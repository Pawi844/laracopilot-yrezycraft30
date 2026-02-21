<?php
namespace App\Services;
use App\Models\SystemSetting;
use App\Models\NotificationLog;
use App\Models\IspClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService {

    public function sendSms(string $to, string $message, ?int $clientId = null): bool {
        if (SystemSetting::get('sms','enabled','0') !== '1') {
            Log::info('[SMS] Disabled. Would send to '.$to.': '.$message);
            return false;
        }
        $gateway = SystemSetting::get('sms','gateway','africastalking');
        $ok = false;

        try {
            switch ($gateway) {
                case 'africastalking':
                    $resp = Http::withHeaders([
                        'apiKey'=> SystemSetting::get('sms','api_key',''),
                        'Accept'=> 'application/json',
                    ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                        'username' => SystemSetting::get('sms','api_secret','sandbox'),
                        'to'       => $to,
                        'message'  => $message,
                        'from'     => SystemSetting::get('sms','sender_id',''),
                    ]);
                    $ok = $resp->successful();
                    break;

                case 'twilio':
                    $sid   = SystemSetting::get('sms','account_sid','');
                    $token = SystemSetting::get('sms','auth_token','');
                    $resp  = Http::withBasicAuth($sid, $token)
                        ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                            'From' => SystemSetting::get('sms','from_number',''),
                            'To'   => $to,
                            'Body' => $message,
                        ]);
                    $ok = $resp->successful();
                    break;

                case 'vonage':
                    $resp = Http::post('https://rest.nexmo.com/sms/json', [
                        'api_key'    => SystemSetting::get('sms','api_key',''),
                        'api_secret' => SystemSetting::get('sms','api_secret',''),
                        'from'       => SystemSetting::get('sms','sender_id',''),
                        'to'         => ltrim($to, '+'),
                        'text'       => $message,
                    ]);
                    $ok = $resp->json('messages.0.status') === '0';
                    break;

                case 'infobip':
                    $baseUrl = 'https://'.SystemSetting::get('sms','api_url','');
                    $resp    = Http::withToken(SystemSetting::get('sms','api_key',''))
                        ->post($baseUrl.'/sms/2/text/advanced', [
                            'messages' => [[
                                'destinations' => [['to' => $to]],
                                'from'         => SystemSetting::get('sms','sender_id',''),
                                'text'         => $message,
                            ]]
                        ]);
                    $ok = $resp->successful();
                    break;

                case 'zettatel':
                    // Zettatel HTTP API
                    $resp = Http::get('https://portal.zettatel.com/SMSApi/send', [
                        'userid'      => SystemSetting::get('sms','api_key',''),
                        'password'    => SystemSetting::get('sms','api_secret',''),
                        'mobile'      => ltrim(ltrim($to, '+'), '0'),
                        'senderId'    => SystemSetting::get('sms','sender_id',''),
                        'msg'         => $message,
                        'msgType'     => 'text',
                        'duplicatecheck' => 'true',
                        'output'      => 'json',
                    ]);
                    // Zettatel returns {"status":"Success"} or {"status":"FAILED"}
                    $ok = $resp->successful() && str_contains(strtolower($resp->body()), 'success');
                    if (!$ok) Log::warning('[Zettatel] Response: '.$resp->body());
                    break;

                default: // custom HTTP
                    $url = SystemSetting::get('sms','api_url','');
                    if ($url) {
                        $resp = Http::post($url, [
                            'to'      => $to,
                            'message' => $message,
                            'key'     => SystemSetting::get('sms','api_key',''),
                        ]);
                        $ok = $resp->successful();
                    }
            }
        } catch (\Exception $e) {
            Log::error('[SMS] '.$gateway.' error: '.$e->getMessage());
        }

        $this->log($clientId, 'sms', $to, $message, $ok ? 'sent' : 'failed');
        return $ok;
    }

    public function sendWhatsapp(string $to, string $message, ?int $clientId = null): bool {
        if (SystemSetting::get('whatsapp','enabled','0') !== '1') return false;
        $gateway = SystemSetting::get('whatsapp','gateway','360dialog');
        $ok = false;
        try {
            switch ($gateway) {
                case '360dialog':
                    $resp = Http::withHeaders(['D360-API-KEY'=>SystemSetting::get('whatsapp','api_key','')])
                        ->post('https://waba.360dialog.io/v1/messages', [
                            'to'   => ltrim($to,'+'),
                            'type' => 'text',
                            'text' => ['body'=>$message],
                        ]);
                    $ok = $resp->successful();
                    break;
                case 'twilio':
                    $sid   = SystemSetting::get('whatsapp','account_sid','');
                    $token = SystemSetting::get('whatsapp','auth_token','');
                    $resp  = Http::withBasicAuth($sid,$token)
                        ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json",[
                            'From' => 'whatsapp:'.SystemSetting::get('whatsapp','from_number',''),
                            'To'   => 'whatsapp:'.$to,
                            'Body' => $message,
                        ]);
                    $ok = $resp->successful();
                    break;
                case 'ultramsg':
                    $instance = SystemSetting::get('whatsapp','phone_number_id','');
                    $resp = Http::post("https://api.ultramsg.com/{$instance}/messages/chat",[
                        'token' => SystemSetting::get('whatsapp','api_key',''),
                        'to'    => $to,
                        'body'  => $message,
                    ]);
                    $ok = $resp->json('sent') === 'true';
                    break;
                default:
                    $url = SystemSetting::get('whatsapp','api_url','');
                    if ($url) { $resp = Http::post($url,['to'=>$to,'message'=>$message]); $ok=$resp->successful(); }
            }
        } catch (\Exception $e) { Log::error('[WhatsApp] '.$e->getMessage()); }
        $this->log($clientId,'whatsapp',$to,$message,$ok?'sent':'failed');
        return $ok;
    }

    public function sendEmail(string $to, string $subject, string $body, ?int $clientId = null): bool {
        if (SystemSetting::get('mail','enabled','0') !== '1') return false;
        try {
            config([
                'mail.mailers.smtp.host'       => SystemSetting::get('mail','host','smtp.gmail.com'),
                'mail.mailers.smtp.port'       => SystemSetting::get('mail','port','587'),
                'mail.mailers.smtp.encryption' => SystemSetting::get('mail','encryption','tls'),
                'mail.mailers.smtp.username'   => SystemSetting::get('mail','username',''),
                'mail.mailers.smtp.password'   => SystemSetting::get('mail','password',''),
                'mail.from.address'            => SystemSetting::get('mail','from_address','noreply@isp.com'),
                'mail.from.name'               => SystemSetting::get('mail','from_name','ISP'),
            ]);
            Mail::html($body, fn($m) => $m->to($to)->subject($subject));
            $this->log($clientId,'email',$to,$subject,'sent');
            return true;
        } catch (\Exception $e) {
            Log::error('[Email] '.$e->getMessage());
            $this->log($clientId,'email',$to,$subject,'failed');
            return false;
        }
    }

    public function notifyClient(IspClient $client, string $event, array $vars = []): void {
        $baseVars = array_merge([
            'name'          => $client->first_name ?? $client->username,
            'username'      => $client->username,
            'plan'          => $client->plan->name ?? 'N/A',
            'expiry'        => $client->expiry_date?->format('d M Y') ?? 'N/A',
            'days_left'     => $client->expiry_date ? now()->diffInDays($client->expiry_date, false) : 0,
            'amount'        => $client->plan->price ?? '0',
            'company'       => SystemSetting::get('general','company_name','ISP'),
            'support_phone' => SystemSetting::get('general','company_phone',''),
            'paybill_no'    => SystemSetting::get('mpesa','shortcode',''),
            'paybill_type'  => strtoupper(SystemSetting::get('mpesa','type','paybill')),
        ], $vars);
        if ($client->notify_sms && $client->phone) {
            $msg = \App\Models\NotificationTemplate::render($event,'sms',$baseVars,$client->reseller_id);
            if ($msg) $this->sendSms($client->phone,$msg,$client->id);
        }
        if ($client->notify_whatsapp && $client->phone) {
            $msg = \App\Models\NotificationTemplate::render($event,'whatsapp',$baseVars,$client->reseller_id);
            if ($msg) $this->sendWhatsapp($client->phone,$msg,$client->id);
        }
        if ($client->notify_email && $client->email) {
            $tpl = \App\Models\NotificationTemplate::where('event',$event)->where('channel','email')->first();
            $msg = \App\Models\NotificationTemplate::render($event,'email',$baseVars,$client->reseller_id);
            if ($msg) $this->sendEmail($client->email,$tpl?->subject ?? SystemSetting::get('general','company_name','ISP').' Notification',$msg,$client->id);
        }
    }

    private function log(?int $clientId, string $channel, string $recipient, string $message, string $status): void {
        NotificationLog::create(['client_id'=>$clientId,'channel'=>$channel,'recipient'=>$recipient,'message'=>$message,'status'=>$status]);
    }
}