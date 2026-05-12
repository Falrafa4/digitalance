<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Models\Client;

class ClientControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Client::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data client berhasil diambil',
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $client = Client::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Client berhasil ditambahkan',
            'data' => $client,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data client berhasil diambil',
            'data' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Akun client berhasil diperbarui',
            'data' => $client,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Akun client berhasil dihapus',
        ]);
    }
}
