@extends('layouts.app')

@section('title')
Attendance
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('header-nav')
  <nav class="header-nav">
  <ul class="header-nav-list">
    <li class="header-nav-item"><a href="/">ホーム</a></li>
    <li class="header-nav-item"><a href="/date">日付一覧</a></li>
    <li class="header-nav-item"><a href="/list">ユーザー一覧</a></li>
    <li class="header-nav-item"><a href="{{route('logout')}}">ログアウト</a></li>
  </ul>
  </nav>
@endsection

@section('heading')
  {{ $user->name }}さんお疲れ様です！
@endsection

@section('content')
  <form class="form" action="/stamp" method="post">
  @csrf
    <div class="form__group">
      @if($user->status == 0)
      <button class="form__group-button" type="submit" name="work_start">勤務開始</button>
      @else
      <button class="form__group-button" type="submit" name="work_start" disabled>勤務開始</button>
      @endif
      @if($user->status == 1)
      <button class="form__group-button" type="submit" name="work_end">勤務終了</button>
      @else
      <button class="form__group-button" type="submit" name="work_end" disabled>勤務終了</button>
      @endif
    </div>
    <div class="form__group">
      @if($user->status == 1)
      <button class="form__group-button" type="submit" name="rest_start">休憩開始</button>
      @else
      <button class="form__group-button" type="submit" name="rest_start" disabled>休憩開始</button>
      @endif
      @if($user->status == 2)        
      <button class="form__group-button" type="submit" name="rest_end">休憩終了</button>
      @else
      <button class="form__group-button" type="submit" name="rest_end" disabled>休憩終了</button>
      @endif
    </div>
  </form>
@if(session('message'))
  <div class="message">
  {{ session('message') }}
  </div>
@endif
@if($user->status == 3)
  <div class="message">
  本日は出勤済みです。
  </div>
@endif
@endsection