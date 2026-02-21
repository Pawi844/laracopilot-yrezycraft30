<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspPlan;
use App\Models\Reseller;
use Illuminate\Http\Request;

class HotspotController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $plans = IspPlan::with('reseller')->where('type','hotspot')->orderBy('price')->paginate(20);
        return view('admin.hotspot.index', compact('plans'));
    }

    public function create() {
        $this->auth();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.hotspot.create', compact('resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:hourly,daily,weekly,monthly',
            'speed_download' => 'nullable|string|max:50',
            'speed_upload' => 'nullable|string|max:50',
            'data_limit' => 'nullable|string|max:50',
            'session_timeout' => 'nullable|integer',
            'idle_timeout' => 'nullable|integer',
            'profile_name' => 'nullable|string|max:100',
            'reseller_id' => 'nullable|exists:resellers,id',
            'description' => 'nullable|string'
        ]);
        $validated['type'] = 'hotspot';
        $validated['active'] = $request->has('active');
        IspPlan::create($validated);
        return redirect()->route('admin.hotspot.index')->with('success', 'Hotspot plan created!');
    }

    public function edit($id) {
        $this->auth();
        $plan = IspPlan::where('type','hotspot')->findOrFail($id);
        $resellers = Reseller::where('status','active')->get();
        return view('admin.hotspot.edit', compact('plan','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $plan = IspPlan::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:hourly,daily,weekly,monthly',
            'speed_download' => 'nullable|string|max:50',
            'speed_upload' => 'nullable|string|max:50',
            'data_limit' => 'nullable|string|max:50',
            'session_timeout' => 'nullable|integer',
            'profile_name' => 'nullable|string|max:100',
            'description' => 'nullable|string'
        ]);
        $validated['active'] = $request->has('active');
        $plan->update($validated);
        return redirect()->route('admin.hotspot.index')->with('success', 'Hotspot plan updated!');
    }

    public function destroy($id) {
        $this->auth();
        IspPlan::where('type','hotspot')->findOrFail($id)->delete();
        return redirect()->route('admin.hotspot.index')->with('success', 'Hotspot plan deleted!');
    }
}