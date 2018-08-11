<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestAbsent extends Model
{
    //
    protected $table = 'tbl_request_absents';
    protected $fillable = ['id','staff_id', 'start_date', 'end_date', 'reason','is_deleted'];
    public function hasAbsent(){
        return $this->hasOne('App\Model\Absent','request_absent_id', 'id');
    }
    public function belongsToStaff(){
        return $this->belongsTo('App\Model\Staff','staff_id', 'id');
    }
}
