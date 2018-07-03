<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 12-Jun-18
 * Time: 21:13
 */

namespace App\Http\Controllers\BusinessFunction;


use App\Model\Appointment;
use App\Model\Staff;
use App\Model\UserHasRole;
use App\User;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Carbon\Carbon;

trait AppointmentBussinessFunction
{


    use StaffBusinessFunction;

    public function getAllAppointment()
    {
        $appointments = Appointment::orderBy('start_time', 'desc')->get();

        return $appointments;
    }

    public function getAppointmentByPhone($phone)
    {
        $appointments = Appointment::where('phone', $phone)
            ->orderBy('start_time', 'desc')->get();
        return $appointments;
    }

    public function getAppointmentById($id)
    {
        $appointment = Appointment::where('id', $id)->first();
        return $appointment;
    }

    public function getAppointmentsByStartTime($startTime)
    {
        $appointments = Appointment::whereDate('start_time', $startTime)->get();

        return $appointments;
    }

    public function getAppointmentByDate($phone, $date)
    {
        $result = Appointment::where('phone', $phone)
            ->whereDate('start_time', $date)->get();
        return $result;
    }

    public function createAppointment($bookingDate, $phone, $note, $dentistId, $estimatedTimeStr)
    {
        try {
            $suitableDentistId = -1;
            $listDentist = $this->getAvailableDentist((new \DateTime())->format('Y-m-d'));
            $NUM_OF_DENTIST = count($listDentist);
            $this->logDebug('NUM_DENTIST' . $NUM_OF_DENTIST);
            $bookingDateNewFormat = (new \DateTime($bookingDate))->format("Y-m-d");
            $listAppointment = $this->getAppointmentsByStartTime($bookingDateNewFormat);
            $dentistObj = $this->getStaffById($dentistId);
            $this->logDebug(($dentistObj==null)? ("DENTIST OBJ ID".$dentistId.' NULL'): "DENTIST OBJ NOT NULL");
            $predictAppointmentDate = new \DateTime();
            $bookingDateObj = new \DateTime($bookingDate);
//            $appointmentArray = $listAppointment->toArray();
            $appointmentArray = $this->getListTopAppointment($listDentist, $bookingDate);
            //sort descendent
            usort($appointmentArray, array($this, "sortByTimeStamp"));
            //'if statement' return the $predictAppointmentDate and $suitableDentistId for the code below it
            if (count($appointmentArray) < $NUM_OF_DENTIST) {
                // kieu j cung co loi
                if ($dentistId == null || $dentistObj == null) {
                    $this->logDebug("INTO COUNT< NUMMOF DENTIST ___ Dentistt id = null");
                    $predictAppointmentDate = $this->addTimeToDate($bookingDateObj, "07:00:00");
                    $listFreeDentists = $this->getFreeDentistsAtDate($listDentist, $bookingDateNewFormat);
                    $randomDentist = $this->getRandomDentist($listFreeDentists);
                    $suitableDentistId = $randomDentist->id;
                } else {///neu nguoi dat la bac si
                    $this->logDebug("INTO COUNT< NUMMOF DENTIST ___ Dentistt id != null");
                    $suitableDentistId = $dentistId;
                    $dentistAppointment = null;
                    //lay ra lich cuoi cung cua bac si, vi lich nay chi co 1 hang nen trich tu list ra luon
                    $dentistAppointment = $this->getLastestAppointment($bookingDate, $dentistId);
                    if ($dentistAppointment == null) {
                        $predictAppointmentDate = $this->addTimeToDate($bookingDateObj, "07:00:00");
                    } else {
                        $predictAppointmentDate = $this->getNextStartTime($dentistAppointment);
                    }
                }
            } else {
                if ($dentistId == null || $dentistObj == null) {
                    $this->logDebug("INTO COUNT >= NUMMOF DENTIST ___ Dentistt id == null");
//                    $topElement = $this->getAppointmentOnTop($appointmentArray, $NUM_OF_DENTIST);
                    $equallyAppointment = [];
                    $equallyAppointment[] = $appointmentArray[0];
                    $this->arrangeEquallyAppointment($equallyAppointment, $appointmentArray, 1);
                    if (count($equallyAppointment) > 1) {
                        $this->logDebug("INTO COUNT EQUALLY > 1");
                        $appointment = $this->getRandomAppointment($equallyAppointment);
                        $this->logDebug('size: ' . count($appointmentArray));
                        $this->logDebug('size: ' . count($equallyAppointment));
                        $predictAppointmentDate = $this->getNextStartTime($appointment);
                        $suitableDentistId = $appointment['staff_id'];
                    } else {
                        $this->logDebug("INTO COUNT EQUALLY == 1");
                        $maxdate = new \DateTime("2035-12-12");
                        $minTimeStamp = $maxdate->getTimestamp() + $maxdate->getTimestamp();
                        $minAppointment = array();
                        foreach ($appointmentArray as $item) {
                            $appointmentTimeStamp = $this->getAppointmentTimeStamp($item);
                            if ($appointmentTimeStamp < $minTimeStamp) {
                                $minTimeStamp = $appointmentTimeStamp;
                                $minAppointment = $item;
                            }
                        }
                        // $predictAppointmentDate= the finish datetime of the previous patient;
                        $minAppointmentStartDateTime = new \DateTime($minAppointment['start_time']);
                        $predictAppointmentDate = $this->addTimeToDate($minAppointmentStartDateTime,
                            $minAppointment['estimated_time']);
                        $suitableDentistId = $minAppointment['staff_id'];
                    }
                } else {
                    $this->logDebug("INTO COUNT >= NUMMOF DENTIST ___ Dentistt id != null");
                    $suitableDentistId = $dentistId;
                    $dentistAppointment = $this->getLastestAppointment($bookingDate, $dentistId);
                    $predictAppointmentDate = $this->getNextStartTime($dentistAppointment);
                }
                //if the predict time is in lunch break, add it to the afternoon start at 13h
            }
            ////////////////////////////VALIDATE START_TIME - variable: $predictAppointmentDate //////////////////////////////
            $estimatedTimeObj = new \DateTime("00:30");
            if ($estimatedTimeStr != null) {
                $estimatedTimeObj = new DateTime($estimatedTimeStr);
            }
            $tmpPredictTime = clone $predictAppointmentDate;
            $currentDateTime = new DateTime();
            //process when patient book appointment at the same day, and
            $diffDate = ($currentDateTime->diff($predictAppointmentDate));
            if (($currentDateTime->getTimestamp() - $predictAppointmentDate->getTimeStamp()) > 0) {
                $predictAppointmentDate = $this->addTimeToDate($currentDateTime, '00:10:00');
                $arrayFreeDentist = $this->getFreeDentistsFromTime($listDentist, $currentDateTime, $currentDateTime);
                $randomDentist = $this->getRandomDentist($arrayFreeDentist);
                $suitableDentistId = $randomDentist['id'];
                $this->logDebug("ZZZ");
            }
            $endAppointmentTime = $this->addTimeToDate($tmpPredictTime, $estimatedTimeObj->format("H:i:s"));
            if ($this->isInLunchBreak($endAppointmentTime)) {
                $this->logDebug("IS in lunch");
                $predictAppointmentDate = new \DateTime($bookingDateNewFormat . ' 13:00:00');
            } else if ($this->isEndOfTheDay($predictAppointmentDate)) {
                $this->logDebug("isEndOfTheDay");
                throw new \Exception ('isEndOfTheDay');
            }
            $numericalOrder = $listAppointment->count() + 1;
            $appointment = new Appointment();
            $appointment->phone = $phone;
            $appointment->note = $note;
            $appointment->estimated_time = $estimatedTimeObj->format("H:i:s");
            $appointment->start_time = $predictAppointmentDate->format("Y-m-d H:i:s");
            $appointment->numerical_order = $numericalOrder;
            $appointment->staff_id = $suitableDentistId;
            $appointment->save();
            return $appointment;
        } catch (Exception $exception) {
            $exception->getTrace();
            return null;
        }
    }

