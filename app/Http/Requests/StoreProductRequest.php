<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "title" =>'required',
            'sku' => ['required', 'unique:products'],
            'description' => 'string',
            'images' => ['required', 'array'],
            'images.*.file'=>['required','mimes:png,jpeg,jpg,webp'],
            'images.*.thumbnail'=>'boolean',
            'product_variants' => ['required','array'],
            'product_variants.*.variant' => 'required',
            'product_variants.*.variant_id' => 'required',
            "product_variant_prices" => ['required', 'array'],
            "product_variant_prices.*.price" => ['required', 'numeric'],
            "product_variant_prices.*.stock" => ['integer'],
        ];
    }
}
