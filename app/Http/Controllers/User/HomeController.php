<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Staff;
use App\TreatmentCategory;
use Config;
use Yajra\Datatables\Facades\Datatables;
class HomeController extends Controller
{
    public function homepage(Request $request){
    	 return view('WebUser.HomePage');
    }
    public function DoctorInformation(Request $request){
    	$doctors = DB::table('tbl_staffs')->get();
    	 

    	return view("WebUser.DoctorInformation",['doctors'=>$doctors]);
    }
     public function BangGiaDichVu(){
    	return view('WebUser.ServicePrice');
    }
    public function getDB(){
    	 $ahi = 	$doctors = DB::table('tbl_treatment_categories')->get();
    	 return Datatables::of($ahi)->make(true);

    }
    public function createNews(Request $request){
        echo "string";
        exit();
    }
    public function Profile(Request $request){
        return view('WebUser.User.Profile');
    }

}