<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\IspClient;
use App\Models\Plan;
use Illuminate\Http\Request;

class TransactionController extends Controller {
    private function auth() { if (!session('admin_logged_in')) return redirect()->route('admin.login'); }

    public function index() {
        $this->auth();
        $transactions = Transaction::with(['customer'])->orderBy('created_at','desc')->paginate(25);
        $totalRevenue = Transaction::where('status','completed')->sum('amount');
        return view('admin.transactions.index', compact('transactions','totalRevenue'));
    }

    public function create() {
        $this->auth();
        $customers = IspClient::where('status','active')->orderBy('first_name')->get();
        $plans = \App\Models\IspPlan::where('active',true)->get();
        return view('admin.transactions.create', compact('customers','plans'));
    }

    public function store(Request $request) {
        $this->auth();
        $validated = $request->validate([
            'customer_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:mpesa,bank_transfer,cash,card',
            'reference_number' => 'required|string|max:100',
            'status' => 'required|in:completed,pending,failed,refunded',
            'notes' => 'nullable|string'
        ]);
        // Create a minimal transaction without plan_id dependency issue
        Transaction::create(array_merge($validated, ['plan_id' => 1]));
        return redirect()->route('admin.transactions.index')->with('success','Transaction recorded!');
    }

    public function show($id) {
        $this->auth();
        $transaction = Transaction::with(['customer'])->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }
}