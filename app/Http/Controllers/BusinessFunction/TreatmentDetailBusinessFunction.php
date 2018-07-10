<?php
/**
 * Created by PhpStorm.
 * User: gamamhehe
 * Date: 6/14/2018
 * Time: 8:11 PM
 */

namespace App\Http\Controllers\BusinessFunction;

use App\Model\TreatmentDetail;
use App\Model\TreatmentDetailStep;
use App\Model\TreatmentHistory;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait TreatmentDetailBusinessFunction
{
    public function createTreatmentDetail($idTreatmentHistory, $note, $dentist_id)
    {
        if($note == null){
            $note="&nsbp";
        }
        DB::beginTransaction();
        try {
            $idTreatmentDetail = TreatmentDetail::create([
                'treatment_history_id' => $idTreatmentHistory,
                'staff_id' => $dentist_id,
                'note' => $note,
                'create_date' => Carbon::now()
            ])->id;
            DB::commit();
            return $idTreatmentDetail;
        } catch (\Exception $e) {
            DB::rollback();
            return false;

        }
    }

    public function createTreatmentDetailStep($listStep, $idTreatmentDetail)
    {
        DB::beginTransaction();
        try {
            foreach ($listStep as $step) {
                TreatmentDetailStep::create([
                    'treatment_detail_id' => $idTreatmentDetail,
                    'step_id' => $step,
                    'description'=>'huhu',
                ]);
            }
 
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;

        }
    }
    public function getTreatmentDetailStep($idTreatmentDetail){
        $listStep = TreatmentDetailStep::where('treatment_detail_id',$idTreatmentDetail)->get();
        foreach ($listStep as $key ) {
            $key->stepName = $key->belongsToStep()->first();
        }
        return $listStep;
    }

    public function showTreatmentDetailStepDone($idTreatmentHistory)
    {
        $treatmentHistory = TreatmentHistory::find($idTreatmentHistory);
        $listTreatmentDetail = $treatmentHistory->hasTreatmentDetail()->get();
        $result = [];
        foreach ($listTreatmentDetail as $treatmentDetail) {
            $listTreatmentDetailStep = $treatmentDetail->hasTreatmentDetailStep()->get();
            foreach ($listTreatmentDetailStep as $treatmentDetailStep) {
                $result[] = $treatmentDetailStep->treatment_step_id;
            }
        }
        return $result;
    }

    public function viewTreatmentDetail($treatmentDetailId){
        $treatmentDetail = TreatmentDetail::find($treatmentDetailId);
        return $treatmentDetail;

    }

    public function checkDoneTreatmentHistory($idTreatmnet, $idTreatmentHistory){

    }
}