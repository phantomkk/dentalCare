<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BusinessFunction\MedicineBusinessFunction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Medicine;
use DB;
use Yajra\Datatables\Facades\Datatables;

class MedicineController extends Controller
{
    use MedicineBusinessFunction;

    public function createForTreatmentDetail(Request $request)
    {
        $listMedicine = $request->medicine;
        $listQuantity = $request->quantity;
        $treatment_detail_id = $request->treatment_detail_id;
        $this->createMedicineForTreatmentDetail($listMedicine, $treatment_detail_id, $listQuantity);
    }

    public function loadOfTreatmentdetail(Request $request)
    {
        $treatment_detail_id = $request->treatment_detail_id;
        $listMedicineQuantity = $this->loadMedicineOfTreatmentDetail($treatment_detail_id);
        $listMedicine = [];
        $listQuantity = [];
        foreach ($listMedicineQuantity as $medicineQuantity) {
            $listMedicine[] = $medicineQuantity->belongsToMedicine()->first();
            $listQuantity[] = $medicineQuantity->quantity;
        }
    }

    public function createPrescription()
    {
        return view('admin.medicines.createPrescription');
    }

    public function ajaxSearch($medicine)
    {
        $output = '';

        $data = $this->getMedicineByName($medicine);


        $total_row = $data->count();


        if ($total_row > 0) {
            foreach ($data as $row) {
                $tmp = "'".$row->name."'";
                $output .= '
        <tr>
         <td>' . $row->name . '</td>
         <td>' . $row->use . '</td>
         <td><button type="button" class="btn btn-default btn-success"
                                        style="margin-right: 10px;float: right;" onclick="addToPrescription('.$tmp.','.$row->id.')">Thêm vào đơn thuốc
                                </button></td>
        </tr>
        ';
            }

        }
        if ($total_row == 0) {
            $output = '
       <tr>
        <td align="center" colspan="5">Không có thuốc này trong danh sách</td>
       </tr>
       ';
        }


        $data = array(
            'table_data' => $output,
            'total_data' => $total_row
        );

        echo json_encode($data);
    }

    public function createPrescriptionForTreatmentDetail(Request $request){
        $this->createMedicineForTreatmentDetail($request->medicine, 1, $request->quantity);
    }

}
