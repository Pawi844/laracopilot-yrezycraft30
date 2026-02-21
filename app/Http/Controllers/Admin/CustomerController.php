<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $plans = Plan::where('active', true)->with('service')->get();
        return view('admin.customers.create', compact('plans'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:customers',
            'address' => 'required|string',
            'county' => 'required|string|max:100',
            'plan_id' => 'nullable|exists:plans,id',
            'status' => 'required|in:active,inactive,suspended,pending'
        ]);
        Customer::create($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::with(['plan', 'transactions', 'supportTickets'])->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $plans = Plan::where('active', true)->with('service')->get();
        return view('admin.customers.edit', compact('customer', 'plans'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email,' . $id,
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:customers,id_number,' . $id,
            'address' => 'required|string',
            'county' => 'required|string|max:100',
            'plan_id' => 'nullable|exists:plans,id',
            'status' => 'required|in:active,inactive,suspended,pending'
        ]);
        $customer->update($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Customer::findOrFail($id)->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully!');
    }
}