@extends('layouts.app')

@section('title')
Login
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
@endsection

@section('heading')
ログイン
@endsection

@section('content')
      <form class="form" action="/login" method="post">
      @csrf
        <div class="form__group">
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" />
            </div>
            <div class="form__error">
            @error('email')
            {{ $message }}
            @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="text" name="password" placeholder="パスワード" />
            </div>
            <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
            </div>
          </div>
        </div>
        <div class="form__button">
          <button class="form__button-submit" type="submit">ログイン</button>
        </div>
      </form>
      <div>
        <p class="login__group">アカウントをお持ちでない方はこちらから</p>
        <a class="link" href="/register">会員登録</a>
      </div>
@endsection