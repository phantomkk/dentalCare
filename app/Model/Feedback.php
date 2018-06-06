<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    //
    protected $table = 'tbl_feedbacks';
    protected $fillable = ['id', 'content', 'patient_id', 'treatment_detail_id', 'date_feedback', 'number_start'];


    public function belongsToPatient(){
        return $this->belongsTo('App\Model\User', 'id', 'patient_id');
    }
}