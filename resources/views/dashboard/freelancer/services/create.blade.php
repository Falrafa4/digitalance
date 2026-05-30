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

                <div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-200">
                    <a href="{{ route('freelancer.services.index') }}"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>

                    <div class="flex items-center gap-3">
                        <button type="submit" name="form_action" value="draft"
                            class="inline-flex items-center gap-2 px-6 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                            <i class="ri-save-line"></i>
                            Simpan Draft
                        </button>
                        <button type="submit" name="form_action" value="submit"
                            class="inline-flex items-center gap-2 px-6 py-2 rounded-lg bg-[#0f766e] hover:bg-teal-800 text-white font-semibold transition-colors">
                            <i class="ri-send-plane-fill"></i>
                            Ajukan ke Admin
                        </button>
                    </div>
                </div>
            </form>
        </x-form-layout>
    </div>
@endsection