@extends('layouts.admin')
@section('title','Transactions')
@section('page-title','Transactions')
@section('page-subtitle','Payment records')
@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3">
        <p class="text-gray-400 text-xs">Total Revenue</p>
        <p class="text-green-600 font-black text-xl">KES {{ number_format($totalRevenue) }}</p>
    </div>
    <a href="{{ route('admin.transactions.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Record Payment
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($transactions as $txn)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3"><a href="{{ route('admin.transactions.show', $txn->id) }}" class="text-blue-600 font-mono text-xs font-bold hover:underline">{{ $txn->reference_number }}</a></td>
                <td class="px-4 py-3 text-gray-700 text-xs font-medium">{{ $txn->customer->full_name ?? ($txn->customer->first_name ?? 'N/A') }}</td>
                <td class="px-4 py-3 text-green-600 font-bold text-sm">KES {{ number_format($txn->amount) }}</td>
                <td class="px-4 py-3"><span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded uppercase">{{ str_replace('_',' ',$txn->payment_method) }}</span></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $txn->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $txn->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $txn->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $txn->status === 'refunded' ? 'bg-gray-100 text-gray-600' : '' }}">{{ ucfirst($txn->status) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.transactions.show', $txn->id) }}" class="text-blue-500 hover:text-blue-700 text-xs"><i class="fas fa-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $transactions->links() }}</div>
</div>
@endsection
