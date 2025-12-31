<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'countryRegion' => ['required', 'string', 'max:100'],
            'streetAddress' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'zipCode' => ['required', 'string', 'max:20'],
            'additionalInfo' => ['nullable', 'string', 'max:1000'],
            'paymentMethod' => ['required', 'string', 'in:bank,card,cash_on_delivery'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter your phone number.',
            'countryRegion.required' => 'Please select your country.',
            'streetAddress.required' => 'Please enter your street address.',
            'city.required' => 'Please enter your city.',
            'zipCode.required' => 'Please enter your ZIP/postal code.',
            'paymentMethod.required' => 'Please select a payment method.',
            'paymentMethod.in' => 'Please select a valid payment method.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'countryRegion' => 'country',
            'streetAddress' => 'street address',
            'zipCode' => 'ZIP code',
            'additionalInfo' => 'additional information',
            'paymentMethod' => 'payment method',
        ];
    }
}
