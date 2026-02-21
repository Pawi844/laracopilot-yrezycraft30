@extends('layouts.admin')
@section('title', 'Reseller Settings: ' . $reseller->company_name)
@section('page-title', $reseller->company_name . ' — Settings')
@section('page-subtitle', 'Reseller-specific configuration: branding, gateway overrides, notifications')
@section('content')
<div class="max-w-3xl space-y-5">
    <!-- Tabs -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 flex space-x-2">
        <a href="{{ route('admin.resellers.settings', $reseller->id) }}" class="mk-tab active"><i class="fas fa-cog"></i> Settings</a>
        <a href="{{ route('admin.resellers.templates', $reseller->id) }}" class="mk-tab"><i class="fas fa-file-alt"></i> Templates</a>
        <a href="{{ route('admin.resellers.show', $reseller->id) }}" class="mk-tab"><i class="fas fa-arrow-left"></i> Back to Reseller</a>
    </div>

    <form action="{{ route('admin.resellers.settings.update', $reseller->id) }}" method="POST">
        @csrf @method('PUT')

        <!-- Branding -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                <i class="fas fa-paint-brush text-orange-400"></i>
                <h3 class="text-white font-bold text-sm">Branding</h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                @foreach([
                    ['branding','company_name','Company Name','text', $reseller->company_name],
                    ['branding','support_phone','Support Phone','text', $reseller->phone ?? ''],
                    ['branding','support_email','Support Email','text', $reseller->email ?? ''],
                    ['branding','logo_url','Logo URL','text', ''],
                    ['branding','website','Website','text', ''],
                    ['branding','address','Address','text', $reseller->address ?? ''],
                ] as [$grp,$key,$label,$type,$default])
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $grp }}__{{ $key }}" value="{{ \App\Models\ResellerSetting::get($reseller->id,$grp,$key,$default) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                @endforeach
            </div>
        </div>

        <!-- M-Pesa Override -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                <i class="fas fa-mobile-alt text-orange-400"></i>
                <h3 class="text-white font-bold text-sm">M-Pesa (Override)</h3>
                <span class="ml-auto text-blue-200 text-xs">Leave blank to use system default</span>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                @foreach([
                    ['mpesa','type','M-Pesa Type (paybill/till)'],
                    ['mpesa','shortcode','Paybill / Till Number'],
                    ['mpesa','account_reference','Account Reference'],
                ] as [$grp,$key,$label])
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $grp }}__{{ $key }}" value="{{ \App\Models\ResellerSetting::get($reseller->id,$grp,$key,'') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                @endforeach
            </div>
        </div>

        <!-- SMS Override -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                <i class="fas fa-sms text-orange-400"></i>
                <h3 class="text-white font-bold text-sm">SMS Sender (Override)</h3>
            </div>
            <div class="p-5 grid grid-cols-2 gap-4">
                @foreach([
                    ['sms','sender_id','SMS Sender ID'],
                    ['sms','api_key','API Key (if different)'],
                ] as [$grp,$key,$label])
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $grp }}__{{ $key }}" value="{{ \App\Models\ResellerSetting::get($reseller->id,$grp,$key,'') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-save mr-1"></i>Save Reseller Settings
            </button>
        </div>
    </form>
</div>
@endsection
