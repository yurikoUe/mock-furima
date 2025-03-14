<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // ハイフン付きの8文字
            'address' => ['required', 'string'],
            'building' => ['required', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png'], // JPEGまたはPNGのみ
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザ名は必須です',
            'postal_code.required' => '郵便番号は必須です',
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください',
            'address.required' => '住所は必須です',
            'building.required' => '建物名は必須です',
            'profile_image.image' => 'プロフィール画像は画像ファイルである必要があります',
            'profile_image.mimes' => 'プロフィール画像はJPEGまたはPNG形式でアップロードしてください',
        ];
    }
}
