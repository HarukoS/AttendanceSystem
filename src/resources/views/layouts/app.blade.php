<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <a class="header__logo" href="/">
        Atte
      </a>
    </div>
    <div class="header__nav">
    @yield('header-nav') 
    </div>
  </header>

  <main>
    <div class="form__content">
      <div class="form__heading">
        <h2>@yield('heading')</h2>
      </div>
    @yield('content') 
    </div>
  </main>
</body>

<footer class="footer">
  <small class="copyright">Atte, inc.</small>
</footer>

</html>