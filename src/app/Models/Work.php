<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class Work extends Model
{
    use HasFactory;

    protected $guarded = array('id');

    public static $rules = array(
        'user_id' => 'required',
    );

    protected $fillable = [
        'user_id',
        'date', 
        'work_start', 
        'work_end'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function scopeUserSearch($query, $user_id)
    {
        if (!empty($user_id)){
            $query->where('user_id', $user_id);
        }
    }

    //ユーザーごとの直近の勤務日
    public function latestWork()
    {
        $works = Work::all();
        $workDates = [];
        foreach ($works as $work){
            $user_id = $work['user_id'];
            $work_date = $work['date'];
            $workDates [] = ['user_id' => $user_id, 'work_date' => $work_date];
        }
        $latestDates = [];
        foreach ($workDates as $workDate){
            $user_id = $workDate['user_id'];

            if (!isset($latestDates[$user_id]) || $workDate['work_date'] > $latestDates[$user_id]['work_date']) {
            $latestDates[$user_id] = $workDate;
            }
        }

        $users = User::all();
        $userLists = [];
        foreach ($users as $user){
            $id = $user['id'];
            $name = $user['name'];
            $userLists [] = ['user_id' => $id, 'user_name' => $name];           
        }

        $lists =[];
        foreach ($userLists as $userList){
            foreach ($latestDates as $latestDate){
                if ($latestDate['user_id']===$userList['user_id']){
                    $lists [] = ['user_id' => $userList['user_id'], 'user_name' => $userList['user_name'], 'work_date' => $latestDate['work_date']];
                }
            }
            if (in_array($userList['user_id'], array_column($latestDates, 'user_id'))){
            }else{
                $lists [] = ['user_id' => $userList['user_id'], 'user_name' => $userList['user_name'], 'work_date' => '-'];
            }
        }

        //直近勤務日降順
        foreach ($lists as $key => $value){
            $sort_keys[$key] = $value['work_date'];
        }
        array_multisort($sort_keys, SORT_DESC, $lists);

        return $lists;
    }
}