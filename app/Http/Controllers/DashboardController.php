<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\SkomdaStudent;
use App\Models\Service;
use App\Models\Transaction;

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
        $freelancer = auth('freelancer')->user();

        if (!$freelancer) {
            abort(403, 'Unauthorized');
        }

        $orders = Order::with(['client', 'service'])
            ->whereHas('service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->latest()
            ->get();

        $activeOrders = $orders->whereIn('status', ['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision'])->count();
        $servicesCount = Service::where('freelancer_id', $freelancer->id)->count();
        $avgRating =
            \App\Models\Review::whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })->avg('rating');
        $balance = Transaction::whereHas('order.service', function ($query) use ($freelancer) {
            $query->where('freelancer_id', $freelancer->id);
        })->whereIn('status', ['Paid', 'Success'])->sum('amount');

        $latestOrders = $orders->take(6)->map(function ($order) {
            return [
                'id' => $order->id,
                'title' => $order->service->title ?? 'Service',
                'client_name' => $order->client->name ?? 'Client',
                'status' => $order->status ?? 'Pending',
                'agreed_price' => $order->agreed_price,
                'created_at' => $order->created_at,
            ];
        })->values();

        $jobOpportunities = Offer::with(['order.client', 'order.service'])
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($offer) {
                return [
                    'id' => $offer->id,
                    'title' => $offer->order->service->title ?? $offer->title ?? 'Job',
                    'client_name' => $offer->order->client->name ?? 'Client',
                    'status' => $offer->status ?? 'New',
                    'budget' => $offer->offered_price,
                    'url' => route('freelancer.offers.index'),
                ];
            })
            ->values();

        $dashboardData = [
            'stats' => [
                'activeOrders' => $activeOrders,
                'services' => $servicesCount,
                'avgRating' => $avgRating ? number_format((float) $avgRating, 1) : '0.0',
                'balance' => (float) $balance,
            ],
            'latestOrders' => $latestOrders,
            'jobOpportunities' => $jobOpportunities,
        ];

        return view('dashboard.freelancer.dashboard', compact('dashboardData'));
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
