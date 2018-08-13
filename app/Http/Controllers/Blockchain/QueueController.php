<?php

namespace App\Http\Controllers\Blockchain;

use App\Http\Controllers\BusinessFunction\NodeInfoBusinessFunction;
use App\Http\Controllers\BusinessFunction\QueueBusinessFunction;
use App\Jobs\BlockchainQueue;
use App\Jobs\SendSmsJob;
use App\Model\Blockchain;
use App\Model\NodeInfo;
use App\Model\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use function MongoDB\BSON\toJSON;
use Spatie\Async\Pool;

class QueueController extends Controller
{

    use QueueBusinessFunction;

    public function addToQueue(Request $request)
    {
        $dataEncrypt = $request->data_encrypt;
        $status = 1; // 1 là waiting, 2 là  done
        $checkExistIp = $this->isExist($this->clientIp);
        if ($checkExistIp) {
            return $this->createNewRecordInQueue($dataEncrypt, $status, $this->clientIp);
        }
        Log::info('QueueController_addToQueue_ClientIpNotInNetwork: ' . $this->clientIp);
        return 'fail';
    }

    public function checkStatusOfRecord(Request $request)
    {
        $id = $request->id;
        return $this->checkStatus($id);
    }


    public function runThreadQueue(Request $request)
    {
        $dataEncrypt = $request->data_encrypt;
        $obj = new ClassCheckingStatus($dataEncrypt);
        $func = array($obj, 'checkingStatusContinously');
        BlockchainQueue::dispatch($func);
        return 'success';
    }

    public function updateQueue(Request $request)
    {
        if ($this->isExist($this->clientIp)) {
            $id = $request->id;
            $result = $this->updateRecordById($id);
            if ($result) {
                return 'success';
            } else {
                Log::info('QueueController_UpdateRecordById_ErrorInProcess ' . $this->clientIp);
                return 'fail';
            }
        }
        return 'fail';
    }

    public function updateAll(Request $request)
    {
        if ($this->isExist($this->clientIp)) {
            $id = $request->id;
            $successfullResult = $this->updateAllQueue($id);
            return json_encode($successfullResult);
        }
        Log::info('QueueController_updateAll_ClientIpNotInNetwork: ' . $this->clientIp);
        return '0';
    }

    public
    function checkExist(Request $request)
    {
        return json_encode($this->isExist($request->ip));
    }


}
