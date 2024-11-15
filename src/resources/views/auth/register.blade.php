@extends('layouts.app')

@section('title')
Register
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
@endsection

@section('heading')
会員登録
@endsection

@section('content')
      <form class="form" action="/register" method="post">
      @csrf
        <div class="form__group">
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="text" name="name" placeholder="名前" />
            </div>
            <div class="form__error">
            @error('name')
            {{ $message }}
            @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="email" name="email" placeholder="メールアドレス" />
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
              <input type="password" name="password" placeholder="パスワード" />
            </div>
            <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-content">
            <div class="form__input--text">
             <input type="password" name="password_confirmation" placeholder="確認用パスワード" />
            </div>
            <div class="form__error">
            @error('password_confirmation')
            {{ $message }}
            @enderror
            </div>
          </div>
        </div>
        <div class="form__button">
          <button class="form__button-submit" type="submit">会員登録</button>
        </div>
      </form>
      <div>
        <p class="login__group">アカウントをお持ちの方はこちらから</p>
        <a class="link" href="/login">ログイン</a>
      </div>
@endsection