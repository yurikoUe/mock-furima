@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div>
    <h1>商品の出品</h1>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
            <!-- プレビュー表示エリア -->
            <div id="image-preview">
                <img id="preview" src="#" alt="画像プレビュー" style="display: none; max-width: 200px; max-height: 200px;" />
            </div>
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <h2>商品の詳細</h2>
        <div>
            <label for="category">カテゴリー</label>
            <div id="categories" class="category-buttons">
                @foreach ($categories as $category)
                    <button type="button" class="category-btn" data-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" id="selected-category" name="category_id[]">  <!-- 配列として送信 -->
            @error('category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                @endforeach
            </select>
            @error('condition')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="brand">ブランド</label>
            <select name="brand" id="brand">
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
            @error('brand')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <h2>商品名と説明</h2>
        <div>
            <label for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
        </div>
        <div>
            <label for="description">商品の説明</label>
            <input type="text" name="description" id="description" value="{{ old('description') }}">
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" min="1">
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">出品する</button>
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
            preview.src = e.target.result;  // 読み込んだ画像を表示
            preview.style.display = 'block';  // 画像が選択されたらプレビューを表示
        }

        if (file) {
            reader.readAsDataURL(file);  // 画像を読み込む
        }
    }
</script>
</div>


@endsection
