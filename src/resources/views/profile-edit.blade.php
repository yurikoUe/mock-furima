@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login-register-profile.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>

    <!-- 成功メッセージの表示 -->
    @if (session('success'))
        <div style="color: green; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <!-- プロフィール画像の表示 -->
    <div class="profile__image">
        @if(auth()->user()->profile_image)
            
            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="プロフィール画像" class="rounded-circle">
        @else
            <img src="{{ asset('storage/images/default-profile-image.png') }}" alt="デフォルトプロフィール画像" class="default-profile">
        @endif
    

    <form method="POST" action="/mypage/profile" enctype="multipart/form-data" class="form">
        @csrf

        <div class="form__group">
            <label for="profile_image" class="custom-file-label">画像を選択</label>
            <input id="profile_image" type="file" name="profile_image" class="custom-file-input" accept="image/jpeg, image/png, image/jpg">
        </div>
    </div>
        <div class="form__group">
            <label for="name" class="form__label">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form__input">
        </div>

        <div class="form__group">
            <label for="postal_code" class="form__label">郵便番号</label>
            <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}" class="form__input">
        </div>

        <div class="form__group">
            <label for="address" class="form__label">住所</label>
            <input id="address" type="text" name="address" value="{{ old('address', auth()->user()->address) }}" class="form__input">
        </div>

        <div class="form__group">
            <label for="building" class="form__label">建物名</label>
            <input id="building" type="text" name="building" value="{{ old('building', auth()->user()->building) }}" class="form__input">
        </div>

        <div>
            <button type="submit" class="submit-btn">更新する</button>
        </div>
    </form>
</div>
@endsection
