<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder {
    public function run() {
        $settings = [
            // SMS
            ['group'=>'sms','key'=>'enabled','value'=>'0','label'=>'Enable SMS','type'=>'toggle'],
            ['group'=>'sms','key'=>'gateway','value'=>'africastalking','label'=>'SMS Gateway','type'=>'select'],
            ['group'=>'sms','key'=>'sender_id','value'=>'','label'=>'Sender ID / From Name','type'=>'text'],
            ['group'=>'sms','key'=>'api_key','value'=>'','label'=>'API Key / User ID','type'=>'text'],
            ['group'=>'sms','key'=>'api_secret','value'=>'','label'=>'API Secret / Password','type'=>'password'],
            ['group'=>'sms','key'=>'api_url','value'=>'','label'=>'Custom API URL (if using custom)','type'=>'text'],
            // Zettatel specific
            ['group'=>'sms','key'=>'account_sid','value'=>'','label'=>'Account SID (Twilio)','type'=>'text'],
            ['group'=>'sms','key'=>'auth_token','value'=>'','label'=>'Auth Token (Twilio)','type'=>'password'],
            ['group'=>'sms','key'=>'from_number','value'=>'','label'=>'From Number (Twilio)','type'=>'text'],
            // WhatsApp
            ['group'=>'whatsapp','key'=>'enabled','value'=>'0','label'=>'Enable WhatsApp','type'=>'toggle'],
            ['group'=>'whatsapp','key'=>'gateway','value'=>'360dialog','label'=>'WhatsApp Gateway','type'=>'select'],
            ['group'=>'whatsapp','key'=>'api_key','value'=>'','label'=>'API Key / Token','type'=>'password'],
            ['group'=>'whatsapp','key'=>'phone_number_id','value'=>'','label'=>'Phone Number ID / Instance','type'=>'text'],
            ['group'=>'whatsapp','key'=>'from_number','value'=>'','label'=>'From Number','type'=>'text'],
            ['group'=>'whatsapp','key'=>'api_url','value'=>'','label'=>'Custom API URL','type'=>'text'],
            // Mail
            ['group'=>'mail','key'=>'enabled','value'=>'0','label'=>'Enable Email','type'=>'toggle'],
            ['group'=>'mail','key'=>'host','value'=>'smtp.gmail.com','label'=>'SMTP Host','type'=>'text'],
            ['group'=>'mail','key'=>'port','value'=>'587','label'=>'SMTP Port','type'=>'text'],
            ['group'=>'mail','key'=>'encryption','value'=>'tls','label'=>'Encryption','type'=>'select'],
            ['group'=>'mail','key'=>'username','value'=>'','label'=>'SMTP Username','type'=>'text'],
            ['group'=>'mail','key'=>'password','value'=>'','label'=>'SMTP Password','type'=>'password'],
            ['group'=>'mail','key'=>'from_address','value'=>'','label'=>'From Email Address','type'=>'text'],
            ['group'=>'mail','key'=>'from_name','value'=>'ISP Support','label'=>'From Name','type'=>'text'],
            // M-Pesa
            ['group'=>'mpesa','key'=>'environment','value'=>'sandbox','label'=>'Environment','type'=>'select'],
            ['group'=>'mpesa','key'=>'shortcode','value'=>'','label'=>'Paybill / Till Number','type'=>'text'],
            ['group'=>'mpesa','key'=>'type','value'=>'paybill','label'=>'Type','type'=>'select'],
            ['group'=>'mpesa','key'=>'consumer_key','value'=>'','label'=>'Consumer Key','type'=>'text'],
            ['group'=>'mpesa','key'=>'consumer_secret','value'=>'','label'=>'Consumer Secret','type'=>'password'],
            ['group'=>'mpesa','key'=>'passkey','value'=>'','label'=>'Passkey (STK Push)','type'=>'password'],
            ['group'=>'mpesa','key'=>'callback_url','value'=>'','label'=>'Callback URL','type'=>'text','description'=>'e.g. https://yourdomain.com/api/mpesa/callback'],
            ['group'=>'mpesa','key'=>'transaction_desc','value'=>'Internet Payment','label'=>'Transaction Description','type'=>'text'],
            // General
            ['group'=>'general','key'=>'company_name','value'=>'MtaaKonnect ISP','label'=>'Company Name','type'=>'text'],
            ['group'=>'general','key'=>'company_phone','value'=>'','label'=>'Support Phone','type'=>'text'],
            ['group'=>'general','key'=>'company_email','value'=>'','label'=>'Support Email','type'=>'text'],
            ['group'=>'general','key'=>'company_address','value'=>'','label'=>'Physical Address','type'=>'text'],
            ['group'=>'general','key'=>'logo_url','value'=>'','label'=>'Logo URL','type'=>'text'],
            // Billing
            ['group'=>'billing','key'=>'invoice_prefix','value'=>'INV','label'=>'Invoice Prefix','type'=>'text'],
            ['group'=>'billing','key'=>'vat_rate','value'=>'16','label'=>'VAT Rate (%)','type'=>'text'],
            ['group'=>'billing','key'=>'grace_period_days','value'=>'3','label'=>'Grace Period (days after expiry)','type'=>'text'],
            // TR-069 Global ACS
            ['group'=>'tr069','key'=>'acs_url','value'=>'','label'=>'Global ACS URL','type'=>'text','description'=>'e.g. http://acs.yourisp.com:7547 — used for all devices unless overridden'],
            ['group'=>'tr069','key'=>'acs_username','value'=>'','label'=>'ACS Username','type'=>'text'],
            ['group'=>'tr069','key'=>'acs_password','value'=>'','label'=>'ACS Password','type'=>'password'],
            // Call Centre / VoIP
            ['group'=>'callcentre','key'=>'voip_url','value'=>'','label'=>'VoIP Panel URL','type'=>'text','description'=>'URL to your VoIP/PBX web panel (FreePBX, 3CX, etc.)'],
            ['group'=>'callcentre','key'=>'voip_username','value'=>'','label'=>'VoIP Username','type'=>'text'],
            ['group'=>'callcentre','key'=>'voip_password','value'=>'','label'=>'VoIP Password','type'=>'password'],
            ['group'=>'callcentre','key'=>'provider','value'=>'custom','label'=>'VoIP Provider','type'=>'text','description'=>'e.g. FreePBX, 3CX, Asterisk, Twilio, Vonage'],
            ['group'=>'callcentre','key'=>'sip_server','value'=>'','label'=>'SIP Server / Domain','type'=>'text'],
            ['group'=>'callcentre','key'=>'recording_enabled','value'=>'0','label'=>'Enable Call Recording','type'=>'toggle'],
        ];

        foreach ($settings as $s) {
            SystemSetting::firstOrCreate(
                ['group'=>$s['group'],'key'=>$s['key']],
                array_merge(['value'=>'','is_secret'=>($s['type']==='password')],$s)
            );
        }
    }
}