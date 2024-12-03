@extends('layouts.app')

@section('title')
Attendance
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/user.css') }}" />
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
    {{$request_user['user_name']}}さんの勤怠表
    <form class="header_wrap" action="/userChangeMonth" method="get">
        @csrf
        <button class="date_change-button" name="prevMonth"><</button>
        <input type="hidden" name="month" value="{{ $thisMonth }}">
        <input type="hidden" name="user_id" value="{{ $request_user['user_id'] }}">
        <input type="hidden" name="user_name" value="{{ $request_user['user_name'] }}">
        <p class="header_text">{{ $thisMonth->format('Y年m月') }}</p>
        <button class="date_change-button" name="nextMonth">></button>
    </form>
@endsection

@section('content')
    <div class="table">
        <table class="table_inner">
            <tr class="table_row">
                <th class="table_header">日付</th>
                <th class="table_header">勤務開始</th>
                <th class="table_header">勤務終了</th> 
                <th class="table_header">休憩時間</th>
                <th class="table_header">勤務時間</th>
            </tr>
            @foreach ($thisMonthPeriod as $thisMonthDate)
            <tr class="table_row">
                <td class="table_item">{{ $thisMonthDate->format('d日') }}</td>
                @foreach ($workSums as $workSum)
                @if ($workSum['work_date'] === $thisMonthDate->format('Y-m-d'))
                <td class="table_item">{{ $workSum['work_start'
                    ]->format('H:i:s') }}</td>
                @if ($workSum['work_end'] !== null)
                <td class="table_item">{{ $workSum['work_end']->format('H:i:s') }}</td>
                @else
                <td class="table_item">-</td>
                @endif
                @if ($workSum['rest_time'] !== null)
                <td class="table_item">{{ gmdate('H:i:s', $workSum['rest_time']) }}</td>
                @else
                <td class="table_item">-</td>
                @endif
                @if ($workSum['work_time'] !== null)
                <td class="table_item">{{ gmdate('H:i:s', $workSum['work_time']) }}</td>
                @else
                <td class="table_item">-</td>
                @endif
                @endif
                @endforeach
                @if (!(in_array($thisMonthDate->format('Y-m-d'), array_column($workSums, 'work_date'))))
                <td class="table_item">-</td>
                <td class="table_item">-</td>
                <td class="table_item">-</td>
                <td class="table_item">-</td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>
@endsection