@extends('layouts.admin')
@section('title', 'Customers - Mobilink Admin')
@section('page-title', 'Customer Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">{{ $customers->total() }} total customers</p>
    <a href="{{ route('admin.customers.create') }}" class="bg-sky-600 text-white px-5 py-2 rounded-lg hover:bg-sky-700 transition-all font-semibold">
        <i class="fas fa-user-plus mr-2"></i>Add Customer
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">County</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($customers as $customer)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-sky-100 flex items-center justify-center font-bold text-sky-700">{{ substr($customer->first_name, 0, 1) }}</div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $customer->full_name }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $customer->id_number }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-gray-800">{{ $customer->email }}</p>
                    <p class="text-xs text-gray-500">{{ $customer->phone }}</p>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->county }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->plan->name ?? 'No Plan' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $customer->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $customer->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $customer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-1">
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-gray-600 hover:text-gray-800 text-sm"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="text-sky-600 hover:text-sky-800 text-sm ml-2"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this customer?')" class="text-red-500 hover:text-red-700 text-sm ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
</div>
@endsection
