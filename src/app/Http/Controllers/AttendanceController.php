<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AttendanceController extends Controller
{
    //打刻
    public function stamp(Request $request)
    {
        $user = Auth::user();
        $user_id = Auth::user()->id;
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $work_date = Work::where('user_id', $user_id)
            ->where('date', $today)
            ->first();
    
        //勤務開始
        if ($request->has('work_start')) {  
            Work::create([
                'user_id' => $user->id,
                'work_start' => Carbon::now(),
                'date' => Carbon::now(),
            ]);
        User::find($user->id)->update(['status'=>'1']);
        return redirect('/')->with('message', '出勤打刻が完了しました。'); 
        }

        //勤務終了
        if ($request->has('work_end')) {

            //退勤打刻の際に、その日の出勤データがない場合、前日の勤務終了時刻に"23:59:59"を入力し、当日の勤務データを新規作成し、勤務開始時刻に"00:00:00"を入れ、勤務終了に現在の時刻を入れる
            /**
             * 
             * 
             */
            if (!$work_date) {
                Work::where('user_id', $user->id)
                    ->where('date', $yesterday)
                    ->first()
                    ->update(['work_end' => '23:59:59']);

                Work::create([
                    'user_id' => $user->id,
                    'work_start' => '00:00:00',
                    'date' => Carbon::now(),
                    'work_end' => Carbon::now(),
                ]);
            }
            //その日の出勤データがある場合、勤務終了に現在の時刻を入れる
            else {
                Work::where('user_id', $user->id)
                    ->where('date', $today)
                    ->first()
                    ->update(['work_end' => Carbon::now()]);  
            }
        User::find($user->id)->update(['status'=>'3']);
        return redirect('/')->with('message', '退勤打刻が完了しました。'); 
        }

        $work = Work::where('user_id', $user_id)
            ->where('date', $today)
            ->first();
    
        $rest = Rest::where('user_id', $user_id)
            ->where('date', $today)
            ->latest();

        //休憩開始
        if ($request->has('rest_start')) {  

            //休憩開始打刻の際に、その日の出勤データがない場合、前日の勤務終了時刻に"23:59:59"を入力し、当日の勤務データを作成し、勤務開始時刻に"00:00:00"を入れ、休憩開始に現在の時刻を入れる
            if (!$work_date) {
                Work::where('user_id', $user->id)
                    ->where('date', $yesterday)
                    ->first()
                    ->update(['work_end' => '23:59:59']);
            
                Work::create([
                'user_id' => $user->id,
                'work_start' => '00:00:00',
                'date' => Carbon::now(),
                ]);
            
                $work = Work::where('user_id', $user_id)
                ->where('date', $today)
                ->first();
            
                Rest::create([
                'work_id' => $work->id,
                'rest_start' => Carbon::now(),
                ]);
        
            User::find($user->id)->update(['status'=>'2']);
            return redirect('/')->with('message', '休憩が開始されました。'); 

            } else {
                Rest::create([
                    'work_id' => $work->id,
                    'rest_start' => Carbon::now(),
                ]);
            User::find($user->id)->update(['status'=>'2']);
            return redirect('/')->with('message', '休憩が開始されました。');
            }
        }

        //休憩終了
        if ($request->has('rest_end')) {  

            //休憩終了打刻の際に、その日の出勤・休憩データがない場合、前日の勤務終了時刻に"23:59:59"を入力し、当日の勤務データを作成し、勤務開始時刻に"00:00:00"を入れ、前日の休憩データの休憩終了時刻に"23:59:59"を入力し、当日の休憩データを作成し、休憩開始時刻に"00:00:00"、休憩終了時刻に現在の時刻を入れる
            if (!$work_date) {

                Work::where('user_id', $user->id)
                    ->where('date', $yesterday)
                    ->first()
                    ->update(['work_end' => '23:59:59']);
            
                Work::create([
                'user_id' => $user->id,
                'work_start' => '00:00:00',
                'date' => Carbon::now(),
                ]);

                Rest::where('user_id', $user->id)
                    ->where('date', $yesterday)
                    ->latest()
                    ->update(['rest_end' => '23:59:59']);

                Rest::create([
                    'work_id' => $work->id,
                    'rest_start' => '00:00:00',
                ]);        
            }

            Rest::where('work_id', $work->id)
                ->where('rest_end', null)
                ->latest()
                ->update(['rest_end' => Carbon::now()]);

            User::find($user->id)->update(['status'=>'1']);
            return redirect('/')->with('message', '休憩が終了しました。'); 
        }
    }

    //日付一覧ページ
    public function date()
    {
        $date = Carbon::now();
        $works = Work::with('user','rests')
            ->whereDate('date', $date)
            ->paginate(5);

        //1勤務あたりの拘束時間
        $work_totals = [];
        foreach ($works as $work) {
            $id = $work->id;
            $work_start = \Carbon\Carbon::parse($work->work_start);
            $work_end = \Carbon\Carbon::make($work->work_end);
            if ($work_end == null) {
                $work_totals[] = ['id' => $id, 'work_time' => null];
            } else {
                $work_time = $work_end->diffInSeconds($work_start);
                $work_totals[] = ['id' => $id, 'work_time' => $work_time];
            }
        }
           
        //各休憩時間
        $rests = Rest::all();
        $rest_totals = [];
        foreach ($rests as $rest) {
            $work_id = $rest->work_id;
            $start = \Carbon\Carbon::parse($rest->rest_start);
            $end = \Carbon\Carbon::parse($rest->rest_end);
            $rest_time = $end->diffInSeconds($start);
            $rest_totals[] = ['work_id' => $work_id, 'rest_time' => $rest_time];
        }

        //1日あたりの合計休憩時間
        $restSums = [];
        foreach ($rest_totals as $rest_total){
            $work_id = $rest_total['work_id'];
            $rest_totalSum = $rest_total['rest_time'];

        if (!isset($restSums[$work_id])) {
            $restSums[$work_id] = 0;
        }

        $restSums[$work_id] += $rest_totalSum;
        }

        //1日あたりの合計勤務時間（拘束時間-合計休憩時間）
        $workSums = [];
        foreach ($work_totals as $work_total){
            $id = $work_total['id'];
            $work_total_time = $work_total['work_time'];
            
            foreach ($restSums as $key => $rest_totalSum) {
                if ($key === $id){
                    if ($work_total_time == null){
                        $workSums[] = ['work_id' => $id, 'work_time' => null];
                    } else {
                        $workMinusRest = $work_total_time - $rest_totalSum;
                        $workSums[] = ['work_id' => $id, 'work_time' => $workMinusRest];
                    }
                }
            }
            if (array_key_exists($id, $restSums)) {
            } else {
            $workSums [] = ['work_id' => $id, 'work_time' => $work_total_time];             
            }
        }

        return view('date', compact('works', 'work_totals', 'work_id', 'rest_totals', 'date', 'restSums', 'workSums'));
    }

    //日付一覧ページ（日付の変更）
    public function changeDate(Request $request)
    {
        $date = Carbon::parse($request->input('date'));

        if ($request->has('prevDate')) {
            $date->subDay();
        }

        if ($request->has('nextDate')) {
            $date->addDay();
        }   

        $works = Work::with('user','rests')
            ->whereDate('date', $date)
            ->paginate(5);

        //1勤務あたりの拘束時間
        $work_totals = [];
        foreach ($works as $work){
            $id = $work->id;
            $work_start = \Carbon\Carbon::parse($work->work_start);
            $work_end = \Carbon\Carbon::make($work->work_end);
            if ($work_end == null) {
                $work_totals[] = ['id' => $id, 'work_time' => null];
            } else {
                $work_time = $work_end->diffInSeconds($work_start);
                $work_totals[] = ['id' => $id, 'work_time' => $work_time];
            }
        }
    
        //各休憩時間
        $rests = Rest::all();
        $rest_totals = [];
        foreach ($rests as $rest){
            $work_id = $rest->work_id;
            $start = \Carbon\Carbon::parse($rest->rest_start);
            $end = \Carbon\Carbon::parse($rest->rest_end);
            $rest_time = $end->diffInSeconds($start);
            $rest_totals[] = ['work_id' => $work_id, 'rest_time' => $rest_time];
        }

        //1日あたりの合計休憩時間
        $restSums = [];
        foreach ($rest_totals as $rest_total){
            $work_id = $rest_total['work_id'];
            $rest_totalSum = $rest_total['rest_time'];

            if(!isset($restSums[$work_id])) {
            $restSums[$work_id] = 0;
            }

            $restSums[$work_id] += $rest_totalSum;
        }

        //1日あたりの合計勤務時間（拘束時間-合計休憩時間）
        $workSums = [];
        foreach ($work_totals as $work_total) {
            $id = $work_total['id'];
            $work_total_time = $work_total['work_time'];

            foreach ($restSums as $key => $rest_totalSum) {
                if ($key === $id) {
                    if ($work_total_time == null) {
                        $workSums[] = ['work_id' => $id, 'work_time' => null];
                    } else {
                        $workMinusRest = $work_total_time - $rest_totalSum;
                        $workSums[] = ['work_id' => $id, 'work_time' => $workMinusRest];
                    }
                }
            }
            if (array_key_exists($id, $restSums)) {
            } else {
                $workSums[] = ['work_id' => $id, 'work_time' => $work_total_time];
            }
        }

        return view('date', compact('works', 'work_totals', 'work_id', 'rest_totals', 'date', 'restSums', 'workSums'));
    }

    //ユーザー一覧ページ
    public function list()
    {   
        $works = Work::all();
        $workDates = [];
        foreach ($works as $work) {
            $user_id = $work['user_id'];
            $work_date = $work['date'];
            $workDates [] = ['user_id' => $user_id, 'work_date' => $work_date];
        }
        $latestDates = [];
        foreach ($workDates as $workDate) {
            $user_id = $workDate['user_id'];

            if (!isset($latestDates[$user_id]) || $workDate['work_date'] > $latestDates[$user_id]['work_date']) {
            $latestDates[$user_id] = $workDate;
            }
        }

        $users = User::all();
        $userLists = [];
        foreach ($users as $user) {
            $id = $user['id'];
            $name = $user['name'];
            $userLists [] = ['user_id' => $id, 'user_name' => $name];           
        }

        $lists =[];
        foreach ($userLists as $userList) {
            foreach ($latestDates as $latestDate) {
                if ($latestDate['user_id']===$userList['user_id']) {
                    $lists [] = ['user_id' => $userList['user_id'], 'user_name' => $userList['user_name'], 'work_date' => $latestDate['work_date']];
                }
            }
            if (in_array($userList['user_id'], array_column($latestDates, 'user_id'))) {
            } else {
                $lists [] = ['user_id' => $userList['user_id'], 'user_name' => $userList['user_name'], 'work_date' => '-'];
            }
        }

        //直近勤務日降順
        foreach ($lists as $key => $value) {
            $sort_keys[$key] = $value['work_date'];
        }
        array_multisort($sort_keys, SORT_DESC, $lists);

        $collection = collect($lists);
        $listdata = $this->paginate($collection, 15, null, ['path'=>'/list']);

        return view('list', compact('listdata'));
    }

    //ユーザー一覧ページネーション
    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    //ユーザー毎勤怠表ページ
    public function user(Request $request)
    {
        $request_user = $request->all(); 
        $works = Work::with('user', 'rests')->UserSearch($request->user_id)->get();

        //各休憩時間
        $rests = Rest::all();
        $rest_totals = [];
        foreach ($rests as $rest) {
            $work_id = $rest->work_id;
            $start = \Carbon\Carbon::parse($rest->rest_start);
            $end = \Carbon\Carbon::parse($rest->rest_end);
            $rest_time = $end->diffInSeconds($start);
            $rest_totals [] = ['work_id' => $work_id, 'rest_time' => $rest_time];
        }

        //1日あたりの合計休憩時間
        $restSums = [];
        foreach ($rest_totals as $rest_total) {
            $work_id = $rest_total['work_id'];
            $rest_totalSum = $rest_total['rest_time'];
        if (!isset($restSums[$work_id])) {
            $restSums[$work_id] = 0;
        }
        $restSums[$work_id] += $rest_totalSum;
        }

        //1勤務あたりの拘束時間
        $work_totals = [];
        foreach ($works as $work) {
            $id = $work->id;
            $work_start = \Carbon\Carbon::parse($work->work_start);
            $work_end = \Carbon\Carbon::make($work->work_end);
            $work_date = $work->date;
            if ($work_end == null) {
                $work_totals[] = ['id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'work_time' => null];
            } else {
                $work_time = $work_end->diffInSeconds($work_start);
                $work_totals[] = ['id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'work_time' => $work_time];
            }
        }

        //1日あたりの合計勤務時間（拘束時間-合計休憩時間）
        $workSums = [];
        foreach ($work_totals as $work_total) {
            $id = $work_total['id'];
            $work_total_time = $work_total['work_time'];
            $work_date = $work_total['work_date'];
            $work_start = $work_total['work_start'];
            $work_end = $work_total['work_end'];

            foreach ($restSums as $key => $rest_totalSum) {
                $rest_time = $rest_totalSum;
                if ($key === $id) {
                    if ($work_total_time == null) {
                        $workSums[] = ['work_id' => $id, 'work_date' => $work_date,'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => null];
                    } else {
                        $workMinusRest = $work_total_time - $rest_totalSum;
                        $workSums[] = ['work_id' => $id, 'work_date' => $work_date,'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => $workMinusRest];
                    }
                }
            }
            if (array_key_exists($id, $restSums)) {
            } else {
                $workSums[] = ['work_id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => $work_total_time];
            }
        }

        // 当月を取得
        $year = Carbon::today()->format('Y');
        $month = Carbon::today()->format('m');
        $thisMonth = Carbon::Create($year, $month, 01, 00, 00, 00);

        // 当月の期間を取得
        $thisMonthPeriod = $this->getThisMonthPeriod($thisMonth);

        return view('user')
            ->with('request_user', $request_user)
            ->with('workSums', $workSums)
            ->with('thisMonth', $thisMonth)
            ->with('thisMonthPeriod', $thisMonthPeriod);
    }

    //ユーザー毎勤怠表ページ（表示月の変更）
    public function userChangeMonth(Request $request)
    {
        $request_user = $request->all();
        $works = Work::with('user', 'rests')->UserSearch($request->user_id)->get();

        $thisMonth = Carbon::parse($request->input('month'));

        if ($request->has('prevMonth')) {
            $thisMonth->subMonth();
        }

        if ($request->has('nextMonth')) {
            $thisMonth->addMonth();
        }   

        //各休憩時間
        $rests = Rest::all();
        $rest_totals = [];
        foreach ($rests as $rest) {
            $work_id = $rest->work_id;
            $start = \Carbon\Carbon::parse($rest->rest_start);
            $end = \Carbon\Carbon::parse($rest->rest_end);
            $rest_time = $end->diffInSeconds($start);
            $rest_totals [] = ['work_id' => $work_id, 'rest_time' => $rest_time];
        }

        //1日あたりの合計休憩時間
        $restSums = [];
        foreach ($rest_totals as $rest_total) {
            $work_id = $rest_total['work_id'];
            $rest_totalSum = $rest_total['rest_time'];
            if (!isset($restSums[$work_id])) {
                $restSums[$work_id] = 0;
            }
        $restSums[$work_id] += $rest_totalSum;
        }

        //1勤務あたりの拘束時間
        $work_totals = [];
        foreach ($works as $work) {
            $id = $work->id;
            $work_start = \Carbon\Carbon::parse($work->work_start);
            $work_end = \Carbon\Carbon::make($work->work_end);
            $work_date = $work->date;
            if ($work_end == null) {
                $work_totals[] = ['id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'work_time' => null];
            } else {
                $work_time = $work_end->diffInSeconds($work_start);
                $work_totals[] = ['id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'work_time' => $work_time];
            }
        }

        //1日あたりの合計勤務時間（拘束時間-合計休憩時間）
        $workSums = [];
        foreach ($work_totals as $work_total) {
            $id = $work_total['id'];
            $work_total_time = $work_total['work_time'];
            $work_date = $work_total['work_date'];
            $work_start = $work_total['work_start'];
            $work_end = $work_total['work_end'];

            foreach ($restSums as $key => $rest_totalSum) {
                $rest_time = $rest_totalSum;
                if ($key === $id) {
                    if ($work_total_time == null) {
                        $workSums[] = ['work_id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => null];
                    } else {
                        $workMinusRest = $work_total_time - $rest_totalSum;
                        $workSums[] = ['work_id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => $workMinusRest];
                    }
                }
            }
            if (array_key_exists($id, $restSums)) {
            } else {
                $workSums[] = ['work_id' => $id, 'work_date' => $work_date, 'work_start' => $work_start, 'work_end' => $work_end, 'rest_time' => $rest_totalSum, 'work_time' => $work_total_time];
            }
        }

        // 当月の期間を取得
        $thisMonthPeriod = $this->getThisMonthPeriod($thisMonth);

        return view('user')
            ->with('request_user', $request_user)
            ->with('workSums', $workSums)
            ->with('thisMonth', $thisMonth)
            ->with('thisMonthPeriod', $thisMonthPeriod);
    }

    //当月の期間を取得
    private function getThisMonthPeriod($thisMonth)
    {
        // 月初を取得
        $start = $thisMonth->copy()->startOfMonth();
        // 月末を取得
        $end = $thisMonth->copy()->endOfMonth();
        // 月初～月末の期間を取得
        return CarbonPeriod::create($start->format('Y-m-d'), $end->format('Y-m-d'))->toArray();
    }
}