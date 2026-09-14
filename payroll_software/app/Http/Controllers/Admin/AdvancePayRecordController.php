<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


class AdvancePayRecordController extends Controller
{
  //


  // public function insertAdvancePaidRecord($employeeAutoId, $amount, $year, $month, $advancePurposeId, $createById)
  // {

  //   $insert = new AdvancePayRecord();
  //   $insert->emp_id = $employeeAutoId;
  //   $insert->adv_purpose_id = $advancePurposeId;
  //   $insert->adv_amount = $amount;
  //   $insert->month = $month;
  //   $insert->year = $year;
  //   $insert->date = Carbon::now();
  //   $insert->create_by = $createById;
  //   $insert->adv_remarks = "";
  //   $insert->created_at = Carbon::now();
  //   return $insert->save();
  // }

  // public function deleteEmployeeCashPaymentRecord($id)
  // {
  //   // dd($id);
  //   return AdvancePayRecord::where('id', $id)->delete();
  // }


  // public function updateAdvancePaidRecord($paidRecordId, $employeeAutoId, $amount, $year, $month, $advancePurposeId, $createById)
  // {
  //   $update = AdvancePayRecord::where('id', $paidRecordId)
  //     ->where('emp_id', $employeeAutoId)->where('month', $month)->where('year', $year)->update([
  //       'adv_purpose_id' => $advancePurposeId,
  //       'adv_amount' => $amount,
  //       'date' => Carbon::now(),
  //       'create_by' => $createById,
  //       'updated_at' => Carbon::now(),
  //     ]);
  // }

  // public function deleteEmployeeAdvancePaidRecord($emp_auto_id, $month, $year)
  // {
  //   AdvancePayRecord::where('emp_id', $emp_auto_id)->where('month', $month)
  //     ->where('year', $year)->delete();
  // }

  // public function getAnEmployeeAdvancePaidTotalAmount($employeeAutoId, $year, $advanceTypeId)
  // {
  //   $totalAdvanceAmount = AdvancePayRecord::where('emp_id', $employeeAutoId)
  //     //  ->where('year',$year)  //  1= Iqama advance,  2= Other Advance 
  //     ->where('adv_purpose_id', $advanceTypeId)->sum('adv_amount');
  //   if ($totalAdvanceAmount == null) {
  //     return 0;
  //   } else {
  //     return $totalAdvanceAmount;
  //   }
  // }

  // public function getThisMonthAdvancePaidRecord($employeeAutoId, $year, $month, $advanceTypeId)
  // {
  //   return  AdvancePayRecord::where('emp_id', $employeeAutoId)
  //     ->where('year', $year)->where('month', $month)
  //     ->where('adv_purpose_id', $advanceTypeId)->first();
  // }

}
