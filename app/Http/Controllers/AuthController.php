<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registrasi
    public function register(Request $request)
    {
        try {
            // Validasi input registrasi
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'tanggal_lahir' => 'nullable|date',
                'referal_id' => 'nullable|exists:users,id', // Validasi referral
                'nomor_hp' => 'nullable|string|max:15', // Validasi nomor HP
                'alamat' => 'nullable|string|max:255',   // Validasi alamat
            ]);

            // Membuat user baru
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
                'referal_id' => $validatedData['referal_id'] ?? null,
                'nomor_hp' => $validatedData['nomor_hp'] ?? null,
                'alamat' => $validatedData['alamat'] ?? null,
            ]);

            // Mengirim email verifikasi
            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully. Please verify your email.',
                'data' => $user,
            ], 201);

        } catch (ValidationException $e) {
            // Response error validasi
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Response error
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // Login
    public function login(Request $request)
    {
        try {
            // Validasi input login
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Attempt login
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Cek apakah email sudah diverifikasi
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email first.',
                ], 403);
            }

            // Generate token for login
            $token = $user->createToken('authToken')->plainTextToken;

            // Return success response with user data and token
            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token, // Token login
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'nomor_hp' => $user->nomor_hp,
                    'alamat' => $user->alamat,
                ], // Balikkan data user yang login
            ]);

        } catch (ValidationException $e) {
            // Response error validasi
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Response error
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
