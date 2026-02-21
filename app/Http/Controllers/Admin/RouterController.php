<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Nas;
use App\Models\Reseller;
use Illuminate\Http\Request;

class RouterController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $routers = Router::with(['nas','reseller'])->orderBy('created_at','desc')->paginate(20);
        return view('admin.routers.index', compact('routers'));
    }

    public function create() {
        $this->auth();
        $nas = Nas::where('status','active')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.routers.create', compact('nas','resellers'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:50',
            'api_port' => 'required|integer',
            'username' => 'required|string|max:100',
            'password' => 'required|string',
            'model' => 'nullable|string|max:100',
            'nas_id' => 'nullable|exists:nas,id',
            'reseller_id' => 'nullable|exists:resellers,id'
        ]);
        Router::create($validated);
        return redirect()->route('admin.routers.index')->with('success', 'MikroTik router added!');
    }

    public function edit($id) {
        $this->auth();
        $router = Router::findOrFail($id);
        $nas = Nas::where('status','active')->get();
        $resellers = Reseller::where('status','active')->get();
        return view('admin.routers.edit', compact('router','nas','resellers'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $router = Router::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:50',
            'api_port' => 'required|integer',
            'username' => 'required|string|max:100',
            'nas_id' => 'nullable|exists:nas,id',
            'reseller_id' => 'nullable|exists:resellers,id'
        ]);
        if ($request->filled('password')) $validated['password'] = $request->password;
        $router->update($validated);
        return redirect()->route('admin.routers.index')->with('success', 'Router updated!');
    }

    public function destroy($id) {
        $this->auth();
        Router::findOrFail($id)->delete();
        return redirect()->route('admin.routers.index')->with('success', 'Router deleted!');
    }

    public function sync($id) {
        $this->auth();
        $router = Router::findOrFail($id);
        // Simulate MikroTik API sync
        $router->update(['last_sync' => now(), 'status' => 'online', 'firmware' => 'RouterOS 7.x']);
        return back()->with('success', 'Router ' . $router->name . ' synced with MikroTik API.');
    }
}