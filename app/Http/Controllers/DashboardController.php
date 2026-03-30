<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Order;
use App\Models\SkomdaStudent;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalUsers = Client::count() + Freelancer::count() + SkomdaStudent::count();
        $activeProjects = Order::whereIn('status', ['Pending', 'In Progress'])->count();
        $totalRevenue = Order::where('status', 'Completed')->sum('agreed_price');
        $openDisputes = Order::where('status', 'Disputed')->count();

        $pendingVerifications = Freelancer::with('user')
            ->where('status', 'Pending') 
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin.dashboard', compact(
            'totalUsers',
            'activeProjects',
            'totalRevenue',
            'openDisputes',
            'pendingVerifications'
        ));
    }

    public function client()
    {
        $user = auth()->guard('client')->user();

        // 1. Ambil semua order milik client ini
        $allOrders = Order::where('client_id', $user->id)->get();

        // 2. Hitung statistik
        $activeProjects = $allOrders->whereIn('status', ['Pending', 'In Progress'])->count();

        // Hitung total pengeluaran (agreed_price) dari order yang statusnya bukan ditolak/cancel
        $totalSpent = $allOrders->where('status', '!=', 'Cancelled')->sum('agreed_price');

        $completedProjects = $allOrders->where('status', 'Completed')->count();

        // 3. Ambil 3 project terbaru buat list di bawah (sama kayak sebelumnya)
        $projects = Order::with('service')
            ->where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.client.dashboard', compact('user', 'projects', 'activeProjects', 'totalSpent', 'completedProjects'));
    }

    public function freelancer()
    {
        return view('dashboard.freelancer.dashboard');
    }

    public function verifyFreelancer($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update(['status' => 'Approved']); // Pastikan kolom status ada di tabel freelancers
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
        $freelancers = Freelancer::with('user')->latest()->get();
        $skomdaStudents = SkomdaStudent::latest()->get();

        return view('admin.admin-user', compact('clients', 'freelancers', 'skomdaStudents'));
    }
}
