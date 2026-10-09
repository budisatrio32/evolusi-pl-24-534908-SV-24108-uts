<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * CRUD peminjaman buku dalam bentuk JSON. Seluruh endpoint dilindungi auth:sanctum.
 * Data yang tidak ada otomatis dibalas 404 lewat route model binding.
 */
class PeminjamanApiController extends Controller
{
    /**
     * GET /api/peminjaman - 200
     */
    public function index(): AnonymousResourceCollection
    {
        return PeminjamanResource::collection(Peminjaman::latest()->latest('id')->get());
    }

    /**
     * POST /api/peminjaman - 201, atau 422 bila tidak valid
     */
    public function store(PeminjamanRequest $request): JsonResponse
    {
        $peminjaman = Peminjaman::create($request->validated());

        return (new PeminjamanResource($peminjaman))
            ->additional(['message' => 'Data peminjaman berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/peminjaman/{id} - 200, atau 404
     */
    public function show(Peminjaman $peminjaman): PeminjamanResource
    {
        return new PeminjamanResource($peminjaman);
    }

    /**
     * PUT /api/peminjaman/{id} - 200, 404, atau 422
     */
    public function update(PeminjamanRequest $request, Peminjaman $peminjaman): PeminjamanResource
    {
        $peminjaman->update($request->validated());

        return (new PeminjamanResource($peminjaman))
            ->additional(['message' => 'Data peminjaman berhasil diperbarui.']);
    }

    /**
     * DELETE /api/peminjaman/{id} - 200, atau 404
     */
    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->delete();

        return response()->json(['message' => 'Data peminjaman berhasil dihapus.']);
    }
}
