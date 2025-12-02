<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Revistar si el método es POST o PUT/PATCH para aplicar reglas específicas
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);
        $rule = $isUpdate ? 'sometimes' : 'required';

        // Valida si el request es de actualización y no hay datos
        if (empty($this->all()) && $isUpdate) {
            abort(response()->json(['message' => 'Es necesario enviar datos para actualizar el producto.'], 422));
        }

        // Reglas de validación para crear o actualizar un producto
        return [
            'name' => $rule . '|string|max:255',
            'description' => 'nullable|string',
            'price' => $rule . '|numeric|min:1',
            'stock' => $rule . '|integer|min:0',
        ];
    }
}
