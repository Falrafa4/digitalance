@extends('layouts.dashboard')
@section('title', 'Edit Service | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <x-form-layout title="Edit Service" backUrl="{{ route('freelancer.services.show', $service->id) }}"
            backLabel="Kembali ke Detail">

            @if($service->reject_reason)
                <div class="mb-8 p-5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <i class="ri-error-warning-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-amber-900 font-bold text-sm mb-1">Catatan Admin (Perlu Diperbaiki)</h4>
                        <p class="text-amber-800 text-[13px] leading-relaxed">
                            {{ $service->reject_reason }}
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route('freelancer.services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('dashboard.freelancer.services._form-fields', [
                    'categories' => $categories ?? collect(),
                    'service' => $service,
                    'categoryLocked' => true,
                ])

                <x-form-actions submitLabel="Simpan Perubahan"
                        cancelUrl="{{ route('freelancer.services.show', $service->id) }}" />
                </form>
            </x-form-layout>
        </div>
@endsection