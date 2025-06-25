<?php

namespace App\Http\Controllers;
/**
 * @OA\SecurityScheme(
 *     securityScheme="meu_token",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Utilize o token no formato: Bearer {token}"
 * )
 *
 * @OA\Info(
 *     title="AiQFome Favoritos API",
 *     version="1.0.0",
 *     description="API para integração com AiQFome"
 * )
 *
 * @OA\Security(
 *     securityScheme="meu_token"
 * )
 */
abstract class Controller
{
    //
}
