@extends('layouts.admin')
@section('title','Record Payment')
@section('page-title','Record Payment')
@section('content')
<div class="max-w-xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.transactions.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Customer *</label>
                <select name="customer_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->username }} — {{ $c->full_name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Amount (KES) *</label><input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Payment Method *</label>
                <select name="payment_method" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="mpesa">M-Pesa</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reference Number *</label><input type="text" name="reference_number" value="{{ old('reference_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="MP123456" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status *</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">{{ old('notes') }}</textarea></div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Record Payment</button>
        </div>
    </form>
</div>
</div>
@endsection
