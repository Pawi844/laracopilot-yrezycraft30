@extends('layouts.admin')
@section('title', 'Transactions - Mobilink Admin')
@section('page-title', 'Transaction Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">Total Revenue: <strong class="text-green-600">KES {{ number_format($totalRevenue) }}</strong></p>
    <a href="{{ route('admin.transactions.create') }}" class="bg-sky-600 text-white px-5 py-2 rounded-lg hover:bg-sky-700 font-semibold">
        <i class="fas fa-plus mr-2"></i>Record Transaction
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $txn)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-mono text-gray-600"><a href="{{ route('admin.transactions.show', $txn->id) }}" class="text-sky-600 hover:underline">{{ $txn->reference_number }}</a></td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $txn->customer->full_name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $txn->plan->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm font-bold text-gray-800">KES {{ number_format($txn->amount) }}</td>
                <td class="px-6 py-4"><span class="text-xs uppercase bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ str_replace('_', ' ', $txn->payment_method) }}</span></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $txn->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $txn->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $txn->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $txn->status === 'refunded' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst($txn->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">{{ $txn->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $transactions->links() }}</div>
</div>
@endsection
