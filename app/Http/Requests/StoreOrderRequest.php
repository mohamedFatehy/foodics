<?php

namespace App\Http\Requests;

use App\Http\Controllers\ApiControllerController;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends ApiRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'products list is required',
            'products.*.product_id.required' => 'product Id required',
            'products.*.product_id.exists' => 'valid product Id required',
            'products.*.quantity.exists' => 'valid product Quantity required',
            'products.*.quantity.required' => 'product Quantity required',
            'products.*.quantity.min' => 'product Quantity must be greater than 0',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiControllerController::FailureResponse($validator->errors()->first(), 422, $validator->errors()->toArray()));
    }
}
