<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Registra un nuevo usuario.
     * @param  \App\Http\Requests\UserRequest  $request Solicitud con los datos del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta con el usuario creado.
     * 
     * @OA\Post(
        *     path="/api/v1/users/register",
        *     summary="Registra un nuevo usuario",
        *     tags={"Usuarios"},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"name", "email", "password", "role"},
        *             @OA\Property(property="name", type="string", example="Juan Pérez"),
        *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
        *             @OA\Property(property="password", type="string", example="password123"),
        *             @OA\Property(property="role", type="string", example="user"),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=201,
        *         description="Usuario creado exitosamente",
        *         @OA\JsonContent(
        *             @OA\Property(property="id", type="integer", example=1),
        *             @OA\Property(property="name", type="string", example="Juan Pérez"),
        *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
        *             @OA\Property(property="role", type="string", example="user"),
        *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=422,
        *         description="Datos inválidos",
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Error interno del servidor",
        *     ),
        * )
     */
    public function register(UserRequest $request)
    {
        // Crea un nuevo usuario
        $user = User::create($request->validated());

        // Devuelve el usuario creado
        return new UserResource($user);
    }

    /**
     * Autentica a un usuario y genera un token de acceso.
     * @param  \Illuminate\Http\Request  $request Solicitud con las credenciales del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta con el token de acceso.
     * 
     * @OA\Post(
     *     path="/api/v1/users/login",
     *     summary="Autentica a un usuario y genera un token de acceso",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(property="user", type="object", example={
     *                 "id": 1,
     *                 "name": "Juan Pérez",
     *                 "email": "juan.perez@example.com",
     *                 "role": "user",
     *                 "created_at": "2025-01-01T00:00:00.000000Z",
     *                 "updated_at": "2025-01-01T00:00:00.000000Z"
     *             }),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *     ),
     * )
     */
    public function login(Request $request)
    {
        // Valida las credenciales del usuario
        $credentials = $request->only('email', 'password');

        // Intenta autenticar al usuario con las credenciales proporcionadas
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Devuelve el usuario autenticado
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('api')->user()),
        ]);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * @return \Illuminate\Http\JsonResponse Respuesta de éxito al cerrar sesión.
     * 
     * @OA\Post(
        *     path="/api/v1/users/logout",
        *     summary="Cierra la sesión del usuario autenticado",
        *     tags={"Usuarios"},
        *     security={{"bearerAuth": {}}},
        *     @OA\Response(
        *         response=200,
        *         description="Sesión cerrada exitosamente",
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Token de acceso inválido o no proporcionado",
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Error interno del servidor",
        *     ),
        * )
     */
    public function logout()
    {
        // Cierra la sesión del usuario autenticado
        auth('api')->logout();

        // Devuelve una respuesta de éxito
        return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
    }

    /**
     * Obtiene los detalles del usuario autenticado.
     * @return \Illuminate\Http\JsonResponse Respuesta con los detalles del usuario.
     * 
     * @OA\Get(
        *     path="/api/v1/users/me",
        *     summary="Obtiene los detalles del usuario autenticado",
        *     tags={"Usuarios"},
        *     security={{"bearerAuth": {}}},
        *     @OA\Response(
        *         response=200,
        *         description="Detalles del usuario obtenidos exitosamente",
        *         @OA\JsonContent(
        *             @OA\Property(property="id", type="integer", example=1),
        *             @OA\Property(property="name", type="string", example="Juan Pérez"),
        *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
        *             @OA\Property(property="role", type="string", example="user"),
        *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Token de acceso inválido o no proporcionado",
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Error interno del servidor",
        *     ),
        * )
     */
    public function me()
    {
        // Devuelve los detalles del usuario autenticado
        return new UserResource(auth('api')->user());
    }
}
