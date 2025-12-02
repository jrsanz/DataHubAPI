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
     */
    public function me()
    {
        // Devuelve los detalles del usuario autenticado
        return new UserResource(auth('api')->user());
    }
}
