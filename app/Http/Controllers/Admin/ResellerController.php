<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $resellers = Reseller::withCount(['clients','operators','plans'])->orderBy('created_at','desc')->paginate(20);
        return view('admin.resellers.index', compact('resellers'));
    }

    public function create() {
        $this->auth();
        $features = ['nas_management','tr069','hotspot','pppoe','notifications','reports','api_access'];
        return view('admin.resellers.create', compact('features'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:resellers',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'county' => 'nullable|string|max:100',
            'domain' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,suspended,pending'
        ]);
        $validated['allowed_features'] = $request->allowed_features ?? [];
        Reseller::create($validated);
        return redirect()->route('admin.resellers.index')->with('success', 'Reseller created!');
    }

    public function show($id) {
        $this->auth();
        $reseller = Reseller::withCount(['clients','operators','plans','nas','routers'])->findOrFail($id);
        return view('admin.resellers.show', compact('reseller'));
    }

    public function edit($id) {
        $this->auth();
        $reseller = Reseller::findOrFail($id);
        $features = ['nas_management','tr069','hotspot','pppoe','notifications','reports','api_access'];
        return view('admin.resellers.edit', compact('reseller','features'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $reseller = Reseller::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:resellers,email,'.$id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'county' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,suspended,pending'
        ]);
        $validated['allowed_features'] = $request->allowed_features ?? [];
        $reseller->update($validated);
        return redirect()->route('admin.resellers.index')->with('success', 'Reseller updated!');
    }

    public function destroy($id) {
        $this->auth();
        Reseller::findOrFail($id)->delete();
        return redirect()->route('admin.resellers.index')->with('success', 'Reseller deleted!');
    }
}