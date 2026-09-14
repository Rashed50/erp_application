<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeMultiProjectWorkHistory;
use Carbon\Carbon;

class EmployeeMultiProjectWorkHistoryController extends Controller
{

    // All Employee Last Two Month Work Record 
    public function getLastTwoMonthEmployeeMultiprojectWorkRecord($month, $year)
    {
        $previousMonth = $month;
        if ($month == 1) {
            $previousMonth = 12;
        } else {
            $previousMonth = $month - 1;
        }

        return $getAll = EmployeeMultiProjectWorkHistory::with('projectName', 'employee')->where('month',  $previousMonth)->orWhere('month', 2)->where('year', $year)->get();
    }

    public function searchAnEmployeeMultiprojectWorkRecord($emp_auto_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('emp_id', $emp_auto_id)->where('month', $month)
            ->where('year', $year)->get();
    }
    public function searchEmployeeMultiprojectWorkRecord($month, $year)
    {
        return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('month', $month)->where('year', $year)->get();
    }

    public function getListOfEmployeeAutoIdExistInMultiProectWorkRecord($month, $year)
    {
        return EmployeeMultiProjectWorkHistory::select('emp_id')
            ->where('month', $month)->where('year', $year)->get();
    }



    public function deleteAnEmpMultiprojectWorkHistory($empwh_auto_id)
    {

        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->delete();
    }

    public function deleteMonthAndYearWiseAnEmpMultiprojectWorkHistory($emp_auto_id, $month, $year)
    {

        return EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->delete();
    }

    public function countNoOfProjectWorkThisMonth($emp_auto_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->count();
    }

    public function findIdWiseAnEmpMultiprojectWorkHistory($empwh_auto_id)
    {
        // return "okk";
        return EmployeeMultiProjectWorkHistory::with('projectName', 'employee')->where('empwh_auto_id', $empwh_auto_id)->first();
    }


    public function storeUpdate($empId, $month, $year, $project)
    {

        /*
      $proj_exit = EmployeeMultiProjectWorkHistory::where('emp_id',$empId)->where('month',$month)->where('year',$year)->where('project_id',$project)->first();

      if ($proj_exit!=null){

          $update = EmployeeMultiProjectWorkHistory::where('emp_id',$empId)->where('month',$month)->where('year',$year)->where('project_id',$project)->update([
              // 'emp_id' => $empId,
              // 'project_id' => $project,
              // 'month' => $month,
              // 'year' => $year,
              'total_day' => 10,
              'total_hour' => 10,
              'updated_at'=> Carbon::now('Asia/Dhaka')->toDateTimeString()
          ]);
      }else {
        $insert = EmployeeMultiProjectWorkHistory::insert([
            'emp_id' => $empId,
            'project_id' => $project,
            'month' => $month,
            'year' => $year,
            'total_day' => 1,
            'total_hour' => 1,
            'created_at'=> Carbon::now('Asia/Dhaka')->toDateTimeString()
        ]);
     }
    */
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
            'project_id' => 'required',
            'month' => 'required',
            'year' => 'required',
            'total_day' => 'required',
            'total_hour' => 'required',
        ], []);


        $proj_exit = EmployeeMultiProjectWorkHistory::where('emp_id', $request->emp_id)
            ->where('year', $request->year)->where('month', $request->month)
            ->where('project_id', $request->project_id)->count();


        if ($proj_exit != null) {
            //  dd('Boos ai data already exit');

            $update = EmployeeMultiProjectWorkHistory::where('emp_id', $request->emp_id)
                ->where('year', $request->year)->where('month', $request->month)
                ->where('project_id', $request->project_id)->update([
                    'emp_id' => $request['emp_id'],
                    'project_id' => $request['project_id'],
                    'month' => $request['month'],
                    'year' => $request['year'],
                    'total_day' => $request['total_day'],
                    'total_hour' => $request['total_hour'],
                    'updated_at' => Carbon::now('Asia/Dhaka')->toDateTimeString()
                ]);
        } else {
            $insert = EmployeeMultiProjectWorkHistory::insert([
                'emp_id' => $request['emp_id'],
                'project_id' => $request['project_id'],
                'month' => $request['month'],
                'year' => $request['year'],
                'total_day' => $request['total_day'],
                'total_hour' => $request['total_hour'],
                'creadte_at' => Carbon::now('Asia/Dhaka')->toDateTimeString()
            ]);
        }
    }



    public function getIsAnEmployeeMultiprojectWorkHistory($emp_auto_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->count() > 1 ? true : false;
    }
    //     public function getIsEmployeeWorkMultipleProjectInThisMonth($empAutoId ,$month,$year,$projectId){
    //         return $proj_exit = EmployeeMultiProjectWorkHistory::where('emp_id', $empAutoId)
    //           ->where('month', $month)
    //           ->where('year', $year)
    //           ->where('project_id', $projectId)
    //           ->first();
    //    }





}
