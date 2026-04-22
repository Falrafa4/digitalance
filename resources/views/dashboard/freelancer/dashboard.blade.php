@php
    use App\Models\Client;
    use App\Models\Freelancer;
    use App\Models\SkomdaStudent;

    $totalUsers = Client::count() + Freelancer::count() + SkomdaStudent::count();
    $totalClients = Client::count();
    $totalFreelancers = Freelancer::count();
    $totalSkomda = SkomdaStudent::count();
@endphp

@extends('layouts.dashboard')
@section('title', 'Admin Dashboard | Digitalance')

@section('content')
    {{-- Welcome --}}
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
        <div class="min-w-0">
            <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
                Hi, {{ Auth::user()->name }}!
                <span class="inline-block">👋</span>
            </h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">
                Here's what's happening with your work today.
            </p>
        </div>
    </section>