    public function getListTopAppointment($listAvailableDentist, $dateStr)
    {
        $appoinements = [];
        foreach ($listAvailableDentist as $dentist) {
            $tmp = $this->getLastestAppointment($dateStr, $dentist->id);
            if ($tmp != null) {
                $appoinements[] = $tmp;
            }
        }
        return $appoinements;
    }

    private function getLastestAppointment($dateStr, $dentistId)
    {
        $appointment = Appointment::where('staff_id', $dentistId)
            ->whereDate('start_time', $dateStr)
            ->orderBy('start_time', 'desc')
            ->first();
        return $appointment;
    }

    private function getNextStartTime($appointment)
    {
        if ($appointment == null) {
            return null;
        }
        $startTime = new \DateTime($appointment['start_time']);
        $estimatedTime = ($appointment['estimated_time']);
        return $this->addTimeToDate($startTime, $estimatedTime);
    }

    private function arrangeEquallyAppointment(&$equallyAppointment, $arrayAppointment, $index)
    {
        if ($index == count($arrayAppointment)) {
            return;
        }
        $currentAppointment = $arrayAppointment[$index - 1];
        $nextAppointment = $arrayAppointment[$index];
        $currentTimeStamp = $this->getAppointmentTimeStamp($currentAppointment);
        $nextTimeStamp = $this->getAppointmentTimeStamp($nextAppointment);

        if ($currentTimeStamp == $nextTimeStamp) {
            $equallyAppointment[] = $arrayAppointment[$index];
            $this->arrangeEquallyAppointment($equallyAppointment, $arrayAppointment, $index + 1);
        }
    }

