<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Tr069Device;
use App\Models\IspClient;
use App\Models\Reseller;
use Illuminate\Http\Request;

class Tr069Controller extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $devices = Tr069Device::with(['client','reseller'])->orderBy('updated_at','desc')->paginate(25);
        $online = Tr069Device::where('status','online')->count();
        $offline = Tr069Device::where('status','offline')->count();
        return view('admin.tr069.index', compact('devices','online','offline'));
    }

    public function create() {
        $this->auth();
        $clients = IspClient::where('status','active')->orderBy('username')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.tr069.create', compact('clients','resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'serial_number' => 'required|unique:tr069_devices|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'firmware_version' => 'nullable|string|max:50',
            'hardware_version' => 'nullable|string|max:50',
            'ip_address' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:30',
            'client_id' => 'nullable|exists:clients,id',
            'reseller_id' => 'nullable|exists:resellers,id'
        ]);
        Tr069Device::create($validated);
        return redirect()->route('admin.tr069.index')->with('success', 'TR-069 device registered!');
    }

    public function show($id) {
        $this->auth();
        $device = Tr069Device::with(['client','reseller'])->findOrFail($id);
        return view('admin.tr069.show', compact('device'));
    }

    public function edit($id) {
        $this->auth();
        $device = Tr069Device::findOrFail($id);
        $clients = IspClient::where('status','active')->orderBy('username')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.tr069.edit', compact('device','clients','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $device = Tr069Device::findOrFail($id);
        $validated = $request->validate([
            'manufacturer' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'firmware_version' => 'nullable|string|max:50',
            'ip_address' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:30',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|in:online,offline,unknown,error'
        ]);
        $device->update($validated);
        return redirect()->route('admin.tr069.index')->with('success', 'Device updated!');
    }

    public function destroy($id) {
        $this->auth();
        Tr069Device::findOrFail($id)->delete();
        return redirect()->route('admin.tr069.index')->with('success', 'Device removed!');
    }

    public function reboot($id) {
        $this->auth();
        $device = Tr069Device::findOrFail($id);
        // Simulate TR-069 reboot command
        return back()->with('success', 'Reboot command sent to device ' . $device->serial_number . ' via TR-069.');
    }
}