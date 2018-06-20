<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 16-Jun-18
 * Time: 18:37
 */

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\BusinessFunction\HistoryTreatmentBusinessFunction;
use App\Http\Controllers\Controller;
use http\Env\Request;

class HistoryTreatmentController extends  Controller
{
    use HistoryTreatmentBusinessFunction;

    public function getByPhone($phone)
    {
        try {
            $historyTreatments = $this->getHistoryTreatmentByPhone($phone);
            return response()->json($historyTreatments, 200);
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Có lỗi xảy ra Không thể lấy dữ liệu";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }

    public function getAll()
    {
        try {
            $treatmentHistories = $this->getAllHistoryTreatments();
            return response()->json($treatmentHistories, 200);
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Có lỗi xảy ra Không thể lấy dữ liệu";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }

    public function getById(Request $request)
    {
        $id = $request->query('id');
        try {
            $historyTreatments = $this->getHistoryTreatmentById($id);
            return response()->json($historyTreatments, 200);
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Có lỗi xảy ra Không thể lấy dữ liệu";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }


}