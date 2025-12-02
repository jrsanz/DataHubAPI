<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     * @return \Illuminate\Http\JsonResponse Respuesta con la lista de productos.
     */
    public function index()
    {
        // Devuelve la lista de productos
        return response()->json(Product::all(), 200);
    }

    /**
     * Muestra un producto específico.
     * @param  \App\Models\Product  $product Producto a mostrar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto.
     */
    public function show(Product $product)
    {
        // Devuelve el producto
        return response()->json($product, 200);
    }

    /**
     * Busca productos por nombre o precio.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos de búsqueda.
     * @return \Illuminate\Http\JsonResponse Respuesta con los productos encontrados.
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
        return response()->json($products, 200);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto almacenado.
     */
    public function store(ProductRequest $request)
    {
        // Crea un nuevo producto
        $product = Product::create($request->validated());
        return response()->json($product, 201);
    }

    /**
     * Actualiza un producto existente en la base de datos.
     * @param  \Illuminate\Http\Request  $request Solicitud con los datos actualizados del producto.
     * @param  \App\Models\Product  $product Producto a actualizar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto actualizado.
     */
    public function update(ProductRequest $request, Product $product)
    {
        // Actualiza el producto seleccionado
        $product->update($request->validated());
        return response()->json($product, 200);
    }

    /**
     * Elimina un producto de la base de datos.
     * @param  \App\Models\Product  $product Producto a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta con el producto eliminado.
     */
    public function destroy(Product $product)
    {
        // Elimina el producto seleccionado
        $product->delete();
        return response()->json(null, 204);
    }
}
