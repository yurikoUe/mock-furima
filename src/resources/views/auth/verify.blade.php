@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メール認証が必要です</h1>
    <p>登録したメールアドレスに認証用のリンクを送信しました。</p>
    <p>メール内のリンクをクリックして、認証を完了してください。</p>

    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            認証メールを再送しました！メールをご確認ください。
        </div>
    @endif

    <p>もしメールが届かない場合は、以下のボタンを押して再送してください。</p>
    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary">認証メールを再送する</button>
    </form>
</div>
@endsection
