<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'sku' => 'required|string|unique:products,sku',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}