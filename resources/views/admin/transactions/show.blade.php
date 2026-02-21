@extends('layouts.admin')
@section('title', 'Transaction Details - Mobilink Admin')
@section('page-title', 'Transaction Details')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-800 font-mono">{{ $transaction->reference_number }}</h2>
                <p class="text-gray-500 text-sm">{{ $transaction->created_at->format('d M Y, H:i A') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold
                {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                {{ $transaction->status === 'refunded' ? 'bg-gray-100 text-gray-600' : '' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Customer</p>
                <p class="font-bold text-gray-800">{{ $transaction->customer->full_name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $transaction->customer->email ?? '' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Plan</p>
                <p class="font-bold text-gray-800">{{ $transaction->plan->name ?? 'N/A' }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Amount</p>
                <p class="text-3xl font-black text-green-700">KES {{ number_format($transaction->amount) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Payment Method</p>
                <p class="font-bold text-gray-800 uppercase">{{ str_replace('_', ' ', $transaction->payment_method) }}</p>
            </div>
        </div>
        @if($transaction->notes)
        <div class="mt-6 bg-yellow-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Notes</p>
            <p class="text-gray-700">{{ $transaction->notes }}</p>
        </div>
        @endif
        <div class="mt-6 flex space-x-3">
            <a href="{{ route('admin.transactions.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">← Back to Transactions</a>
        </div>
    </div>
</div>
@endsection
