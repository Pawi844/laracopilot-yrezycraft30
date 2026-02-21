<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\IspClient;
use App\Models\Transaction;
use App\Models\ClientInvoice;
use App\Models\Plan;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller {

    private function getAccessToken(): ?string {
        $consumerKey    = SystemSetting::get('mpesa','consumer_key','');
        $consumerSecret = SystemSetting::get('mpesa','consumer_secret','');
        $env            = SystemSetting::get('mpesa','environment','sandbox');
        $url = $env === 'production'
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        try {
            $resp = Http::withBasicAuth($consumerKey,$consumerSecret)->get($url);
            return $resp->json('access_token');
        } catch (\Exception $e) {
            Log::error('[MPesa] Token: '.$e->getMessage());
            return null;
        }
    }

    // STK Push
    public function stkPush(Request $req) {
        $req->validate(['phone'=>'required','amount'=>'required|numeric','account_ref'=>'required']);
        $token = $this->getAccessToken();
        if (!$token) return response()->json(['error'=>'Failed to get M-Pesa token'],500);

        $env       = SystemSetting::get('mpesa','environment','sandbox');
        $shortcode = SystemSetting::get('mpesa','shortcode','');
        $passkey   = SystemSetting::get('mpesa','passkey','');
        $timestamp = date('YmdHis');
        $password  = base64_encode($shortcode.$passkey.$timestamp);
        $callback  = SystemSetting::get('mpesa','callback_url','');
        $type      = SystemSetting::get('mpesa','type','paybill');

        $url = $env === 'production'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $phone = '254'.ltrim(ltrim($req->phone,'+'),'0');
        $resp = Http::withToken($token)->post($url,[
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $type === 'till' ? 'CustomerBuyGoodsOnline' : 'CustomerPayBillOnline',
            'Amount'            => intval($req->amount),
            'PartyA'            => $phone,
            'PartyB'            => $shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callback,
            'AccountReference'  => $req->account_ref,
            'TransactionDesc'   => SystemSetting::get('mpesa','transaction_desc','Internet Payment'),
        ]);
        return response()->json($resp->json());
    }

    // M-Pesa Callback — auto-provision client
    public function callback(Request $req) {
        Log::info('[MPesa Callback]', $req->all());
        $data = $req->json('Body.stkCallback');
        if (!$data || $data['ResultCode'] !== 0) return response()->json(['ok'=>true]);

        $items   = collect($data['CallbackMetadata']['Item'])->keyBy('Name');
        $amount  = $items->get('Amount')['Value']  ?? 0;
        $mpesaRef= $items->get('MpesaReceiptNumber')['Value'] ?? '';
        $phone   = '0'.substr($items->get('PhoneNumber')['Value'] ?? '',3);
        $account = $data['AccountReference'] ?? '';

        // Find client by username / account reference
        $client = IspClient::where('username',$account)
            ->orWhere('phone',$phone)
            ->first();

        // Record transaction
        Transaction::create([
            'client_id'   => $client?->id,
            'amount'      => $amount,
            'type'        => 'payment',
            'method'      => 'mpesa',
            'reference'   => $mpesaRef,
            'phone'       => $phone,
            'account_ref' => $account,
            'status'      => 'completed',
            'description' => 'M-Pesa payment from '.$phone,
        ]);

        if ($client) {
            // Find plan matching amount
            $plan = $client->plan ?? Plan::where('price','<=',$amount)->orderByDesc('price')->first();
            // Extend expiry by plan duration (assume monthly=30 days)
            $expiry = $client->expiry_date && $client->expiry_date > now()
                ? $client->expiry_date->addDays($plan?->duration_days ?? 30)
                : now()->addDays($plan?->duration_days ?? 30);
            $client->update(['status'=>'active','expiry_date'=>$expiry]);

            // Create invoice
            ClientInvoice::create([
                'client_id'            => $client->id,
                'invoice_no'           => ClientInvoice::nextInvoiceNo(),
                'amount'               => $amount,
                'status'               => 'paid',
                'plan_name'            => $plan?->name ?? 'Internet',
                'billing_period_start' => now()->toDateString(),
                'billing_period_end'   => $expiry->toDateString(),
                'due_date'             => now()->toDateString(),
                'paid_at'              => now(),
                'payment_method'       => 'mpesa',
                'mpesa_ref'            => $mpesaRef,
            ]);

            // Notify client
            (new NotificationService)->notifyClient($client,'payment_received',[
                'amount'   => $amount,
                'reference'=> $mpesaRef,
                'expiry'   => $expiry->format('d M Y'),
                'plan'     => $plan?->name ?? 'Internet',
            ]);
        }
        return response()->json(['ok'=>true]);
    }

    // Paybill validation callback
    public function validation(Request $req) {
        return response()->json(['ResultCode'=>0,'ResultDesc'=>'Accepted']);
    }

    // Paybill confirmation callback
    public function confirmation(Request $req) {
        return $this->callback($req);
    }
}