<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Service;
use App\Models\SkomdaStudent;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $totalUsers = Client::count() + Freelancer::count() + SkomdaStudent::count();
        $totalClients = Client::count();
        $totalFreelancers = Freelancer::count();
        $totalSkomda = SkomdaStudent::count();

        $pendingVerifications = Freelancer::with('skomda_student')
            ->where('status', 'Pending')
            ->latest()
            ->take(3)
            ->get();

        $totalPendingCount = Freelancer::where('status', 'Pending')->count();

        // Count "disputes" - for now we use 'Revision' as a proxy if no dispute table exists
        $disputedOrders = Order::with(['client', 'service.freelancer'])
            ->where('status', 'Revision')
            ->latest()
            ->get();
        $openDisputes = $disputedOrders->count();

        // Advanced Stats
        $totalTurnover = Transaction::where('status', 'Paid')->sum('amount');
        $totalRevenue = $totalTurnover * 0.10; // 10% platform fee as requested
        $recentTransactions = Transaction::with(['order.client'])->latest()->take(5)->get();

        $todayTurnover = Transaction::where('status', 'Paid')
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');
        $todayOrders = Order::whereDate('created_at', now()->toDateString())->count();
        $todayRevenue = $todayTurnover * 0.10;

        try {
            $selectedMonth = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable $exception) {
            $selectedMonth = now()->startOfMonth();
        }

        // Monthly Revenue Chart Data (10% Platform Fee)
        $monthlyTurnover = collect(range(5, 0))->map(function ($offset) use ($selectedMonth) {
            $monthStart = (clone $selectedMonth)->subMonthsNoOverflow($offset)->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();
            $turnover = Transaction::where('status', 'Paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            return (object) [
                'month' => (int) $monthStart->month,
                'year' => (int) $monthStart->year,
                'month_key' => $monthStart->format('Y-m'),
                'label' => $monthStart->translatedFormat('F Y'),
                'total' => (float) $turnover * 0.10,
            ];
        });

        // Weekly Revenue Chart Data inside the selected month
        $weekStart = (clone $selectedMonth)->startOfMonth();
        $monthEnd = (clone $selectedMonth)->endOfMonth();
        $weeklyTurnover = collect();

        while ($weekStart->lte($monthEnd)) {
            $periodStart = (clone $weekStart);
            $periodEnd = (clone $weekStart)->addDays(6);

            if ($periodEnd->gt($monthEnd)) {
                $periodEnd = (clone $monthEnd);
            }

            $turnover = Transaction::where('status', 'Paid')
                ->whereBetween('created_at', [$periodStart->copy()->startOfDay(), $periodEnd->copy()->endOfDay()])
                ->sum('amount');

            $weeklyTurnover->push((object) [
                'period_start' => $periodStart->translatedFormat('j M'),
                'period_end' => $periodEnd->translatedFormat('j M'),
                'week_label' => $periodStart->translatedFormat('j M') . ' - ' . $periodEnd->translatedFormat('j M'),
                'total' => (float) $turnover * 0.10,
            ]);

            $weekStart = $periodEnd->copy()->addDay()->startOfDay();
        }

        return view('dashboard.admin.dashboard', compact(
            'totalUsers',
            'totalClients',
            'totalFreelancers',
            'totalSkomda',
            'pendingVerifications',
            'totalPendingCount',
            'openDisputes',
            'disputedOrders',
            'totalRevenue',
            'totalTurnover',
            'recentTransactions',
            'todayOrders',
            'todayRevenue',
            'monthlyTurnover',
            'weeklyTurnover'
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
            ->whereIn('status', ['Pending', 'Negotiated', 'In Progress', 'Revision'])
            ->count();

        $totalSpent = $allOrders
            ->whereIn('status', ['Paid', 'Completed'])
            ->sum('agreed_price');

        $completedProjects = $allOrders
            ->where('status', 'Completed')
            ->count();

        $projects = Order::with('service')
            ->where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $projectsData = $projects->map(function ($o) {
            return [
                'id' => $o->id,
                'brief' => $o->brief,
                'status' => $o->status,
                'deadline' => $o->deadline,
                'agreed_price' => $o->agreed_price,
                'service_id' => $o->service_id,
                'service' => [
                    'title' => ($o->service->title ?? $o->service->name ?? 'Service'),
                ],
                'href' => route('client.orders.show', $o->id),
            ];
        });

        $statsData = [
            'total' => $allOrders->count(),
            'active' => $activeProjects,
            'completed' => $completedProjects,
            'totalSpent' => $totalSpent,
        ];

        $showWelcomePopup = $user->created_at && $user->created_at->isToday() && $allOrders->isEmpty();

        return view('dashboard.client.dashboard', compact(
            'user',
            'projects',
            'projectsData',
            'statsData',
            'activeProjects',
            'totalSpent',
            'completedProjects',
            'showWelcomePopup'
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
                'deadline' => $order->deadline,
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

        $ordersWithStatusChange = $orders->whereIn('status', ['Revision', 'Completed'])->take(3)->map(function ($order) {
            return [
                'id' => $order->id,
                'title' => $order->service->title ?? 'Service',
                'client_name' => $order->client->name ?? 'Client',
                'status' => $order->status,
                'agreed_price' => $order->agreed_price,
                'created_at' => $order->created_at,
                'deadline' => $order->deadline,
            ];
        })->values();

        $dashboardData = [
            'stats' => [
                'activeOrders' => $activeOrders,
                'services' => $servicesCount,
                'avgRating' => $avgRating ? number_format((float) $avgRating, 1) : '0.0',
                'balance' => (float) $balance,
            ],
            'latestOrders' => $latestOrders,
            'jobOpportunities' => $jobOpportunities,
            'ordersWithStatusChange' => $ordersWithStatusChange,
        ];

        $showWelcomePopup = $freelancer->career_track_status === 'None' && $freelancer->status !== 'Approved';

        return view('dashboard.freelancer.dashboard', compact('dashboardData', 'showWelcomePopup'));
    }

    public function verifyFreelancer($id)
    {
        $freelancer = Freelancer::with('skomda_student')->findOrFail($id);
        
        $updateData = ['status' => 'Approved', 'reject_reason' => null];
        if ($freelancer->career_track_status === 'Pending') {
            $updateData['career_track_status'] = 'Approved';
            $updateData['career_track_notes'] = null;
        }
        $freelancer->update($updateData);

        $exists = \App\Models\Notification::where('role', 'freelancer')
            ->where('user_id', $freelancer->id)
            ->where('title', 'Akun & Jalur Karir Diverifikasi')
            ->where('is_read', false)
            ->exists();

        if (!$exists) {
            \App\Models\Notification::create([
                'title' => 'Akun & Jalur Karir Diverifikasi',
                'message' => 'Selamat, akun freelancer dan pengajuan jalur karir kamu (' . ($freelancer->career_track ?? 'Spesialis') . ') telah disetujui oleh admin!',
                'type' => 'success',
                'role' => 'freelancer',
                'user_id' => $freelancer->id,
                'link' => '/freelancer/profile',
            ]);
        }

        return response()->json(['message' => 'Success']);
    }

    public function rejectFreelancer($id, \Illuminate\Http\Request $request)
    {
        $freelancer = Freelancer::with('skomda_student')->findOrFail($id);

        $reason = $request->input('reason', 'Tidak ada alasan spesifik');

        $updateData = [
            'status' => 'Rejected',
            'reject_reason' => $reason,
        ];
        if ($freelancer->career_track_status === 'Pending') {
            $updateData['career_track_status'] = 'Rejected';
            $updateData['career_track_notes'] = $reason;
        }
        $freelancer->update($updateData);

        $exists = \App\Models\Notification::where('role', 'freelancer')
            ->where('user_id', $freelancer->id)
            ->where('title', 'Verifikasi Ditolak')
            ->where('is_read', false)
            ->exists();

        if (!$exists) {
            \App\Models\Notification::create([
                'title' => 'Verifikasi Ditolak',
                'message' => 'Maaf, pengajuan akun freelancer & jalur karir kamu belum dapat kami setujui. Alasan: ' . $reason,
                'type' => 'danger',
                'role' => 'freelancer',
                'user_id' => $freelancer->id,
                'link' => '/freelancer/profile',
            ]);
        }

        return response()->json(['message' => 'Success']);
    }

    public function settings()
    {
        if (auth('administrator')->check()) {
            return view('dashboard.admin.settings');
        }
        if (auth('client')->check()) {
            return view('dashboard.client.settings');
        }
        if (auth('freelancer')->check()) {
            return view('dashboard.freelancer.settings');
        }
        abort(403);
    }

    public function search(Request $request)
    {
        $q = $request->query('q');

        $results = collect();

        if ($q) {
            $fuzzy = '%' . str_replace(' ', '%', $q) . '%';

            // Search Menus
            $menus = collect([
                ['name' => 'Dashboard', 'url' => route('admin.dashboard'), 'desc' => 'Ringkasan statistik dan aktivitas terbaru'],
                ['name' => 'Users & Clients', 'url' => route('admin.user'), 'desc' => 'Kelola data pengguna, klien, dan siswa'],
                ['name' => 'Freelancers', 'url' => route('admin.freelancers.index'), 'desc' => 'Daftar talent dan verifikasi data'],
                ['name' => 'Orders Management', 'url' => route('admin.orders.index'), 'desc' => 'Kelola pesanan dan status proyek'],
                ['name' => 'Services Catalog', 'url' => route('admin.services.index'), 'desc' => 'Manajemen layanan dan kategori'],
                ['name' => 'Loker Management', 'url' => route('admin.loker.index'), 'desc' => 'Pantau lowongan kerja dan lamaran freelancer'],
                ['name' => 'Offers', 'url' => route('admin.offers.index'), 'desc' => 'Penawaran masuk dan request custom'],
                ['name' => 'Transactions', 'url' => route('admin.transactions.index'), 'desc' => 'Riwayat pembayaran dan keuangan'],
                ['name' => 'Reviews', 'url' => route('admin.reviews.index'), 'desc' => 'Ulasan klien terhadap freelancer'],
                ['name' => 'Settings', 'url' => route('admin.settings'), 'desc' => 'Pengaturan dashboard dan panduan'],
                ['name' => 'Profile', 'url' => route('admin.profile'), 'desc' => 'Pengaturan akun administrator'],
            ])->filter(function ($menu) use ($q) {
                return stripos($menu['name'], $q) !== false || stripos($menu['desc'], $q) !== false;
            })->map(function ($item) {
                return (object) [
                    'title' => $item['name'],
                    'name' => $item['name'],
                    'email' => $item['desc'],
                    'search_type' => 'Menu',
                    'search_url' => $item['url'],
                    'created_at' => now(),
                ];
            });

            // Search Clients
            $clients = Client::where('name', 'like', $fuzzy)
                ->orWhere('email', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Client';
                    $item->search_url = route('admin.clients.index') . '?q=' . urlencode($item->name);

                    return $item;
                });

            // Search Freelancers
            $freelancers = Freelancer::whereHas('skomda_student', function ($query) use ($fuzzy) {
                $query->where('name', 'like', $fuzzy)
                    ->orWhere('email', 'like', $fuzzy)
                    ->orWhere('major', 'like', $fuzzy)
                    ->orWhere('nis', 'like', $fuzzy);
            })
                ->get()->map(function ($item) {
                    $item->search_type = 'Freelancer';
                    $item->search_url = route('admin.freelancers.index') . '?q=' . urlencode($item->name);

                    return $item;
                });

            // Search Services
            $services = Service::where('title', 'like', $fuzzy)
                ->orWhere('description', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('admin.services.index') . '?q=' . urlencode($item->title);

                    return $item;
                });

            // Search Orders
            $orders = Order::where('id', 'like', $fuzzy)
                ->orWhere('brief', 'like', $fuzzy)
                ->orWhereHas('client', function ($query) use ($fuzzy) {
                    $query->where('name', 'like', $fuzzy);
                })
                ->orWhereHas('service', function ($query) use ($fuzzy) {
                    $query->where('title', 'like', $fuzzy);
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('admin.orders.index');

                    return $item;
                });

            // Search Lokers
            $lokers = Loker::where('title', 'like', $fuzzy)
                ->orWhere('description', 'like', $fuzzy)
                ->orWhereHas('client', function ($query) use ($fuzzy) {
                    $query->where('name', 'like', $fuzzy);
                })
                ->orWhereHas('category', function ($query) use ($fuzzy) {
                    $query->where('name', 'like', $fuzzy);
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Loker';
                    $item->search_url = route('admin.loker.index') . '?q=' . urlencode($item->title);

                    return $item;
                });

            // Search Transactions
            $transactions = Transaction::where('id', 'like', $fuzzy)
                ->orWhere('order_id', 'like', $fuzzy)
                ->orWhere('status', 'like', $fuzzy)
                ->orWhere('type', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->title = 'Transaksi #' . $item->id;
                    $item->search_type = 'Transaction';
                    $item->search_url = route('admin.transactions.index');

                    return $item;
                });

            // Search Skomda Students
            $skomdaStudents = SkomdaStudent::where('name', 'like', $fuzzy)
                ->orWhere('email', 'like', $fuzzy)
                ->orWhere('nis', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Student';
                    $item->search_url = route('admin.skomda-students.index') . '?q=' . urlencode($item->name);

                    return $item;
                });

            // Search Service Categories
            $categories = \App\Models\ServiceCategory::where('name', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Category';
                    $item->search_url = route('admin.service-categories.index');

                    return $item;
                });

            // Search Portofolios
            $portofolios = \App\Models\Portofolio::where('title', 'like', $fuzzy)
                ->orWhere('description', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Portofolio';
                    $item->search_url = route('admin.portofolios.index');

                    return $item;
                });

            // Search Offers
            $offers = Offer::where('id', 'like', $fuzzy)
                ->orWhere('title', 'like', $fuzzy)
                ->orWhere('description', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Offer';
                    $item->search_url = route('admin.offers.index');

                    return $item;
                });

            // Search Results
            $results_data = \App\Models\Result::where('note', 'like', $fuzzy)
                ->orWhere('version', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->title = 'Result ' . ($item->version ?? $item->id);
                    $item->search_type = 'Result';
                    $item->search_url = route('admin.results.index');

                    return $item;
                });

            // Search Reviews
            $reviews = \App\Models\Review::where('comment', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->title = 'Review for Order #' . $item->order_id;
                    $item->search_type = 'Review';
                    $item->search_url = route('admin.reviews.index');

                    return $item;
                });

            // Search Negotiations (Chat messages)
            $negotiations = \App\Models\Negotiation::where('message', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->title = 'Chat in Order #' . $item->order_id;
                    $item->search_type = 'Chat';
                    $item->search_url = route('admin.negotiations.index');

                    return $item;
                });

            $results = $results->concat($menus)
                ->concat($clients)
                ->concat($freelancers)
                ->concat($skomdaStudents)
                ->concat($services)
                ->concat($categories)
                ->concat($orders)
                ->concat($lokers)
                ->concat($transactions)
                ->concat($portofolios)
                ->concat($offers)
                ->concat($results_data)
                ->concat($reviews)
                ->concat($negotiations);
        }

        return view('dashboard.admin.search', compact('results', 'q'));
    }

    public function clientSearch(Request $request)
    {
        $q = $request->query('q');
        $user = auth()->guard('client')->user();
        $results = collect();

        if ($q && $user) {
            $fuzzy = '%' . str_replace(' ', '%', $q) . '%';

            // Search Talents (Freelancers)
            $freelancers = Freelancer::where('status', 'Approved')
                ->whereHas('skomda_student', function ($query) use ($fuzzy) {
                    $query->where('name', 'like', $fuzzy)
                        ->orWhere('major', 'like', $fuzzy);
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Talent';
                    $item->search_url = route('client.talents.show', $item->id);

                    return $item;
                });

            // Search Services
            $services = Service::where('title', 'like', $fuzzy)
                ->orWhere('description', 'like', $fuzzy)
                ->get()->map(function ($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('client.services.show', $item->id);

                    return $item;
                });

            // Search Orders (Own)
            $orders = Order::where('client_id', $user->id)
                ->where(function ($query) use ($fuzzy) {
                    $query->where('brief', 'like', $fuzzy)
                        ->orWhereHas('service', function ($q2) use ($fuzzy) {
                            $q2->where('title', 'like', $fuzzy);
                        });
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('client.orders.show', $item->id);

                    return $item;
                });

            $results = $results->concat($freelancers)->concat($services)->concat($orders);
        }

        return view('dashboard.admin.search', compact('results', 'q')); // Reuse the same search view for now
    }

    public function getDisputeDetail($id)
    {
        $order = Order::with([
            'client',
            'service.freelancer',
            'negotiations' => function ($q) {
                $q->latest();
            },
            'results' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        return response()->json([
            'order' => $order,
            'client' => $order->client,
            'freelancer' => $order->service->freelancer,
            'negotiations' => $order->negotiations,
            'results' => $order->results,
        ]);
    }

    public function getFreelancerDetail($id)
    {
        $freelancer = Freelancer::with('skomda_student')
            ->withCount(['services', 'portofolios'])
            ->findOrFail($id);

        return response()->json($freelancer);
    }

    public function freelancerSearch(Request $request)
    {
        $q = $request->query('q');
        $user = auth()->guard('freelancer')->user();
        $results = collect();

        if ($q && $user) {
            $fuzzy = '%' . str_replace(' ', '%', $q) . '%';

            // Search Own Services
            $services = Service::where('freelancer_id', $user->id)
                ->where(function ($query) use ($fuzzy) {
                    $query->where('title', 'like', $fuzzy)
                        ->orWhere('description', 'like', $fuzzy);
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Service';
                    $item->search_url = route('freelancer.services.show', $item->id);

                    return $item;
                });

            // Search Orders (Received)
            $orders = Order::whereHas('service', function ($query) use ($user) {
                $query->where('freelancer_id', $user->id);
            })
                ->where(function ($query) use ($fuzzy) {
                    $query->where('brief', 'like', $fuzzy)
                        ->orWhereHas('client', function ($q2) use ($fuzzy) {
                            $q2->where('name', 'like', $fuzzy);
                        })
                        ->orWhereHas('service', function ($q2) use ($fuzzy) {
                            $q2->where('title', 'like', $fuzzy);
                        });
                })
                ->get()->map(function ($item) {
                    $item->search_type = 'Order';
                    $item->search_url = route('freelancer.orders.show', $item->id);

                    return $item;
                });

            $results = $results->concat($services)->concat($orders);
        }

        return view('dashboard.admin.search', compact('results', 'q')); // Reuse the same search view
    }

    /**
     * PERBAIKAN TASK 6: Aksi eksekusi Admin untuk menyelesaikan dispute secara tuntas
     */
    public function resolveDispute(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status_decision' => 'required|in:Completed,Cancelled',
        ]);

        $decision = $request->input('status_decision');

        // Update status order berdasarkan keputusan peninjauan admin
        $order->update([
            'status' => $decision
        ]);

        $textStatus = $decision === 'Completed' ? 'Dinyatakan Selesai (Dana diteruskan ke Freelancer)' : 'Dibatalkan (Dana dikembalikan ke Klien)';
        $notifType = $decision === 'Completed' ? 'success' : 'danger';

        // Kirim Notifikasi otomatis ke Client
        \App\Models\Notification::create([
            'title' => 'Keputusan Dispute Admin',
            'message' => "Admin telah meninjau dan menyelesaikan dispute untuk pesanan #{$order->id}. Keputusan: Pesanan {$textStatus}.",
            'type' => $notifType,
            'role' => 'client',
            'user_id' => $order->client_id,
            'link' => route('client.orders.show', $order->id),
        ]);

        // Kirim Notifikasi otomatis ke Freelancer
        \App\Models\Notification::create([
            'title' => 'Keputusan Dispute Admin',
            'message' => "Admin telah meninjau dan menyelesaikan dispute untuk proyek #{$order->id}. Keputusan: Pesanan {$textStatus}.",
            'type' => $notifType,
            'role' => 'freelancer',
            'user_id' => $order->freelancer_id,
            'link' => route('freelancer.orders.show', $order->id),
        ]);

        // Kembali ke halaman sebelumnya membawa session flash success (bukan alert JS)
        return redirect()->back()->with('success', "Dispute berhasil diselesaikan. Status pesanan kini menjadi {$decision}.");
    }
}
