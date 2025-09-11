<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:categories,categoryId',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'condition' => 'required|in:new,like_new,good,fair,poor',
        ];

        // For update requests, status field can be included
        if ($this->method() == 'PUT' || $this->method() == 'PATCH') {
            $rules['status'] = 'required|in:available,sold,unavailable';
        }

        // For create requests or if images are present in update
        if ($this->method() == 'POST' || $this->hasFile('images')) {
            $rules['images'] = 'required|array|min:1|max:5';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'categoryId.exists' => 'The selected category is invalid.',
            'images.required' => 'Please upload at least one product image.',
            'images.max' => 'You can upload a maximum of 5 images.',
            'images.*.image' => 'The file must be an image.',
            'images.*.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'images.*.max' => 'The image may not be greater than 2MB.',
        ];
    }
}