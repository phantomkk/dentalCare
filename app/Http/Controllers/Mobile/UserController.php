<?php
/**
 * Created by PhpStorm.
 * User: Luc
 * Date: 04-Jun-18
 * Time: 11:43
 */

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\BusinessFunction\PatientBusinessFunction;
use App\Http\Controllers\BusinessFunction\TreatmentBusinessFunction;
use App\Http\Controllers\BusinessFunction\UserBusinessFunction;
use App\Model\Patient;
use App\Model\User;
use App\Model\UserHasRole;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Mockery\Exception;

class UserController extends BaseController
{
    use PatientBusinessFunction;
    use UserBusinessFunction;
    use TreatmentBusinessFunction;

    public function register(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $user = $this->getUserByPhone($phone);
            if ($user == null) {
                $user = new User();
                $password = $request->input('password');
                $name = $request->input('name');
                $gender = $request->input('gender');
                $birthday = $request->input('birthday');
                $districtId = $request->input('districtId');
                $address = $request->input('address');

                $user->phone = $phone;
                $user->password = Hash::make($password);

                $patient = new Patient();
                $patient->phone = $phone;
                $patient->date_of_birth = $birthday;
                $patient->gender = $gender;
                $patient->district_id = $districtId;
                $patient->name = $name;
                $patient->avatar = "";
                $patient->address = $address;
                ////HASH
                $userHasRole = new UserHasRole();
                $userHasRole->phone = $phone;
                $userHasRole->role_id = 4;
                $userHasRole->start_time = Carbon::now();
                $this->createUserWithRole($user, $patient, $userHasRole);

                return response()->json($patient, 200);
            } else {
                $error = new \stdClass();
                $error->error = "Số điện thoại đã tồn tại";
                $error->exception = "No Exception";
                return response()->json($error, 400);
            }
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Không thể đăng kí thông tin người dùng";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }

    /**
     * @param Request $request
     * @return json
     */
    public function loginUser(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $password = $request->input('password');
            $notifToken = $request->input('noti_token');
            $result = $this->checkLogin($phone, $password);
            if ($result != null) {
                $result->noti_token = $notifToken;
                $this->updateUser($result);
                $patients = $this->getPatient($phone);
                $userResponse = new \stdClass();
                $userResponse->phone = $phone;
                $userResponse->noti_token = $notifToken;
                $userResponse->patients = $patients;

                $clientSecret = env('PASS_SECRET', false);
                Log::info("phone: " . $phone);
                $request->request->add([
                    'client_id' => '1',
                    'grant_type' => 'password',
                    'client_secret' => $clientSecret,
                    'scope' => '',
                    'username' => $phone
                ]);
//                var_dump($request->all());return;
                $tokenRequest = Request::create('/oauth/token', 'post');
                $tokenResponse = (Route::dispatch($tokenRequest));
                $tokenResponseBody = json_decode($tokenResponse->getContent());
//                var_dump($tokenResponseBody);
//                return;
                if ($tokenResponse != null) {
                    $userResponse->access_token = $tokenResponseBody->access_token;
                    $userResponse->refresh_token = $tokenResponseBody->refresh_token;
                    $userResponse->token_type = $tokenResponseBody->token_type;
                    $userResponse->expires_in = $tokenResponseBody->expires_in;
                }
                return response()->json($userResponse, 200);
            } else {
                $error = new \stdClass();
                $error->error = "Số điện thoại hoặc mật khẩu không chính xác";
                $error->exception = "No exception";
                return response()->json($error, 400);
            }
        } catch (\Exception $ex) {
            return response()->json($this->getErrorObj('Lỗi server', $ex), 400);
        }
    }