    private function logDebug($message)
    {
        Log::info("LOG_DEBUG_Appointment: " . $message);
    }

    private function isInLunchBreak($appointmentEndDateTime)
    {
        $time = $appointmentEndDateTime->format('H:i:s');
        if ((strtotime($time) > strtotime('12:00:00'))
            && (strtotime($time) < strtotime('13:00:00'))) {
            return true;
        }
        return false;
    }

    public function isEndOfTheDay($appointmentEndDateTime)
    {
        $time = $appointmentEndDateTime->format('H:i:s');
        if ((strtotime($time) > strtotime('19:00:00'))) {
            return true;
        }
        return false;
    }

    /**
     * @param $time
     * @return array dentist id int[]
     */
    public function getFreeDentistsFromTime($listAvailableDentist, $atDate, $fromTime)
    {

        $appointment = new Appointment();
        $appointments = $appointment
            ->whereDate(
                'start_time', '=', $atDate->format("Y-m-d"))
            ->where(
                'start_time', '>=', $fromTime->format("Y-m-d H:i:s")
            )
            ->get();
        if ($appointments->count() == 0) {
            return $listAvailableDentist;
        } else {
            $dentistArray = [];
            foreach ($listAvailableDentist as $dentist) {
                if (!$this->isDentistBusy($appointments, $dentist->id)) {
                    $dentistArray[] = $dentist;
                }
            }
            return $dentistArray;
        }
    }

    private function getFreeDentistsAtDate($listAvailableDentists, $date)
    {
        //get all dentist that works at that day
//        $listAvailableDentists = $this->getAvailableDentist($date);
        $listFreeDentists = [];
        $appointments = $this->getAppointmentsByStartTime($date);
        //find dentist that doesn't treat for patient at the first of the day
        foreach ($listAvailableDentists as $availableDentist) {
            if (!($this->isDentistBusy($appointments, $availableDentist->id))) {
                $listFreeDentists[] = $availableDentist;
            }
        }
        return $listFreeDentists;
    }

    private function isDentistBusy($appointments, $dentistId)
    {
        foreach ($appointments as $appointment) {
            if ($appointment->staff_id == $dentistId) {
                return true;
            }
        }
        return false;
    }

