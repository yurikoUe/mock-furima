@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="form__title">会員登録</h1>
    <form method="POST" action="/register" class="form">
        @csrf
        
        <div class="form__group">
            <label for="name" class="form__label">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form__input">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

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

        <div class="form__group">
            <label for="password_confirmation" class="form__label">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form__input">
            @if ($errors->has('password_confirmation'))
                <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
            @endif
        </div>

        <button type="submit" class="submit-btn">登録する</button>
    </form>
    <a href="/login" class="login-link">ログインはこちら</a>
</div>
@endsection
