<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 02-Jul-18
 * Time: 17:38
 */

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\BusinessFunction\AppointmentBussinessFunction;
use App\Http\Controllers\BusinessFunction\PatientBusinessFunction;
use App\Http\Controllers\BusinessFunction\RequestAbsentBusinessFunction;
use App\Http\Controllers\BusinessFunction\UserBusinessFunction;
use App\Model\Patient;
use Exception;
use Illuminate\Http\Request;

class PatientController extends BaseController
{
    use UserBusinessFunction;
    use PatientBusinessFunction;
    use AppointmentBussinessFunction;
    public function updatePatientInfo(Request $request)
    {
        try {
            $patientId = $request->input('id');
            $name = $request->input('name');
            $gender = $request->input('gender');
            $birthday = $request->input('date_of_birth');
            $address = $request->input('address');
            $districtId = $request->input('district_id');
            $patient = $this->getPatientById($patientId);
            if ($patient != null) {
                $patient->name = $name;
                $patient->gender = $gender;
                $patient->date_of_birth = $birthday;
                $patient->address = $address;
                $patient->district_id = $districtId;
                $result = $this->updatePatient($patient);
                if ($result == true) {
                    $successResponse = new \stdClass();
                    $successResponse->status = "OK";
                    $successResponse->code = 200;
                    $successResponse->message = "Sửa tài khoản thành công";
                    $successResponse->data = $patient;
                    return response()->json($successResponse, 200);
                } else {
                    $error = new \stdClass();
                    $error->error = "Không thể sửa đổi thông tin người dùng";
                    $error->exeption = null;
                    return response()->json($error, 400);
                }
            } else {
                $error = new \stdClass();
                $error->error = "Không thể tìm thấy id bệnh nhân";
                $error->exeption = null;
                return response()->json($error, 400);
            }
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Lỗi máy chủ";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }

    public function getByPhone(Request $request)
    {
        try {
            $phone = $request->input("phone");
            $user = $this->getUserByPhone($phone);
            if($user==null){
                $error = $this->getErrorObj("Số điện thoại chưa được đăng kí", "No exception");
                return response()->json($error, 400);
            }
            $patients = $this->getPatientByPhone($phone);
            return $patients;
        }catch (Exception $ex){
            $error = $this->getErrorObj("Lỗi server", $ex);
            return response()->json($error, 500);
        }
    }

    public function getPatientAppointmentByDate(Request $request)
    {
        $dateStr = $request->input('date');
        $phone = $request->input('phone');
        try {
            $appointments = $this->getAppointmentByDate($phone, $dateStr);
            foreach ($appointments as $appointment) {
                $appointment->patient = $appointment->hasPatientOfAppointment()->first();
            }
            return response()->json($appointments);
        }catch (Exception $ex){
            $error = $this->getErrorObj("Lỗi máy chủ", $ex);
            return response()->json($error, 500);
        }
    }

}