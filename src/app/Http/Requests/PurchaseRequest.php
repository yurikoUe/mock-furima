<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'order_address_id' => 'required|exists:order_addresses,id',
            'payment_method' => 'required|in:card,convenience_store',
        ];
    }

    public function messages()
        {
            return [
                'payment_method.required' => '支払い方法を選択してください。',
                'payment_method.in' => '選択した支払い方法は無効です。',
                'order_address_id.required' => '配送先の住所を入力してください。',
            ];
        }
}
