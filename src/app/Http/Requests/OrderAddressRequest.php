<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderAddressRequest extends FormRequest
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
            'order_postal_code' => ['required', 'string'], // 入力必須
            'order_address' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // ハイフンありの8文字 (例: 123-4567)
            'order_building' => ['required', 'string'], // 入力必須
        ];
    }

    public function messages(): array
    {
        return [
            'order_postal_code.required' => '郵便番号を入力してください。',
            'order_address.required' => '住所を入力してください。',
            'order_address.regex' => '住所は「123-4567」の形式で入力してください。',
            'order_building.required' => '建物名を入力してください。',
        ];
    }
}
