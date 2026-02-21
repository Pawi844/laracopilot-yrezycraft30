<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $operators = Operator::with('reseller')->orderBy('created_at','desc')->paginate(20);
        return view('admin.operators.index', compact('operators'));
    }

    public function create() {
        $this->auth();
        $resellers = Reseller::where('status','active')->get();
        $allPermissions = ['view_clients','edit_clients','delete_clients','view_plans','edit_plans','view_nas','edit_nas','view_sessions','view_transactions','send_notifications','view_reports'];
        return view('admin.operators.create', compact('resellers','allPermissions'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|unique:operators|max:100',
            'email' => 'required|email|unique:operators',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,operator,support',
            'reseller_id' => 'nullable|exists:resellers,id'
        ]);
        $validated['password'] = Hash::make($request->password);
        $validated['permissions'] = $request->permissions ?? [];
        $validated['active'] = $request->has('active');
        Operator::create($validated);
        return redirect()->route('admin.operators.index')->with('success', 'Operator created!');
    }

    public function edit($id) {
        $this->auth();
        $operator = Operator::findOrFail($id);
        $resellers = Reseller::where('status','active')->get();
        $allPermissions = ['view_clients','edit_clients','delete_clients','view_plans','edit_plans','view_nas','edit_nas','view_sessions','view_transactions','send_notifications','view_reports'];
        return view('admin.operators.edit', compact('operator','resellers','allPermissions'));
    }

    public function update(Request $request, $id) {
        $this->auth();
        $operator = Operator::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:operators,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,operator,support',
            'reseller_id' => 'nullable|exists:resellers,id'
        ]);
        if ($request->filled('password')) $validated['password'] = Hash::make($request->password);
        $validated['permissions'] = $request->permissions ?? [];
        $validated['active'] = $request->has('active');
        $operator->update($validated);
        return redirect()->route('admin.operators.index')->with('success', 'Operator updated!');
    }

    public function destroy($id) {
        $this->auth();
        Operator::findOrFail($id)->delete();
        return redirect()->route('admin.operators.index')->with('success', 'Operator deleted!');
    }
}