<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtech</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-utilities">
        <a class="header__logo" href="/">
          <img src="{{ asset('storage/images/logo.svg') }}" alt="サイトロゴ">
        </a>
        <!-- 検索フォーム -->
        <form action="{{ route('index') }}" method="GET">
            <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request()->get('keyword') }}">
            <input type="hidden" name="tab" value="{{ request()->get('tab') }}">
        </form>
        <nav>
          <ul class="header-nav">
            @if (Auth::check())
            <li class="header-nav__item">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="header-nav__button">ログアウト</button>
              </form>
            </li>
            @else
            <li class="header-nav__item">
              <a class="header-nav__link" href="/login">ログイン</a>
            </li>
            @endif
            <li class="header-nav__item">
              <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
            <li class="header-nav__item">
              <a class="header-nav__link" href="/sell">出品</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>
