@extends('layouts.admin')
@section('title', 'Customer Details - Mobilink Admin')
@section('page-title', 'Customer Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-center mb-4">
                <div class="w-20 h-20 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-3xl font-bold text-sky-700">{{ substr($customer->first_name, 0, 1) }}</span>
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ $customer->full_name }}</h2>
                <p class="text-gray-500 text-sm">{{ $customer->email }}</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2
                    {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $customer->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                    {{ $customer->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $customer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium">{{ $customer->phone }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">ID Number</span><span class="font-medium">{{ $customer->id_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">County</span><span class="font-medium">{{ $customer->county }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Address</span><span class="font-medium text-right">{{ $customer->address }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Member Since</span><span class="font-medium">{{ $customer->created_at->format('d M Y') }}</span></div>
            </div>
            @if($customer->plan)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Current Plan</p>
                <div class="bg-sky-50 rounded-lg p-3">
                    <p class="font-bold text-sky-800">{{ $customer->plan->name }}</p>
                    <p class="text-sm text-sky-600">KES {{ number_format($customer->plan->price) }}/{{ $customer->plan->billing_cycle }}</p>
                </div>
            </div>
            @endif
            <div class="mt-4 flex space-x-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="flex-1 text-center bg-sky-600 text-white py-2 rounded-lg hover:bg-sky-700 text-sm font-semibold">Edit</a>
                <a href="{{ route('admin.customers.index') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 text-sm font-semibold">Back</a>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">Transaction History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customer->transactions as $txn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $txn->reference_number }}</td>
                            <td class="px-6 py-3 text-sm font-bold">KES {{ number_format($txn->amount) }}</td>
                            <td class="px-6 py-3 text-sm capitalize">{{ str_replace('_', ' ', $txn->payment_method) }}</td>
                            <td class="px-6 py-3"><span class="text-xs px-2 py-0.5 rounded-full {{ $txn->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($txn->status) }}</span></td>
                            <td class="px-6 py-3 text-xs text-gray-500">{{ $txn->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">No transactions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">Support Tickets</h3>
            </div>
            <div class="p-6 space-y-3">
                @forelse($customer->supportTickets as $ticket)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-500">{{ $ticket->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($ticket->priority) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-4">No support tickets found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
