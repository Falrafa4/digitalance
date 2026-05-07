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

    public function search(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');
        
        $results = collect();
        
        if ($q) {
            // Search Menus
            $menus = collect([
                ['name' => 'Dashboard', 'url' => route('admin.dashboard'), 'desc' => 'Ringkasan statistik dan aktivitas terbaru'],
                ['name' => 'Users & Clients', 'url' => route('admin.user'), 'desc' => 'Kelola data pengguna, klien, dan siswa'],
                ['name' => 'Freelancers', 'url' => route('admin.freelancers.index'), 'desc' => 'Daftar talent dan verifikasi data'],
                ['name' => 'Orders Management', 'url' => route('admin.orders.index'), 'desc' => 'Kelola pesanan dan status proyek'],
                ['name' => 'Services Catalog', 'url' => route('admin.services.index'), 'desc' => 'Manajemen layanan dan kategori'],
                ['name' => 'Offers', 'url' => route('admin.offers.index'), 'desc' => 'Penawaran masuk dan request custom'],
                ['name' => 'Transactions', 'url' => route('admin.transactions.index'), 'desc' => 'Riwayat pembayaran dan keuangan'],
                ['name' => 'Reviews', 'url' => route('admin.reviews.index'), 'desc' => 'Ulasan klien terhadap freelancer'],
                ['name' => 'Settings', 'url' => route('admin.settings'), 'desc' => 'Pengaturan dashboard dan panduan'],
                ['name' => 'Profile', 'url' => route('admin.profile'), 'desc' => 'Pengaturan akun administrator'],
            ])->filter(function($menu) use ($q) {
                return stripos($menu['name'], $q) !== false || stripos($menu['desc'], $q) !== false;
            })->map(function($item) {
                return (object) [
                    'title' => $item['name'],
                    'email' => $item['desc'], // hack to use the email field for description
                    'search_type' => 'Menu',
                    'search_url' => $item['url'],
                    'created_at' => now(), // dummy
                ];
            });

            // Search Clients
            $clients = Client::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->get()->map(function($item) {
                    $item->search_type = 'Client';
                    $item->search_url = route('admin.clients.index') . '?q=' . urlencode($item->name);
                    return $item;
                });
                
            // Search Freelancers
            $freelancers = Freelancer::whereHas('skomda_student', function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                })
                ->get()->map(function($item) {
                    $item->search_type = 'Freelancer';
                    $item->search_url = route('admin.freelancers.index') . '?q=' . urlencode($item->name);
                    return $item;
                });
                
            // Search Services
            $services = Service::where('title', 'like', "%{$q}%")
                ->get()->map(function($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('admin.services.index') . '?q=' . urlencode($item->title);
                    return $item;
                });
                
            // Search Orders
            $orders = Order::whereHas('client', function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->orWhereHas('service', function($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%");
                })
                ->get()->map(function($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('admin.orders.index');
                    return $item;
                });
                
            $results = $results->concat($menus)->concat($clients)->concat($freelancers)->concat($services)->concat($orders);
        }
        
        return view('dashboard.admin.search', compact('results', 'q'));
    }

    public function clientSearch(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');
        $user = auth()->guard('client')->user();
        $results = collect();
        
        if ($q && $user) {
            // Search Talents (Freelancers)
            $freelancers = Freelancer::whereHas('skomda_student', function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->get()->map(function($item) {
                    $item->search_type = 'Talent';
                    $item->search_url = route('client.talents.show', $item->id);
                    return $item;
                });
                
            // Search Services
            $services = Service::where('title', 'like', "%{$q}%")
                ->get()->map(function($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('client.services.show', $item->id);
                    return $item;
                });
                
            // Search Orders (Own)
            $orders = Order::where('client_id', $user->id)
                ->whereHas('service', function($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%");
                })
                ->get()->map(function($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('client.orders.show', $item->id);
                    return $item;
                });
                
            $results = $results->concat($freelancers)->concat($services)->concat($orders);
        }
        
        return view('dashboard.admin.search', compact('results', 'q')); // Reuse the same search view for now
    }

    public function freelancerSearch(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');
        $user = auth()->guard('freelancer')->user();
        $results = collect();
        
        if ($q && $user) {
            // Search Own Services
            $services = Service::where('freelancer_id', $user->id)
                ->where('title', 'like', "%{$q}%")
                ->get()->map(function($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('freelancer.services.show', $item->id);
                    return $item;
                });
                
            // Search Orders (Received)
            $orders = Order::whereHas('service', function($query) use ($user) {
                    $query->where('freelancer_id', $user->id);
                })
                ->whereHas('client', function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->get()->map(function($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('freelancer.orders.index');
                    return $item;
                });
                
            $results = $results->concat($services)->concat($orders);
        }
        
        return view('dashboard.admin.search', compact('results', 'q')); // Reuse the same search view
    }
}
