@extends('layouts.app')

@section('title')
Attendance
@endsection

@section('css')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/date.css') }}" />
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
    <form class="header_wrap" action="/changeDate" method="get">
        @csrf
        <button class="date_change-button" name="prevDate"><</button>
        <input type="hidden" name="date" value="{{ $date }}">
        <p class="header_text">{{ $date->format('Y-m-d') }}</p>
        <button class="date_change-button" name="nextDate">></button>
    </form>
@endsection

@section('content')
    <div class="table">
        <table class="table_inner">
            <tr class="table_row">
                <th class="table_header">名前</th>
                <th class="table_header">勤務開始</th>
                <th class="table_header">勤務終了</th> 
                <th class="table_header">休憩時間</th>
                <th class="table_header">勤務時間</th>
            </tr>
            @foreach ($works as $work)
                @if ($work->date === $date->format('Y-m-d'))
                <tr class="table_row">
                    <td class="table_item">{{ $work->user->name }}</td>
                    <td class="table_item">{{ $work->work_start }}</td>
                    <td class="table_item">{{ $work->work_end }}</td>
                    <td class="table_item">
                    @foreach ($restSums as $key => $restSum)
                    @if ($key === $work->id)
                    {{ gmdate('H:i:s', $restSum) }}
                    @endif
                    @endforeach
                    </td>
                    @foreach ($workSums as $workSum)
                    @if ($workSum['work_id'] === $work->id)
                    <td class="table_item">{{ gmdate('H:i:s', $workSum['work_time']) }}</td>
                    @endif
                    @endforeach
                </tr>
                @endif
            @endforeach
        </table>
    </div>
    <div class="d-flex justify-content-center">
    {{$works->appends($_GET)->links('vendor/pagination/bootstrap-4')}}
    </div>
@endsection