    public function searchListPhone(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
            $phones = $this->getUserPhones($keyword);
            return response()->json($phones, 200);
        } catch (Exception $ex) {
            return response()->json($this->getErrorObj('Lỗi server', $ex),400);
        }

    }

    public function logout(Request $request)
    {
        if (!$this->guard()->check()) {
            return response([
                'message' => 'No active user session was found'
            ], 404);
        }

        // Taken from: https://laracasts.com/discuss/channels/laravel/laravel-53-passport-password-grant-logout
        $request->user('api')->token()->revoke();

        Auth::guard()->logout();

//        Session::flush();
//
//        Session::regenerate();
        return response([
            'message' => 'User was logged out'
        ]);
    }

    public function testPassport()
    {
        return "TEST PASSPORT SUCCESS";
    }

    public function loginGET()
    {
        return response()->json(['hello' => 'cha co gi ca haha'], 200);
    }

    public function changePassword(Request $request)
    {
        $phone = $request->input('phone');
        $newPassword = $request->input('password');
        $currentPassword = $request->input('current_password');
        $user = $this->checkLogin($phone, $currentPassword);
        $errorResponse = new \stdClass();
        if ($user != null) {
            if ($this->changeUserPassword($phone, $newPassword)) {
                $successResponse = new \stdClass();
                $successResponse->status = "OK";
                $successResponse->code = 200;
                $successResponse->message = "Sửa mật khẩu thành công";
                $successResponse->data = null;
                return response()->json($successResponse, 200);
            } else {
                $errorResponse->error = "Không thể sửa mật khẩu";
                $errorResponse->exception = null;
                return response()->json($errorResponse, 400);
            }
        } else {
            $errorResponse->error = "Mật khẩu hiện tại không hợp lệ";
            $errorResponse->exception = null;
            return response()->json($errorResponse, 400);
        }
    }

    public function getUser()
    {
//        $u = Auth::guard('api')->user();
        $user = Auth::guard('api')->user();
        $token = $user->AauthAcessToken()->get();
//        $token = json_decode($token);
        return response()->json($token);
    }


//get function to change password quickly
    public function resetpassword($phone, $password)
    {
//        $phone = $request->get('phone');
//        $password = $request->get('password');

        $user = User::where('phone', $phone)->first();
        if (
            $user != null
        ) {
            $user->password = Hash::make($password);
            $user->save();
            return response()->json("Update Phone: " . $phone . " and password: " . $password . " Successful!");
        } else {
            return response()->json("Không tìm thấy số điện thoại " . $phone);
        }
    }


    public function changeAvatar(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $id = $request->input('id');
                $image = $request->file('image');
                $tmpPatient = $this->getPatientById($id);
                if ($tmpPatient != null) {
                    if ($this->editAvatar($image, $id)) {
                        $patient = $this->getPatientById($id);
                        $response = new \stdClass();
                        $response->status = "OK";
                        $response->message = "Chỉnh sửa avatar thành côngs";
                        $response->data = $patient->avatar;
                        return response()->json($response, 200);
                    } else {
                        $error = new \stdClass();
                        $error->error = "Có lỗi xảy ra, không thể chỉnh sửa avatar";
                        $error->exception = "Nothing";
                        return response()->json($error, 400);
                    }
                } else {
                    $error = new \stdClass();
                    $error->error = "Không thể tìm thấy bệnh nhân ";
                    $error->exception = "Nothing";
                    return response()->json($error, 400);
                }
            } else {
                $error = new \stdClass();
                $error->error = "Lỗi khi nhận hình ảnh ";
                $error->exception = "Nothing";
                return response()->json($error, 400);
            }
        } catch (\Exception $ex) {
            $error = new \stdClass();
            $error->error = "Lỗi máy chủ";
            $error->exception = $ex->getMessage();
            return response()->json($error, 400);
        }
    }

    public function sendFirebase()
    {
        try {
            $notification = new \stdClass();
            $notification->title = 'asdf';
            $notification->text = 'is is my text Tex';
            $notification->click_action = 'android.intent.action.MAIN';

            $data = new \stdClass();
            $data->keyname = 'sss';


            $requestObj = new \stdClass();
            $requestObj->notification = $notification;
            $requestObj->data = $data;
            $requestObj->to = '/topics/all';
            $client = new Client();
            $request = $client->request('POST', 'https://fcm.googleapis.com/fcm/send',
                [
                    'body' => json_encode($requestObj),
                    'Content-Type' => 'application/json',
                    'authorization' => 'key=AAAAUj5G2Bc:APA91bF8TkhDriuoevyt_I0G3G-qNniLSDdDHbULjcvsas4sHCuTKueiODRnuvVuYk6YkCHKLt3fr-Sw7UhZMzRSfmWMWzt2NZXzljYZxch39fg0v3NsBzQM5_QKUEy4bOdnnjigzaBX'
                ]
            );
//            $request->setBody($requestObj);
            $response = $request->getBody()->getContents();
            return response()->json($response);
        } catch (GuzzleException $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function updateNotifToken(Request $request)
    {
        $token = $request->input('noti_token');
        $phone = $request->input('phone');
        $user = $this->getUserByPhone($phone);
        if ($user != null) {
            $user->noti_token = $token;
            $result = $this->updateUser($user);
            if ($result) {
                return response()->json("Change firebase notification token successful", 200);

            } else {
                return response()->json("change firebase notification token error", 400);
            }
        } else {
            $error = new \stdClass();
            $error->error = "Không tìm thấy số điện thoại " . $phone;
            $error->exception = "nothing";
            return response()->json($error, 400);
        }
    }

}