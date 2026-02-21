<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplatesSeeder extends Seeder {
    public function run() {
        $templates = [
            // ── Welcome ──────────────────────────────────────────────
            ['welcome','sms',null,'Dear {name}, welcome to {company}! Your account is ready. Username: {username}. For support call {support_phone}.'],
            ['welcome','email','Welcome to {company} - Account Ready','Dear {name},\n\nYour internet account has been created.\n\nUsername: {username}\nPassword: {password}\nPlan: {plan}\nExpiry: {expiry}\n\nFor support: {support_phone}\n\nRegards,\n{company}'],
            ['welcome','whatsapp',null,'Welcome to *{company}* 🎉\n\nHi {name}, your account is active!\n\n📶 *Plan:* {plan}\n👤 *Username:* {username}\n📅 *Expiry:* {expiry}\n\nSupport: {support_phone}'],

            // ── Expiry Reminder ───────────────────────────────────────
            ['expiry_reminder','sms',null,'Hi {name}, your {company} internet expires on {expiry} ({days_left} days). Pay KES {amount} via {paybill_type} {paybill_no}. Ref: {username}'],
            ['expiry_reminder','email','Your Internet Subscription Expires Soon','Dear {name},\n\nYour internet plan expires on {expiry}.\n\nPlan: {plan}\nAmount: KES {amount}\n\nPay via M-Pesa:\n{paybill_type}: {paybill_no}\nAccount: {username}\n\nContact us: {support_phone}\n\n{company}'],
            ['expiry_reminder','whatsapp',null,'⚠️ *Expiry Reminder*\n\nHi {name}! Your internet expires on *{expiry}* ({days_left} days left).\n\nRenew via M-Pesa:\n💳 {paybill_type}: *{paybill_no}*\n📝 Account: *{username}*\n💰 Amount: KES {amount}'],

            // ── Payment Received ──────────────────────────────────────
            ['payment_received','sms',null,'Payment confirmed! KES {amount} received for {username}. Your account is active until {expiry}. Ref: {reference}. {company}'],
            ['payment_received','email','Payment Confirmed - {company}','Dear {name},\n\nWe have received your payment.\n\nAmount: KES {amount}\nReference: {reference}\nPlan: {plan}\nActive Until: {expiry}\n\nThank you for choosing {company}!'],
            ['payment_received','whatsapp',null,'✅ *Payment Confirmed*\n\nHi {name}!\n💰 Amount: KES *{amount}*\n📋 Ref: {reference}\n📶 Plan: {plan}\n📅 Active until: *{expiry}*\n\nThank you! 🙏'],

            // ── Account Suspended ─────────────────────────────────────
            ['account_suspended','sms',null,'Hi {name}, your {company} account {username} has been suspended due to expiry. Pay KES {amount} via {paybill_type} {paybill_no} to restore.'],
            ['account_suspended','email','Account Suspended - Action Required','Dear {name},\n\nYour account {username} has been suspended.\n\nTo restore your service, please pay KES {amount} via:\n{paybill_type}: {paybill_no}\nAccount: {username}\n\nContact: {support_phone}\n\n{company}'],
            ['account_suspended','whatsapp',null,'🚫 *Account Suspended*\n\nHi {name}, your account *{username}* has been suspended.\n\nPay KES {amount} to restore:\n💳 {paybill_type}: *{paybill_no}*\n📝 Account: *{username}*\n\nSupport: {support_phone}'],

            // ── Account Activated ─────────────────────────────────────
            ['account_activated','sms',null,'Great news {name}! Your {company} internet account {username} is now active. Enjoy browsing! Plan: {plan}. Expires: {expiry}.'],
            ['account_activated','whatsapp',null,'🎉 *Account Activated!*\n\nHi {name}! Your internet is back!\n📶 Plan: *{plan}*\n📅 Expires: *{expiry}*\n\nEnjoy! 🌐'],

            // ── Account Expired ───────────────────────────────────────
            ['account_expired','sms',null,'Hi {name}, your {company} internet has expired. Renew by paying KES {amount} via {paybill_type} {paybill_no}, Account: {username}.'],
        ];

        foreach ($templates as $t) {
            NotificationTemplate::firstOrCreate(
                ['event' => $t[0], 'channel' => $t[1], 'reseller_id' => null],
                ['subject' => $t[2], 'body' => $t[3], 'active' => true]
            );
        }
    }
}