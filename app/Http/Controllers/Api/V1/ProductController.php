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
     */
    public function destroy(Product $product)
    {
        // Elimina el producto seleccionado
        $product->delete();

        // Devuelve una respuesta de éxito
        return response()->json(['message' => 'Producto eliminado correctamente.'], 200);
    }
}
