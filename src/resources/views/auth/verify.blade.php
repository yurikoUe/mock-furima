@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="verify">
    <h1>メール認証が必要です</h1>
    <p>登録したメールアドレスに認証用のリンクを送信しました。</p>
    <p>メール内のリンクをクリックして、認証を完了してください。</p>

    @if (session('resent'))
        <div class="verify__alert" role="alert">
            認証メールを再送しました！メールをご確認ください。
        </div>
    @endif

    <p>もしメールが届かない場合は、以下のボタンを押して再送してください。</p>
    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="verify__button">認証メールを再送する</button>
    </form>
</div>
@endsection
