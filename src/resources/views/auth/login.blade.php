@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="form__title">会員登録</h1>
    <form method="POST" action="/login" class="form">
        @csrf

        <div class="form__group">
            <label for="email" class="form__label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form__input">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form__group">
            <label for="password" class="form__label">パスワード</label>
            <input id="password" type="password" name="password" class="form__input">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="submit-btn">ログインする</button>
    </form>
    <a href="/register" class="login-link">会員登録はこちら</a>
</div>
@endsection
