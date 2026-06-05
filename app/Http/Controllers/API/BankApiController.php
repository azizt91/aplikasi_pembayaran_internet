<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BankApiController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return BankResource::collection($banks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bank' => 'required|string|max:255',
            'pemilik_rekening' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'url_icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['nama_bank', 'pemilik_rekening', 'nomor_rekening']);

        if ($request->hasFile('url_icon')) {
            $file = $request->file('url_icon');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('icons', $filename, 'public');
            $data['url_icon'] = $path;
        }

        $bank = Bank::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank berhasil ditambahkan',
            'data' => new BankResource($bank)
        ], 201);
    }

    public function show(string $id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new BankResource($bank)
        ]);
    }

    public function update(Request $request, string $id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_bank' => 'sometimes|required|string|max:255',
            'pemilik_rekening' => 'sometimes|required|string|max:255',
            'nomor_rekening' => 'sometimes|required|string|max:255',
            'url_icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['nama_bank', 'pemilik_rekening', 'nomor_rekening']);

        if ($request->hasFile('url_icon')) {
            if ($bank->url_icon) {
                Storage::disk('public')->delete($bank->url_icon);
            }

            $file = $request->file('url_icon');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('icons', $filename, 'public');
            $data['url_icon'] = $path;
        }

        $bank->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank berhasil diupdate',
            'data' => new BankResource($bank)
        ]);
    }

    public function destroy(string $id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank tidak ditemukan'
            ], 404);
        }

        if ($bank->url_icon) {
            Storage::disk('public')->delete($bank->url_icon);
        }

        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank berhasil dihapus'
        ]);
    }
}
