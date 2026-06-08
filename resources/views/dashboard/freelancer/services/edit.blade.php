@extends('layouts.dashboard')
@section('title', 'Edit Service | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <x-form-layout title="Edit Service" backUrl="{{ route('freelancer.services.show', $service->id) }}"
            backLabel="Kembali ke Detail">

            @if($service->reject_reason)
                <x-ui.alert type="warning" class="mb-8">
                    <h4 class="font-extrabold text-sm mb-1">Catatan Admin (Perlu Diperbaiki)</h4>
                    <p class="text-[13px] leading-relaxed">{{ $service->reject_reason }}</p>
                </x-ui.alert>
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