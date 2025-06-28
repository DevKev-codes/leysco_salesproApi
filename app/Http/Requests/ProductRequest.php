<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Add your authorization logic here
    }

    public function rules()
    {
        return [
            'sku' => 'required|unique:products,sku,'.$this->product?->id,
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }
}
