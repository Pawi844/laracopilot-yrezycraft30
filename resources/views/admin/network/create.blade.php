@extends('layouts.admin')
@section('title', 'Add Network Zone')
@section('page-title', 'Add Network Zone')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="{{ route('admin.network.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">County <span class="text-red-500">*</span></label>
                    <input type="text" name="county" value="{{ old('county') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="e.g. Nairobi" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Area/Suburb <span class="text-red-500">*</span></label>
                    <input type="text" name="area" value="{{ old('area') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="e.g. Westlands" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Coverage Type <span class="text-red-500">*</span></label>
                    <select name="coverage_type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
                        @foreach(['4g', '5g', 'fiber', 'wimax', 'satellite'] as $t)
                            <option value="{{ $t }}" {{ old('coverage_type') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
                        @foreach(['active', 'planned', 'maintenance', 'limited'] as $s)
                            <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Signal Strength (1-5) <span class="text-red-500">*</span></label>
                    <input type="number" name="signal_strength" value="{{ old('signal_strength', 3) }}" min="0" max="5" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.network.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 text-white rounded-lg hover:bg-sky-700 font-semibold"><i class="fas fa-save mr-2"></i>Add Zone</button>
            </div>
        </form>
    </div>
</div>
@endsection
