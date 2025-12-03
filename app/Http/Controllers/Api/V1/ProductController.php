<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     * @return \Illuminate\Http\JsonResponse Respuesta con la lista de productos.
     * 
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Obtiene la lista de productos",
     *     tags={"Productos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos",
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
    public function index()
    {
        // Obtiene todos los productos
        $productos = Product::all();
        
        // Devuelve la lista de productos
        return new ProductCollection($productos);
    }

    /**
     * Muestra un producto específico.
     * @param  \App\Models\Product  $product Producto a mostrar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto.
     * 
     * @OA\Get(
        *     path="/api/v1/products/{id}",
        *     summary="Obtiene un producto",
        *     tags={"Productos"},
        *     security={{"bearerAuth": {}}},
        *     @OA\Parameter(
        *         name="id",
        *         in="path",
        *         description="ID del producto",
        *         required=true,
        *         @OA\Schema(
        *             type="integer",
        *             example=1,
        *         )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Producto obtenido",
        *         @OA\JsonContent(
        *             @OA\Property(property="id", type="integer", example=1),
        *             @OA\Property(property="name", type="string", example="Producto A"),
        *             @OA\Property(property="price", type="number", example=99.99),
        *             @OA\Property(property="stock", type="integer", example=5),
        *             @OA\Property(property="description", type="string", example="Descripción del Producto A"),
        *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
        *         )
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Token de acceso inválido o no proporcionado",
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Producto no encontrado",
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Error interno del servidor",
        *     ),
        * )
     */
    public function show(Product $product)
    {
        // Devuelve el producto
        return new ProductResource($product);
    }

    /**
     * Busca productos por nombre o precio.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos de búsqueda.
     * @return \Illuminate\Http\JsonResponse Respuesta con los productos encontrados.
     * 
     * @OA\Get(
     *     path="/api/v1/products/search",
     *     summary="Busca productos por nombre o precio",
     *     tags={"Productos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         description="Dato de búsqueda",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Producto A",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Productos encontrados",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El parámetro data es obligatorio para la búsqueda",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token de acceso inválido o no proporcionado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *     ),
     * )
     */
    public function search(Request $request)
    {
        // Obtiene los datos de búsqueda
        $data = $request->input('data');

        // Si no se proporcionan datos de búsqueda, devuelve un error
        if (empty($data)) {
            return response()->json(['message' => 'El parámetro data es obligatorio para la búsqueda'], 400);
        }

        // Busca productos por nombre o precio
        $products = Product::where('name', 'LIKE', "%$data%")->orWhere('price', 'LIKE', "%$data%")->get();

        // Devuelve los productos encontrados
        return new ProductCollection($products);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto almacenado.
     * 
     * @OA\Post(
        *     path="/api/v1/products",
        *     summary="Crea un nuevo producto",
        *     tags={"Productos"},
        *     security={{"bearerAuth": {}}},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"name","price","description"},
        *             @OA\Property(property="name", type="string", example="Producto A"),
        *             @OA\Property(property="price", type="number", format="float", example=99.99),
        *             @OA\Property(property="description", type="string", example="Descripción del Producto A"),
        *             @OA\Property(property="stock", type="integer", example=5),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=201,
        *         description="Producto creado",
        *         @OA\JsonContent(
        *             @OA\Property(property="id", type="integer", example=1),
        *             @OA\Property(property="name", type="string", example="Producto A"),
        *             @OA\Property(property="price", type="number", format="float", example=99.99),
        *             @OA\Property(property="description", type="string", example="Descripción del Producto A"),
        *             @OA\Property(property="stock", type="integer", example=5),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Token de acceso inválido o no proporcionado",
        *     ),
        *     @OA\Response(
        *         response=403,
        *         description="Acceso denegado",
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
    public function store(ProductRequest $request)
    {
        // Crea un nuevo producto
        $product = Product::create($request->validated());

        // Devuelve el producto creado
        return new ProductResource($product);
    }

    /**
     * Actualiza un producto existente en la base de datos.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos actualizados del producto.
     * @param  \App\Models\Product  $product Producto a actualizar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto actualizado.
     * 
     * @OA\Put(
        *     path="/api/v1/products/{id}",
        *     summary="Actualiza un producto",
        *     tags={"Productos"},
        *     security={{"bearerAuth": {}}},
        *     @OA\Parameter(
        *         name="id",
        *         in="path",
        *         description="ID del producto",
        *         required=true,
        *         @OA\Schema(
        *             type="integer",
        *             example=1,
        *         )
        *     ),
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"name","price","description"},
        *             @OA\Property(property="name", type="string", example="Producto Actualizado"),
        *             @OA\Property(property="price", type="number", format="float", example=149.99),
        *             @OA\Property(property="description", type="string", example="Descripción actualizada del producto"),
        *             @OA\Property(property="stock", type="integer", example=10),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Producto actualizado",
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Token de acceso inválido o no proporcionado",
        *     ),
        *     @OA\Response(
        *         response=403,
        *         description="Acceso denegado",
        *     ),
        *     @OA\Response(
        *         response=422,
        *         description="Datos inválidos",
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Producto no encontrado",
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Error interno del servidor",
        *     ),
        * )
     */
    public function update(ProductRequest $request, Product $product)
    {
        // Actualiza el producto seleccionado
        $product->update($request->validated());

        // Devuelve el producto actualizado
        return new ProductResource($product);
    }

    /**
     * Elimina un producto de la base de datos.
     * @param  \App\Models\Product  $product Producto a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto eliminado.
     * 
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Elimina un producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token de acceso inválido o no proporcionado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *     ),
     * )
     */
    public function destroy(Product $product)
    {
        // Elimina el producto seleccionado
        $product->delete();

        // Devuelve una respuesta de éxito
        return response()->json(['message' => 'Producto eliminado correctamente.'], 200);
    }
}
