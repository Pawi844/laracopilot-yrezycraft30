@extends('layouts.admin')
@section('title','Import Clients')
@section('page-title','Import Clients')
@section('page-subtitle','Bulk import clients from CSV file')
@section('content')
<div class="max-w-2xl space-y-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-gray-800 font-bold mb-3"><i class="fas fa-file-csv text-green-600 mr-2"></i>Upload CSV File</h3>
        <form action="{{ route('admin.clients.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-orange-400 transition-colors">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3 block"></i>
                <input type="file" name="file" accept=".csv,.txt" class="w-full" required>
                <p class="text-gray-400 text-xs mt-2">CSV file, max 5MB</p>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('admin.clients.import.template') }}" class="flex items-center space-x-1.5 border border-green-200 text-green-700 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-green-50">
                    <i class="fas fa-download"></i><span>Download Template</span>
                </a>
                <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-upload mr-1"></i>Import Clients</button>
            </div>
        </form>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-gray-700 font-bold text-sm mb-3">CSV Column Format</h3>
        <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead style="background:#f8fafc"><tr><th class="px-3 py-2 text-left text-gray-500">Column</th><th class="px-3 py-2 text-left text-gray-500">Required</th><th class="px-3 py-2 text-left text-gray-500">Example</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
            @foreach([
                ['username','Yes','john_doe'],
                ['first_name','Yes','John'],
                ['last_name','Yes','Doe'],
                ['phone','No','+254712345678'],
                ['email','No','john@email.com'],
                ['connection_type','No','pppoe'],
                ['plan_name','No','Home 10Mbps'],
                ['nas_shortname','No','NAS-001'],
                ['fat_code','No','FAT-WL-A1'],
                ['static_ip','No','10.10.1.5'],
                ['mac_address','No','AA:BB:CC:DD:EE:FF'],
                ['status','No','active'],
                ['expiry_date','No','2025-12-31'],
            ] as [$col,$req,$ex])
            <tr><td class="px-3 py-1.5 font-mono text-blue-700">{{ $col }}</td><td class="px-3 py-1.5"><span class="{{ $req==='Yes'?'text-red-600 font-semibold':'text-gray-400' }}">{{ $req }}</span></td><td class="px-3 py-1.5 text-gray-500">{{ $ex }}</td></tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
