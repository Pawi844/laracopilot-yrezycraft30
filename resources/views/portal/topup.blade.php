@extends('layouts.portal')
@section('title','Top Up')
@section('content')
<div class="max-w-lg space-y-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-gray-800 font-black text-lg mb-4"><i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Pay via M-Pesa</h2>
        @if($mpesa['shortcode'])
        <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-4">
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-gray-400 text-xs">{{ ucfirst($mpesa['type']) }} Number</p><p class="text-green-700 font-black text-3xl">{{ $mpesa['shortcode'] }}</p></div>
                <div><p class="text-gray-400 text-xs">Account Reference</p><p class="text-gray-800 font-black text-xl">{{ $client->username }}</p></div>
                <div><p class="text-gray-400 text-xs">Plan Amount</p><p class="text-gray-800 font-bold">KES {{ $client->plan?->price ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs">Plan</p><p class="text-gray-800 font-bold">{{ $client->plan?->name ?? '—' }}</p></div>
            </div>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
            <p class="font-bold text-gray-700">How to pay via M-Pesa:</p>
            <ol class="list-decimal ml-5 space-y-1">
                <li>Go to <strong>M-Pesa</strong> on your phone</li>
                <li>Select <strong>{{ ucfirst($mpesa['type']) === 'Paybill' ? 'Pay Bill' : 'Buy Goods & Services' }}</strong></li>
                <li>Enter <strong>{{ ucfirst($mpesa['type']) }} No: {{ $mpesa['shortcode'] }}</strong></li>
                @if($mpesa['type'] === 'paybill')
                <li>Enter <strong>Account No: {{ $client->username }}</strong></li>
                @endif
                <li>Enter <strong>Amount: KES {{ $client->plan?->price ?? '...' }}</strong></li>
                <li>Enter your M-Pesa PIN and confirm</li>
            </ol>
            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-xl p-3">
                <p class="text-blue-700 text-xs"><i class="fas fa-robot mr-1"></i>Your account will be renewed <strong>automatically</strong> within 1-2 minutes of payment confirmation.</p>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>Online payments are not yet configured. Please contact support to pay.</p>
        </div>
        @endif
        <div class="mt-5 border-t border-gray-100 pt-4">
            <p class="text-gray-500 text-sm">Need help? Call us: <strong>{{ \App\Models\SystemSetting::get('general','company_phone','') }}</strong></p>
        </div>
    </div>
</div>
@endsection
