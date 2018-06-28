<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 17-Jun-18
 * Time: 23:01
 */

namespace App\Http\Controllers\BusinessFunction;


use App\Model\Payment;

trait PaymentBusinessFunction
{
    public function getPaymentByPhone($phone)
    {
        $payments = Payment::where('phone', $phone)->get();
        foreach ($payments as $item) {
            $listPaymentDetail = $item->hasPaymentDetail()->get();
//            dd($listPaymentDetail);
            foreach ($listPaymentDetail as $paymentDetail) {
                $paymentDetail->receptionist = $paymentDetail->beLongsToStaff()->first();
            }
            $item->payment_details = $listPaymentDetail;
        }
        return $payments;
    }

    public function getAllPayment()
    {
        $payments = Payment::all();
        return $payments;
    }

    public function createPayment($total_price, $phone)
    {
        DB::beginTransaction();
        try {
            $id= Payment::create([
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

    public function checkPaymentIsDone($phone){
        $listPayment = Payment::where('phone', $phone)->get();
        foreach ($listPayment as $payment){
            if($payment->is_done == false){
                return $payment;
            }
        }
        return false;
    }

    public function updatePayment($price, $payment){
        DB::beginTransaction();
        try {
            $payment->price = $payment->price + $price;
            $payment->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }

    }
}