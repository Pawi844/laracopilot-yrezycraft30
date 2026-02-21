<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Plan;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $services = Service::where('active', true)->limit(6)->get();
        $plans = Plan::where('active', true)->where('featured', true)->with('service')->limit(3)->get();
        return view('public.home', compact('services', 'plans'));
    }

    public function services()
    {
        $services = Service::where('active', true)->get();
        return view('public.services', compact('services'));
    }

    public function plans()
    {
        $plans = Plan::where('active', true)->with('service')->orderBy('price')->get();
        $services = Service::where('active', true)->get();
        return view('public.plans', compact('plans', 'services'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:20'
        ]);

        SupportTicket::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
            'priority' => 'medium',
            'customer_id' => null
        ]);

        return back()->with('success', 'Your message has been received! We will get back to you within 24 hours.');
    }

    public function coverage()
    {
        return view('public.coverage');
    }
}