<?php

namespace App\Http\Controllers;

use App\Models\Order;

class DashboardController extends Controller
{

    public function admin()
    {
        $totalUsers = \App\Models\Client::count() + \App\Models\Freelancer::count();
        $activeProjects = \App\Models\Order::whereIn('status', ['pending', 'in progress'])->count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('agreed_price');
        $openDisputes = \App\Models\Order::where('status', 'disputed')->count();

        $pendingVerifications = \App\Models\Freelancer::with('user')
            ->where('status', 'pending')  // sesuaikan value status pending di DB kamu
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin-dashboard', compact(
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
        $allOrders = \App\Models\Order::where('client_id', $user->id)->get();

        // 2. Hitung statistik
        $activeProjects = $allOrders->whereIn('status', ['pending', 'in progress'])->count();

        // Hitung total pengeluaran (agreed_price) dari order yang statusnya bukan ditolak/cancel
        $totalSpent = $allOrders->where('status', '!=', 'cancelled')->sum('agreed_price');

        $completedProjects = $allOrders->where('status', 'completed')->count();

        // 3. Ambil 3 project terbaru buat list di bawah (sama kayak sebelumnya)
        $projects = \App\Models\Order::with('service')
            ->where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('client-dashboard', compact('user', 'projects', 'activeProjects', 'totalSpent', 'completedProjects'));
    }

    public function freelancer()
    {
        return view('freelancer-dashboard');
    }

    public function verifyFreelancer($id)
    {
        $freelancer = \App\Models\Freelancer::findOrFail($id);
        $freelancer->update(['status' => 'Approved']); // Pastikan kolom status ada di tabel freelancers
        return response()->json(['message' => 'Success']);
    }

    public function rejectFreelancer($id)
    {
        $freelancer = \App\Models\Freelancer::findOrFail($id);
        $freelancer->update(['status' => 'Rejected']);
        return response()->json(['message' => 'Success']);
    }

    public function user()
    {
        $clients = \App\Models\Client::latest()->get();
        $freelancers = \App\Models\Freelancer::with('user')->latest()->get();
        $skomdaStudents = \App\Models\SkomdaStudent::latest()->get();

        return view('admin.admin-user', compact('clients', 'freelancers', 'skomdaStudents'));
    }
}
