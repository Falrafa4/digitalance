@extends('layouts.dashboard')
@section('title', 'Edit Service | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <x-form-layout title="Edit Service" backUrl="{{ route('freelancer.services.show', $service->id) }}"
            backLabel="Kembali ke Detail">

            <form action="{{ route('freelancer.services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('dashboard.freelancer.services._form-fields', ['categories' => $categories ?? collect(), 'service' => $service])

                <x-form-actions submitLabel="Simpan Perubahan"
                    cancelUrl="{{ route('freelancer.services.show', $service->id) }}" />
            </form>
        </x-form-layout>
    </div>
@endsection