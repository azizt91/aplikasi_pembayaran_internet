<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MobileProfileController extends Controller
{
    /**
     * Update profile data
     */
    public function update(Request $request)
    {
        $pelanggan = $request->user();
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pelanggan,email,' . $pelanggan->id,
            'whatsapp' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'alamat' => $request->alamat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diupdate',
            'data' => [
                'pelanggan' => [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'whatsapp' => $pelanggan->whatsapp,
                    'alamat' => $pelanggan->alamat,
                ]
            ]
        ]);
    }

    /**
     * Upload and update profile photo
     */
    /**
     * Upload and update profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $pelanggan = $request->user();
        
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete old photo if exists
            if ($pelanggan->profile_picture) {
                Storage::disk('public')->delete($pelanggan->profile_picture);
            }

            // Get uploaded file
            $file = $request->file('photo');
            $filename = 'profile_' . $pelanggan->id . '_' . time() . '.jpg';
            
            // Resize and save image (300x300)
            $image = Image::make($file)->fit(300, 300);
            
            // Use profile_pictures to match existing folder structure
            $path = 'profile_pictures/' . $filename;
            
            Storage::disk('public')->put($path, (string) $image->encode('jpg', 85));

            // Update database
            $pelanggan->update([
                'profile_picture' => $path
            ]);

            // Return URL to the serve endpoint
            $photoUrl = route('api.mobile.profile.photo', ['filename' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profile berhasil diupdate',
                'data' => [
                    'photo_url' => $photoUrl
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve profile photo directly (bypass symlink)
     */
    public function getPhoto($filename)
    {
        // Check both possible folders
        $path1 = 'profile_pictures/' . $filename;
        $path2 = 'profile_photos/' . $filename;
        
        if (Storage::disk('public')->exists($path1)) {
            $file = Storage::disk('public')->get($path1);
            $mime = Storage::disk('public')->mimeType($path1);
        } elseif (Storage::disk('public')->exists($path2)) {
            $file = Storage::disk('public')->get($path2);
            $mime = Storage::disk('public')->mimeType($path2);
        } else {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response($file, 200)->header('Content-Type', $mime);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $pelanggan = $request->user();
        
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check old password (plain text comparison)
        if ($request->old_password !== $pelanggan->password) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai'
            ], 422);
        }

        // Update password (plain text)
        $pelanggan->update([
            'password' => $request->new_password
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }
}
