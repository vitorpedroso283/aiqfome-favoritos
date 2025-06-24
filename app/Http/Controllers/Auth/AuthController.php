<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Autenticação de usuário",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciais para autenticação",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             properties={
     *                 "email": {"type":"string","example":"user@test.com"},
     *                 "password": {"type":"string","example":"password123"}
     *             }
     *         )
     *     ),
     *     @OA\Response(response=200, description="Sucesso",
     *         @OA\JsonContent(properties={"token": {"type":"string"}})
     *     ),
     *     @OA\Response(response=401, description="Credenciais Inválidas",
     *         @OA\JsonContent(properties={"message": {"type":"string"}})
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        if (!$token) {
            return response()->json(['message' => 'Credenciais Inválidas'], 401);
        }

        return response()->json(['token' => $token]);
    }
}
