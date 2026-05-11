@extends('layouts.dashboard')
@section('title', 'Create Service | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <x-form-layout title="Buat Service Baru" backUrl="{{ route('freelancer.services.index') }}"
            backLabel="Kembali ke Daftar Service">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-bold mb-2 flex items-center gap-2">
                        <i class="ri-error-warning-line"></i>
                        Periksa kembali input berikut:
                    </p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('freelancer.services.store') }}" method="POST">
                @csrf
                @include('dashboard.freelancer.services._form-fields', ['categories' => $categories])

                <x-form-actions submitLabel="Simpan Service" cancelUrl="{{ route('freelancer.services.index') }}" />
            </form>
        </x-form-layout>
    </div>
@endsection