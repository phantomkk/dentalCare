<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TreatmentHistory extends Model
{
    //
    protected $table = 'tbl_treatment_histories';
    protected $fillable = ['id', 'treatment_id','patient_id', 'description', 'create_date', 'finish_date'];

    public function belongsToTreatment(){
        return $this->belongsTo('App\Model\Treatment', 'treatment_id', 'id');
    }

}
