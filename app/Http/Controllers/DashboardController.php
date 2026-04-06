<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use App\Models\Order; 

class DashboardController extends Controller
{
    public function admin()
    {
        $totalUsers = Client::count() + Freelancer::count() + SkomdaStudent::count();
        $totalClients = Client::count();
        $totalFreelancers = Freelancer::count();
        $totalSkomda = SkomdaStudent::count();

        return view('dashboard.admin.dashboard', compact(
            'totalUsers',
            'totalClients',
            'totalFreelancers',
            'totalSkomda'
        ));
    }

    public function client()
    {
        $user = auth()->guard('client')->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $allOrders = Order::where('client_id', $user->id)->get();

        $activeProjects = $allOrders
            ->whereIn('status', ['Pending', 'In Progress'])
            ->count();

        $totalSpent = $allOrders
            ->where('status', '!=', 'Cancelled')
            ->sum('agreed_price');

        $completedProjects = $allOrders
            ->where('status', 'Completed')
            ->count();

        $projects = Order::with('service')
            ->where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.client.dashboard', compact(
            'user',
            'projects',
            'activeProjects',
            'totalSpent',
            'completedProjects'
        ));
    }

    public function freelancer()
    {
        return view('dashboard.freelancer.dashboard');
    }

    public function verifyFreelancer($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update(['status' => 'Approved']);

        return response()->json(['message' => 'Success']);
    }

    public function rejectFreelancer($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update(['status' => 'Rejected']);

        return response()->json(['message' => 'Success']);
    }

    public function user()
    {
        $clients = Client::latest()->get();
        $freelancers = Freelancer::with('skomda_student')->latest()->get();
        $skomdaStudents = SkomdaStudent::latest()->get();

        return view('admin.admin-user', compact(
            'clients',
            'freelancers',
            'skomdaStudents'
        ));
    }

    public function settings()
    {
        return view('dashboard.admin.settings');
    }
}
