<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:active,inactive',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $this->route('product'),
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
