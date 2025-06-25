<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
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
     *             @OA\Property(property="email", type="string", example="esfomeado@user.com"),
     *             @OA\Property(property="password", type="string", example="tocomfome")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="6|abxZ9NxCBKDj5SGxa8oaB4OsJBbIxbJpUMh1ejmC824109e0")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais Inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());

        if (!$token) {
            return response()->json(['message' => 'Credenciais Inválidas'], 401);
        }

        return new LoginResource($token)->response();
    }
}
