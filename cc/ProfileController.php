<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // Menampilkan profil pengguna
    public function show()
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'tanggal_lahir' => $user->tanggal_lahir,
                'nomor_hp' => $user->nomor_hp,
                'alamat' => $user->alamat,
            ],
        ]);
    }

    // Memperbarui profil pengguna
    public function update(Request $request)
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nomor_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Perbarui data pengguna
        $user->nomor_hp = $request->input('nomor_hp', $user->nomor_hp);
        $user->alamat = $request->input('alamat', $user->alamat);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'tanggal_lahir' => $user->tanggal_lahir,
                'nomor_hp' => $user->nomor_hp,
                'alamat' => $user->alamat,
            ],
        ]);
    }
}
