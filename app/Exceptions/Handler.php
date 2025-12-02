<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Manejo de errores para rutas API
        if ($request->is('api/*')) {
            // ID no encontrado en la base de datos
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => 'Recurso no encontrado.',
                    'status' => 404,
                ], 404);
            }

            // Ruta no existente
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => 'La ruta ' . $request->path() . ' no existe.',
                    'status' => 404,
                ], 404);
            }

            // Método HTTPi no permitido
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'error' => 'Método no permitido para la ruta ' . $request->path() . '.',
                    'status' => 405,
                ], 405);
            }

            // Error de validación
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Datos inválidos.',
                    'error' => $exception->errors(),
                    'status' => 422,
                ], 422);
            }

            // Respuesta por defecto para otros errores
            return response()->json([
                'error' => 'Error interno del servidor.',
                'status' => 500,
            ], 500);
        }

        // Llama al método render de la clase padre
        return parent::render($request, $exception);
    }
}
