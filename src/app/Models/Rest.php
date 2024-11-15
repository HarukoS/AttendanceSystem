<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $guarded = array('id');

    public static $rules = array(
        'work_id' => 'required',
    );

    protected $fillable = [
        'work_id',
        'rest_start', 
        'rest_end'
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
