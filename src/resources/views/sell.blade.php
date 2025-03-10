@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <h1>商品の出品</h1>

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="sell__label">商品画像
            <div class="sell__image-upload">
                <input type="file" name="image" id="image" class="sell__input sell__input-image" accept="image/*" onchange="previewImage(event)">

                <!-- 画像選択ボタン（枠の中央に配置） -->
                <div class="sell__image-placeholder" id="image-placeholder">
                    画像を選択する
                </div>

                <!-- プレビュー表示エリア -->
                <div id="image-preview">
                    <img id="preview" src="#" alt="画像プレビュー" style="display: none; max-width: 200px; max-height: 200px;" />
                </div>
            </div>
        </label>
        @error('image')
            <div class="form__error">{{ $message }}</div>
        @enderror
        

        <h2 class="sell__section-title">商品の詳細</h2>
        <div>
            <label for="category" class="sell__label">カテゴリー</label>
            <div id="categories" class="category-buttons">
                @foreach ($categories as $category)
                    <button type="button" class="category-btn" data-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" id="selected-category" name="category_id[]">  <!-- 配列として送信 -->
            @error('category_id')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="condition" class="sell__label">商品の状態</label>
            <select class="sell__select" name="condition" id="condition">
                <option value="" selected disabled>選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition }}">{{ $condition }}</option>
                @endforeach
            </select>
            @error('condition')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="brand" class="sell__label">ブランド</label>
            <select class="sell__select" name="brand" id="brand">
                <option value="" selected disabled>選択してください</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
            @error('brand')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <h2 class="sell__section-title">商品名と説明</h2>
        <div>
            <label for="name" class="sell__label">商品名</label>
            <input class="sell__input" type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="description" class="sell__label">商品の説明</label>
            <input class="sell__input" type="text" name="description" id="description" value="{{ old('description') }}">
            @error('description')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price" class="sell__label">販売価格</label>
            <input class="sell__input" type="number" name="price" id="price" value="{{ old('price') }}" min="1" placeholder="¥">
            @error('price')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="sell__submit-button">出品する</button>
    </form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let categoryButtons = document.querySelectorAll('.category-btn');
        let selectedCategories = [];

        categoryButtons.forEach(button => {
            button.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-id');
                
                // ボタンの選択状態をトグル
                if (selectedCategories.includes(categoryId)) {
                    selectedCategories = selectedCategories.filter(id => id !== categoryId);  // 削除
                    this.classList.remove('active');
                } else {
                    selectedCategories.push(categoryId);  // 追加
                    this.classList.add('active');
                }

                // 選択されたカテゴリーのIDをhiddenフィールドに設定
                document.getElementById('selected-category').value = selectedCategories.join(',');
            });
        });
    });
</script>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById('preview');
            preview.src = e.target.result;
            preview.style.display = 'block';

            // 画像を選択したら、ボタンを非表示にする
            document.getElementById('image-placeholder').style.display = 'none';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>


@endsection
