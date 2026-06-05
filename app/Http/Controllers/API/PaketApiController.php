<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaketResource;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaketApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/paket
     */
    public function index()
    {
        $paket = Paket::all();
        return PaketResource::collection($paket);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/paket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paket' => 'required|string|max:20',
            'tarif' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate ID Paket
        $lastPaket = Paket::orderBy('id_paket', 'desc')->first();
        if ($lastPaket) {
            $lastNumber = (int) substr($lastPaket->id_paket, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $id_paket = 'P' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $paket = Paket::create([
            'id_paket' => $id_paket,
            'paket' => $request->paket,
            'tarif' => $request->tarif,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil ditambahkan',
            'data' => new PaketResource($paket)
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/paket/{id}
     */
    public function show(string $id)
    {
        $paket = Paket::find($id);

        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PaketResource($paket)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/paket/{id}
     */
    public function update(Request $request, string $id)
    {
        $paket = Paket::find($id);

        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'paket' => 'sometimes|required|string|max:20',
            'tarif' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $paket->update($request->only(['paket', 'tarif']));

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil diupdate',
            'data' => new PaketResource($paket)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/paket/{id}
     */
    public function destroy(string $id)
    {
        $paket = Paket::find($id);

        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        // Check if paket has pelanggan
        if ($paket->pelanggan()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak bisa dihapus karena masih digunakan oleh pelanggan'
            ], 400);
        }

        $paket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil dihapus'
        ]);
    }
}
