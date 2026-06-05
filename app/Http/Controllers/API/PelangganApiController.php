<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PelangganResource;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PelangganApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/pelanggan
     */
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by nama, email, whatsapp
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        // Filter by paket
        if ($request->has('id_paket')) {
            $query->where('id_paket', $request->id_paket);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pelanggan = $query->paginate($perPage);

        return PelangganResource::collection($pelanggan);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/pelanggan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string',
            'whatsapp' => 'required|string|max:15',
            'email' => 'required|email|unique:pelanggan,email',
            'id_paket' => 'required|exists:paket,id_paket',
            'ip_address' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate ID Pelanggan
        $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
        if ($lastPelanggan) {
            $lastNumber = (int) substr($lastPelanggan->id_pelanggan, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $id_pelanggan = 'ID' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        // Generate random password
        $password = Str::random(8);

        $pelanggan = Pelanggan::create([
            'id_pelanggan' => $id_pelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'password' => $password, // Plain text as per existing system
            'level' => 'User',
            'id_paket' => $request->id_paket,
            'ip_address' => $request->ip_address,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan',
            'data' => new PelangganResource($pelanggan->load('paket')),
            'password' => $password // Return password untuk diberikan ke pelanggan
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/pelanggan/{id}
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::with('paket')->find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PelangganResource($pelanggan)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/pelanggan/{id}
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:50',
            'alamat' => 'sometimes|required|string',
            'whatsapp' => 'sometimes|required|string|max:15',
            'email' => 'sometimes|required|email|unique:pelanggan,email,' . $id . ',id_pelanggan',
            'id_paket' => 'sometimes|required|exists:paket,id_paket',
            'ip_address' => 'nullable|string|max:50',
            'status' => 'sometimes|required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan->update($request->only([
            'nama', 'alamat', 'whatsapp', 'email', 'id_paket', 'ip_address', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil diupdate',
            'data' => new PelangganResource($pelanggan->load('paket'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/pelanggan/{id}
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        $pelanggan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus'
        ]);
    }

    /**
     * Get tagihan pelanggan
     * GET /api/pelanggan/{id}/tagihan
     */
    public function tagihan(string $id, Request $request)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        $query = $pelanggan->tagihan();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tahun
        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $tagihan = $query->orderBy('tahun', 'desc')
                        ->orderBy('bulan', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }
}
