<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * 200 + token bila benar, 401 bila email/password salah, 422 bila input tidak valid.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        $token = $user->createToken('vue-spa')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => $this->dataUser($user),
        ]);
    }

    /**
     * GET /api/me - data user pemilik token.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->dataUser($request->user())]);
    }

    /**
     * POST /api/logout - mencabut token yang sedang dipakai saja,
     * token di perangkat lain tetap berlaku.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
