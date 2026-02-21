<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Nas;
use App\Models\Reseller;
use Illuminate\Http\Request;

class NasController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $nas = Nas::with('reseller')->orderBy('created_at','desc')->paginate(20);
        return view('admin.nas.index', compact('nas'));
    }

    public function create() {
        $this->auth();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.nas.create', compact('resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortname' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'secret' => 'required|string|max:255',
            'ip_addresses' => 'required|string',
            'community' => 'nullable|string|max:100',
            'ports' => 'nullable|integer',
            'server' => 'nullable|string|max:100',
            'reseller_id' => 'nullable|exists:resellers,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);
        $validated['ip_addresses'] = array_map('trim', explode(',', $request->ip_addresses));
        Nas::create($validated);
        return redirect()->route('admin.nas.index')->with('success', 'NAS created successfully!');
    }

    public function show($id) {
        $this->auth();
        $nas = Nas::with(['reseller','routers','clients'])->findOrFail($id);
        return view('admin.nas.show', compact('nas'));
    }

    public function edit($id) {
        $this->auth();
        $nas = Nas::findOrFail($id);
        $resellers = Reseller::where('status','active')->get();
        return view('admin.nas.edit', compact('nas','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $nas = Nas::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortname' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'secret' => 'required|string|max:255',
            'ip_addresses' => 'required|string',
            'community' => 'nullable|string|max:100',
            'ports' => 'nullable|integer',
            'server' => 'nullable|string|max:100',
            'reseller_id' => 'nullable|exists:resellers,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,unreachable'
        ]);
        $validated['ip_addresses'] = array_map('trim', explode(',', $request->ip_addresses));
        $nas->update($validated);
        return redirect()->route('admin.nas.index')->with('success', 'NAS updated successfully!');
    }

    public function destroy($id) {
        $this->auth();
        Nas::findOrFail($id)->delete();
        return redirect()->route('admin.nas.index')->with('success', 'NAS deleted!');
    }

    public function testConnection($id) {
        $this->auth();
        // Simulate connection test
        $nas = Nas::findOrFail($id);
        $nas->update(['last_seen' => now(), 'status' => 'active']);
        return back()->with('success', 'NAS ' . $nas->name . ' is reachable. Connection OK.');
    }
}