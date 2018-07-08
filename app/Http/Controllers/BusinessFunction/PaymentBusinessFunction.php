<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 17-Jun-18
 * Time: 23:01
 */

namespace App\Http\Controllers\BusinessFunction;


use App\Model\Payment;
use App\Model\PaymentDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

trait PaymentBusinessFunction
{
    public function getPaymentByPhone($phone)
    {
        $payments = Payment::where('phone', $phone)->get();
        foreach ($payments as $item) {
            $listPaymentDetail = $item->hasPaymentDetail()->get();
            foreach ($listPaymentDetail as $paymentDetail) {
                $paymentDetail->staff = $paymentDetail->beLongsToStaff()->first();
            }
            $treatmentNames = [];
            $listTreatmentHistories = $item->hasManyTreatmentHistory()->get();
            foreach ($listTreatmentHistories as $treatmentHistory) {
                if ($treatmentHistory->belongsToTreatment() != null) {
                    $treatmentNames[] = $treatmentHistory->belongsToTreatment()->first()->name;
                }
            }
            $item->payment_details = $listPaymentDetail;
            $item->treatment_names = $treatmentNames;
        }
        return $payments;
    }

    public function getListPayment()
    {
        $payments = Payment::all();
        return $payments;
    }

    public function getPaymentById($id)
    {
        $payment = Payment::where('id', $id)->first();
        return $payment;
    }

    public function createPayment($total_price, $phone)
    {
        DB::beginTransaction();
        try {
            $id = Payment::create([
                'total_price' => $total_price,
                'phone' => $phone,
            ])->id;
            DB::commit();
            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function checkPaymentIsDone($phone)
    {
        $listPayment = Payment::where('phone', $phone)->get();
        foreach ($listPayment as $payment) {
            if ($payment->is_done == false) {
                return $payment;
            }
        }
        dd("Null");
        return null;
    }

    public function updatePayment($price, $idPayment)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::find($idPayment);
            $payment->total_price = $payment->total_price + $price;
            if ($payment->total_price == 0) {
                $payment->is_done = true;
            }
            $payment->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updatePaymentModel($payment, $paymentDetail)
    {
        DB::beginTransaction();
        try {
            $payment->save();
            $paymentDetail->save();
            return true;
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            throw  new Exception($exception->getMessage());
        }

    }

    public function updatePaymentPaid($price, $idPayment)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::find($idPayment);
            $payment->paid = $payment->paid + $price;
            if ($payment->total_price == $payment->prepaid) {
                $payment->is_done = true;
            }
            $payment->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    public function createPaymentDetail($paymentDetail)
    {
        DB::beginTransaction();
        try {
            $paymentDetail->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }
}