    private function getRandomDentist($arrayDentist)
    {
        $sizeList = sizeof($arrayDentist);
//        $this->logDebug($arrayDentist);
        $index = rand(0, $sizeList - 1);
        $randomDentist = $arrayDentist[$index];
        return $randomDentist;
    }

    private function getRandomAppointment($arrayAppointment)
    {
        $sizeList = sizeof($arrayAppointment);
        $index = rand(0, $sizeList - 1);
        $randomAppointment = $arrayAppointment[$index];
        return $randomAppointment;
    }

    public function getAvailableDentist($date)
    {
        $roleDentist = 2;
        $dentists = UserHasRole::where('role_id', $roleDentist)->get();
        $totalDentists = [];
        foreach ($dentists as $d) {
            $user = $d->belongsToUser()->first();
            if ($user != null) {
                $totalDentists[] = $user->belongToStaff()->first();
            }
        }
        $availableDentist = [];
        foreach ($totalDentists as $dentist) {
            $dentistRequestAbsent = $dentist->hasAbsent()->get();
            if ($dentistRequestAbsent->count() == 0) {
                $availableDentist[] = $dentist;
            } else if (!$this->isDentistAbsent($dentistRequestAbsent, $date)) {
                $availableDentist[] = $dentist;
            }
        }
        return $availableDentist;
    }

    private function isDentistAbsent($dentistRequestAbsent, $date)
    {
        foreach ($dentistRequestAbsent as $requestAbsent) {
            $approveAbsentRecord = $requestAbsent->hasAbsent()->first();
            if (strtotime($requestAbsent->start_date) <= strtotime($date)
                && strtotime($requestAbsent->end_date) >= strtotime($date)
                && $approveAbsentRecord != null) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $date
     * @param $time in string
     * @return mixed
     * @throws \Exception
     */
    private function addTimeToDate($date, $time)
    {
        $intervalTime = new \DateInterval('P0000-00-00T' . $time);
        $date->add($intervalTime);
        return $date;
    }

    private function sortByTimeStamp($appointment1, $appointment2)
    {
        $timestampAP1 = $this->getAppointmentTimeStamp($appointment1);
        $timestampAP2 = $this->getAppointmentTimeStamp($appointment2);
        return $timestampAP1 < $timestampAP2;
    }

    /**
     * @param $appointment
     * @return total timestamp of booking date and estimate time
     */
    private function getAppointmentTimeStamp($appointment)
    {
        $dateTimeAppointment = new \DateTime($appointment['start_time']);
        $timeAppointment = new \DateTime($appointment['estimated_time']);
        $timestampAP = $dateTimeAppointment->getTimestamp() + $timeAppointment->getTimestamp();
        return $timestampAP;
    }

    public function getAppointmentOfUser($phone)
    {
        $listAppointment = User::where('phone', $phone)->first()->hasAppointment()->get();
        $max = 0;
        $result = false;
        foreach ($listAppointment as $appointment) {
            $dateTimeAppointment = new \DateTime($appointment['start_time']);
            if ($max < $dateTimeAppointment->getTimestamp()) {
                $result = $appointment;
            }
        }
        return $result;
    }

    public function saveAppointment($appointment)
    {
        DB::beginTransaction();
        try {
            $appointment->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function checkAppointmentForPatient($phone)
    {
        return Appointment::where('phone', $phone)
            ->whereDate('start_time', Carbon::now()->format('Y-m-d'))
            ->where('is_coming', false)
            ->first();
    }

    public function viewAppointmentForDentist($dentist_id)
    {
        return Appointment::where('staff_id', $dentist_id)
            ->where('start_time', '>=', Carbon::now()->format('Y-m-d'))
            ->get();
    }
    public function viewAppointmentForReception(){
        return Appointment::where('start_time', '>=', Carbon::now()->format('Y-m-d'))
            ->get();
    }
}