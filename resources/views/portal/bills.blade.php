@extends('layouts.portal')
@section('title','My Bills')
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
        <h2 class="text-white font-black">Billing History</h2>
        <p class="text-blue-200 text-xs">All your invoices and payment records</p>
    </div>
    @if($invoices->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#f8fafc">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Invoice</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Period</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">M-Pesa Ref</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Paid At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-blue-700 font-bold">{{ $inv->invoice_no }}</td>
                    <td class="px-5 py-3">{{ $inv->plan_name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $inv->billing_period_start->format('d M') }} – {{ $inv->billing_period_end->format('d M Y') }}</td>
                    <td class="px-5 py-3 font-bold">KES {{ number_format($inv->amount,2) }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-green-700">{{ $inv->mpesa_ref ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : ($inv->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($inv->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $inv->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
    @else
    <div class="p-12 text-center"><i class="fas fa-file-invoice text-5xl text-gray-200 mb-3 block"></i><p class="text-gray-500">No invoices yet.</p></div>
    @endif
</div>
@endsection
