@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div>
    <h1>プロフィール設定</h1>

    <!-- プロフィール画像の表示 -->
    <div>
        @if(auth()->user()->profile_image)
            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="プロフィール画像" width="150">
        @else
            <div class="default-profile">
        @endif
    </div>

    <form method="POST" action="/mypage/profile">
        @csrf

        <div>
            
            <input id="profile_image" type="file" name="profile_image">
        </div>

        <div>
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}">
        </div>

        <div>
            <label for="postal_code">郵便番号</label>
            <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
        </div>

        <div>
            <label for="address">住所</label>
            <input id="address" type="text" name="address" value="{{ old('address', auth()->user()->address) }}">
        </div>

        <div>
            <label for="building">建物名</label>
            <input id="building" type="text" name="building" value="{{ old('building', auth()->user()->building) }}">
        </div>

        <div>
            <button type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection
