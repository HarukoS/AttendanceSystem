<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Work;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->format('Y-m-d');
        $user_id = Auth::user()->id;
        $user = Auth::user();
        $work_date = Work::where('user_id', $user_id)
            ->where('date', $today)
            ->first();
        $status = Auth::user()->status;

        //ログインの際にその日の打刻データがなく、且つ退勤済みステータス3の場合、ステータスを出勤前0にリセットしてindexを表示
        if (!$work_date && $status == '3') {
            User::find($user->id)->update(['status'=>'0']);
            $user = User::find($user->id);
            return view('index', ['user' => $user]);
        } else {
            return view('index', ['user' => $user]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
