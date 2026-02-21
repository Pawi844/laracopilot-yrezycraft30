<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $services = Service::orderBy('created_at','desc')->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create() { $this->auth(); return view('admin.services.create'); }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate(['name'=>'required|string|max:255','slug'=>'required|unique:services|string','description'=>'required|string','short_description'=>'required|string|max:500','icon'=>'required|string|max:100','category'=>'required|in:internet,mobile,tv,business,voip']);
        $validated['active'] = $request->has('active');
        Service::create($validated);
        return redirect()->route('admin.services.index')->with('success','Service created!');
    }

    public function edit($id) { $this->auth(); $service = Service::findOrFail($id); return view('admin.services.edit', compact('service')); }

    public function update(Request $request, $id) {
        $this->auth();
        $service = Service::findOrFail($id);
        $validated = $request->validate(['name'=>'required|string|max:255','slug'=>'required|unique:services,slug,'.$id,'description'=>'required|string','short_description'=>'required|string|max:500','icon'=>'required|string|max:100','category'=>'required|in:internet,mobile,tv,business,voip']);
        $validated['active'] = $request->has('active');
        $service->update($validated);
        return redirect()->route('admin.services.index')->with('success','Service updated!');
    }

    public function destroy($id) { $this->auth(); Service::findOrFail($id)->delete(); return redirect()->route('admin.services.index')->with('success','Service deleted!'); }
}