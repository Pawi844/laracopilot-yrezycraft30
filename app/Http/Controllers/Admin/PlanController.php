<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspPlan;
use App\Models\Reseller;
use Illuminate\Http\Request;

class PlanController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $plans = IspPlan::with('reseller')->orderBy('type')->orderBy('price')->paginate(20);
        return view('admin.plans.index', compact('plans'));
    }

    public function create() {
        $this->auth();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.plans.create', compact('resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pppoe,hotspot,static',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:hourly,daily,weekly,monthly,quarterly,yearly',
            'speed_download' => 'nullable|string|max:50',
            'speed_upload' => 'nullable|string|max:50',
            'data_limit' => 'nullable|string|max:50',
            'session_timeout' => 'nullable|integer',
            'idle_timeout' => 'nullable|integer',
            'address_pool' => 'nullable|string|max:100',
            'profile_name' => 'nullable|string|max:100',
            'burst_limit' => 'nullable|string|max:100',
            'burst_threshold' => 'nullable|string|max:100',
            'burst_time' => 'nullable|string|max:50',
            'reseller_id' => 'nullable|exists:resellers,id',
            'description' => 'nullable|string'
        ]);
        $validated['active'] = $request->has('active');
        IspPlan::create($validated);
        return redirect()->route('admin.plans.index')->with('success', 'Plan created!');
    }

    public function edit($id) {
        $this->auth();
        $plan = IspPlan::findOrFail($id);
        $resellers = Reseller::where('status','active')->get();
        return view('admin.plans.edit', compact('plan','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $plan = IspPlan::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pppoe,hotspot,static',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:hourly,daily,weekly,monthly,quarterly,yearly',
            'speed_download' => 'nullable|string|max:50',
            'speed_upload' => 'nullable|string|max:50',
            'data_limit' => 'nullable|string|max:50',
            'session_timeout' => 'nullable|integer',
            'idle_timeout' => 'nullable|integer',
            'address_pool' => 'nullable|string|max:100',
            'profile_name' => 'nullable|string|max:100',
            'burst_limit' => 'nullable|string|max:100',
            'description' => 'nullable|string'
        ]);
        $validated['active'] = $request->has('active');
        $plan->update($validated);
        return redirect()->route('admin.plans.index')->with('success', 'Plan updated!');
    }

    public function destroy($id) {
        $this->auth();
        IspPlan::findOrFail($id)->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted!');
    }
}