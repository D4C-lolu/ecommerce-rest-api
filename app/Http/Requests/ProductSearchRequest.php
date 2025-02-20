<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ProductSearchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|in:name,price,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:min_price',
            'status' => 'nullable|in:active,inactive'
        ];
    }
}