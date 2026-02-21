<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkZone;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $zones = NetworkZone::orderBy('county')->paginate(20);
        return view('admin.network.index', compact('zones'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.network.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'county' => 'required|string|max:100',
            'area' => 'required|string|max:255',
            'coverage_type' => 'required|in:4g,5g,fiber,wimax,satellite',
            'status' => 'required|in:active,planned,maintenance,limited',
            'signal_strength' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);
        NetworkZone::create($validated);
        return redirect()->route('admin.network.index')->with('success', 'Network zone added!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $zone = NetworkZone::findOrFail($id);
        return view('admin.network.edit', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $zone = NetworkZone::findOrFail($id);
        $validated = $request->validate([
            'county' => 'required|string|max:100',
            'area' => 'required|string|max:255',
            'coverage_type' => 'required|in:4g,5g,fiber,wimax,satellite',
            'status' => 'required|in:active,planned,maintenance,limited',
            'signal_strength' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);
        $zone->update($validated);
        return redirect()->route('admin.network.index')->with('success', 'Network zone updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        NetworkZone::findOrFail($id)->delete();
        return redirect()->route('admin.network.index')->with('success', 'Network zone removed!');
    }
}