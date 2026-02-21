<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder {
    public function run() {
        $settings = [
            // ── General ────────────────────────────────────────────────
            ['general','company_name','MtaaKonnect ISP','text','Company Name','Your ISP business name',false,1],
            ['general','company_email','admin@mtaakonnect.co.ke','text','Company Email','Default from address',false,2],
            ['general','company_phone','+254700000000','text','Company Phone','',false,3],
            ['general','company_address','Nairobi, Kenya','textarea','Company Address','',false,4],
            ['general','company_logo','','text','Logo URL','Full URL to company logo',false,5],
            ['general','currency','KES','text','Currency','e.g. KES, USD',false,6],
            ['general','timezone','Africa/Nairobi','text','Timezone','PHP timezone string',false,7],
            ['general','expiry_reminder_days','3','text','Expiry Reminder Days','Days before expiry to send reminder',false,8],
            ['general','grace_period_days','2','text','Grace Period (days)','Days after expiry before suspension',false,9],

            // ── M-Pesa ────────────────────────────────────────────────
            ['mpesa','type','paybill','select','M-Pesa Type','paybill or till',false,1],
            ['mpesa','shortcode','','text','Paybill / Till Number','Your M-Pesa shortcode',false,2],
            ['mpesa','consumer_key','','text','Daraja Consumer Key','From Safaricom Developer Portal',true,3],
            ['mpesa','consumer_secret','','password','Daraja Consumer Secret','',true,4],
            ['mpesa','passkey','','password','LipaNaMpesa Passkey','Online passkey from Safaricom',true,5],
            ['mpesa','account_reference','ISP Payment','text','Account Reference','Shows on customer phone',false,6],
            ['mpesa','transaction_desc','Internet Payment','text','Transaction Description','',false,7],
            ['mpesa','callback_url','','text','Callback URL','e.g. https://yourdomain.com/api/mpesa/callback',false,8],
            ['mpesa','environment','sandbox','select','Environment','sandbox or production',false,9],
            ['mpesa','enabled','0','toggle','Enable M-Pesa Payments','',false,10],

            // ── SMS ───────────────────────────────────────────────────
            ['sms','gateway','africastalking','select','SMS Gateway','africastalking | twilio | vonage | infobip | custom',false,1],
            ['sms','sender_id','MTAAKONNECT','text','Sender ID','Alphanumeric sender name (max 11 chars)',false,2],
            ['sms','api_key','','password','API Key / Username','',true,3],
            ['sms','api_secret','','password','API Secret / Password','For gateways that require it',true,4],
            ['sms','api_url','','text','Custom API URL','For custom HTTP SMS gateway',false,5],
            ['sms','account_sid','','text','Account SID','Twilio Account SID (Twilio only)',false,6],
            ['sms','auth_token','','password','Auth Token','Twilio Auth Token',true,7],
            ['sms','from_number','','text','From Phone Number','Twilio from number e.g. +1234567890',false,8],
            ['sms','enabled','0','toggle','Enable SMS Notifications','',false,9],

            // ── WhatsApp ──────────────────────────────────────────────
            ['whatsapp','gateway','360dialog','select','WhatsApp Gateway','360dialog | twilio | vonage | ultramsg | custom',false,1],
            ['whatsapp','phone_number_id','','text','Phone Number ID','Meta/360dialog Phone Number ID',false,2],
            ['whatsapp','api_key','','password','API Key / Token','',true,3],
            ['whatsapp','api_url','','text','API Base URL','e.g. https://waba.360dialog.io/v1',false,4],
            ['whatsapp','account_sid','','text','Account SID','Twilio Account SID',false,5],
            ['whatsapp','auth_token','','password','Auth Token','Twilio Auth Token',true,6],
            ['whatsapp','from_number','','text','From WhatsApp Number','e.g. whatsapp:+14155238886',false,7],
            ['whatsapp','enabled','0','toggle','Enable WhatsApp Notifications','',false,8],

            // ── Mail ──────────────────────────────────────────────────
            ['mail','mailer','smtp','select','Mail Driver','smtp | sendmail | mailgun | ses | postmark',false,1],
            ['mail','host','smtp.gmail.com','text','SMTP Host','',false,2],
            ['mail','port','587','text','SMTP Port','587 or 465',false,3],
            ['mail','encryption','tls','select','Encryption','tls | ssl | none',false,4],
            ['mail','username','','text','SMTP Username / Email','',false,5],
            ['mail','password','','password','SMTP Password / App Password','',true,6],
            ['mail','from_address','noreply@mtaakonnect.co.ke','text','From Email Address','',false,7],
            ['mail','from_name','MtaaKonnect ISP','text','From Name','',false,8],
            ['mail','mailgun_domain','','text','Mailgun Domain','',false,9],
            ['mail','mailgun_secret','','password','Mailgun Secret Key','',true,10],
            ['mail','enabled','0','toggle','Enable Email Notifications','',false,11],

            // ── Billing ───────────────────────────────────────────────
            ['billing','auto_suspend','1','toggle','Auto-Suspend Expired Accounts','',false,1],
            ['billing','auto_reconnect','1','toggle','Auto-Reconnect on Payment','',false,2],
            ['billing','invoice_prefix','INV','text','Invoice Number Prefix','',false,3],
            ['billing','tax_rate','16','text','Tax Rate (%)','VAT/Tax percentage',false,4],
            ['billing','tax_name','VAT','text','Tax Name','e.g. VAT, GST',false,5],
        ];

        foreach ($settings as $s) {
            SystemSetting::firstOrCreate(
                ['group' => $s[0], 'key' => $s[1]],
                [
                    'value' => $s[2], 'type' => $s[3], 'label' => $s[4],
                    'description' => $s[5], 'is_secret' => $s[6], 'sort_order' => $s[7]
                ]
            );
        }
    }
}