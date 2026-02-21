@extends('layouts.admin')
@section('title','Transaction Details')
@section('page-title','Transaction Details')
@section('content')
<div class="max-w-xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <p class="text-gray-400 text-xs">Reference Number</p>
            <h2 class="text-2xl font-black text-gray-800 font-mono">{{ $transaction->reference_number }}</h2>
            <p class="text-gray-400 text-xs mt-1">{{ $transaction->created_at->format('d M Y, H:i A') }}</p>
        </div>
        <span class="px-3 py-1.5 rounded-full text-sm font-semibold
            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
            {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
            {{ ucfirst($transaction->status) }}
        </span>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-50 rounded-xl p-4"><p class="text-gray-400 text-xs mb-1">Customer</p><p class="text-gray-800 font-bold">{{ $transaction->customer->full_name ?? 'N/A' }}</p><p class="text-gray-500 text-xs">{{ $transaction->customer->email ?? '' }}</p></div>
        <div class="bg-green-50 rounded-xl p-4"><p class="text-gray-400 text-xs mb-1">Amount</p><p class="text-green-700 font-black text-3xl">KES {{ number_format($transaction->amount) }}</p></div>
        <div class="bg-gray-50 rounded-xl p-4"><p class="text-gray-400 text-xs mb-1">Payment Method</p><p class="text-gray-800 font-bold uppercase">{{ str_replace('_',' ',$transaction->payment_method) }}</p></div>
    </div>
    @if($transaction->notes)
    <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 mb-4"><p class="text-xs font-semibold text-orange-600 mb-1">Notes</p><p class="text-gray-700 text-sm">{{ $transaction->notes }}</p></div>
    @endif
    <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-2"></i>Back to Transactions</a>
</div>
</div>
@endsection
