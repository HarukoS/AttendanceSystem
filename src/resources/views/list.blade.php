@extends('layouts.app')

@section('title')
List
@endsection

@section('css')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/list.css') }}" />
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
ユーザー一覧
@endsection

@section('content')
    <div class="table">
        <table class="table_inner">
            <tr class="table_row">
                <th class="table_header">No.</th>
                <th class="table_header">名前</th>
                <th class="table_header">直近の出勤日</th>
                <th class="table_header"></th>
            </tr>
            @foreach ($listdata as $key => $data)
            <tr class="table_row">
                <td class="table_item">{{$key+1}}</td>
                <td class="table_item">{{ $data['user_name'] }}</td>
                <td class="table_item">{{ $data['work_date'] }}</td>
                <td class="table_item">
                <form action="/user" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                <input type="hidden" name="user_name" value="{{$data['user_name']}}">
                <button class="detail-button_submit" type="submit">月別勤怠表</button>
                </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="d-flex justify-content-center">
    {{$listdata->links('vendor/pagination/bootstrap-4')}}
    </div>
@endsection