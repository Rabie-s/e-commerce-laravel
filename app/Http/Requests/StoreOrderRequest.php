<?php

namespace App\Http\Requests;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
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
        return [
            'customer_info' => ['required', 'array'],
            'customer_info.phone_number' => ['required', 'string', 'max:20'],
            'customer_info.first_name' => ['required', 'string', 'max:255'],
            'customer_info.last_name' => ['required', 'string', 'max:255'],
            'customer_info.city' => ['required', 'string', 'max:255'],
            'customer_info.address' => ['required', 'string', 'max:500'],
            'customer_info.nearby_landmark' => ['nullable', 'string', 'max:255'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_info.required' => 'Customer information is required.',
            'customer_info.phone_number.required' => 'Phone number is required.',
            'customer_info.first_name.required' => 'First name is required.',
            'customer_info.last_name.required' => 'Last name is required.',
            'customer_info.city.required' => 'City is required.',
            'customer_info.address.required' => 'Address is required.',

            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.variant_id.required' => 'Product variant ID is required for each item.',
            'items.*.variant_id.exists' => 'The selected product variant does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $variant = ProductVariant::find($item['variant_id']);

                if (!$variant) {
                    continue;
                }

                $availableStock = $variant->stock;
                $requestedQuantity = $item['quantity'];

                if ($requestedQuantity > $availableStock) {
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "Not enough stock for variant SKU: {$variant->sku}. Available: {$availableStock}, Requested: {$requestedQuantity}"
                    );
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
