<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => 'required|string|max:255',               // 商品名 必須
            'description' => 'required|string|max:255',         // 商品説明 必須 最大255文字
            'image' => 'required|mimes:jpeg,png',               // 商品画像 必須 jpeg または png
            'category_id' => 'required|array|min:1',            // カテゴリー 必須 配列で1つ以上
            'condition' => 'required|string',                   // 商品の状態 必須
            'price' => 'required|numeric|min:1',                // 商品価格 必須 数値型、1円以上
            'brand' => 'nullable|exists:brands,id',              // ブランド 任意 ブランドが存在するID
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '商品名は必須です',
            'description.required' => '商品説明は必須です',
            'description.max' => '商品説明は255文字以内で入力してください',
            'image.required' => '商品画像は必須です',
            'image.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'category_id.required' => '商品カテゴリーは必須です',
            'category_id.array' => 'カテゴリーは配列で選択してください',
            'category_id.min' => '少なくとも1つのカテゴリーを選択してください',
            'condition.required' => '商品の状態は必須です',
            'price.required' => '商品価格は必須です',
            'price.numeric' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は1円以上で入力してください',
            'brand.exists' => '指定されたブランドは存在しません',
        ];
    }
}
