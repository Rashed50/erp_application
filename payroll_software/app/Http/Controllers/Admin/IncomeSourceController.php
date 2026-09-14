<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Http\Request;
use App\Models\IncomeSource;
use App\Models\ProjectInfo;
use App\Models\EmployeeInfo;
use App\Models\IncomeType;
use App\Models\InvoiceRecordDetails;
use App\Models\InvResDetails;
use App\Models\Month;
use Carbon\Carbon;
use Session;
use Auth;
use Image;

class IncomeSourceController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */

  public function getAll()
  {
    return $all = IncomeSource::get();
  }

  public function getPending()
  {
    $inStatus = 0;
    return $all = IncomeSource::where('status', $inStatus)->get();
  }

  public function findId($id)
  {
    return $find = IncomeSource::where('inc_id', $id)->first();
  }

  public function getProjectInfo()
  {
    return $all = ProjectInfo::get();
  }

  public function getEmployee()
  {
    return $all = (new EmployeeDataService())->getAllEmployeesInformation(-1,1);
    // EmployeeInfo::get();
  }

  public function incomeType()
  {
    return $income = IncomeType::get();
  }

  public function insert(Request $req)
  {
    /* form validation */
    $this->validate($req, [], []);

    /* data insert in database */
    // dd($req->all());
    $total_with_vat = ($req->total_amount + $req->vat);
    $net_amount = ($total_with_vat - $req->debit_amount);
    $entered_id = Auth::user()->id;

    $pending = $req->pending_amount;


    $insert = IncomeSource::insert([
      'invoice_no' => $req->invoice_no,
      'total_amount' => $req->total_amount,
      'vat' => $req->vat,
      'total_with_vat' => $total_with_vat,
      'debit_amount' => $req->debit_amount,
      'invoice_status' => $req->invoice_status,
      'net_amount' => $net_amount,
      'remarks' => $req->remarks,
      'project_id' => $req->project_id,
      'submitted_by_id' => $req->employee_id,
      'submitted_date' => $req->submitted_date,
      'description' => $req->description,
      'create_by_id' => $entered_id,
      'created_at' => Carbon::now(),
    ]);

    if ($insert) {
      Session::flash('success', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  public function update(Request $req)
  {
    /* form validation */
    $this->validate($req, [], []);

    /* data insert in database */
    $entered_id = Auth::user()->id;
    $id = $req->id;
    $total_with_vat = ($req->total_amount + $req->vat);
    $net_amount = ($total_with_vat - $req->debit_amount);
    $entered_id = Auth::user()->id;
    $pending = $req->invoice_status;

    /* update data in database */
    $update = IncomeSource::where('inc_id', $id)->update([
      'invoice_no' => $req->invoice_no,
      'total_amount' => $req->total_amount,
      'vat' => $req->vat,
      'total_with_vat' => $total_with_vat,
      'debit_amount' => $req->debit_amount,
      'invoice_status' => $req->invoice_status,
      'net_amount' => $net_amount,
      'remarks' => $req->remarks,
      'project_id' => $req->project_id,
      'submitted_by_id' => $req->employee_id,
      'submitted_date' => $req->submitted_date,
      'description' => $req->description,
      'create_by_id' => $entered_id,
      'created_at' => Carbon::now(),
    ]);

    if ($update) {
      Session::flash('success_update', 'value');
      return Redirect()->route('income-source');
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  public function approve($inc_id)
  {
    $entered_id = Auth::user()->id;
    $update = IncomeSource::where('inc_id', $inc_id)->where('status', 0)->update([
      'create_by_id' => $entered_id,
      'status' => 1,
      'updated_at' => Carbon::now(),
    ]);
    if ($update) {
      Session::flash('success', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  public function delete($inc_id)
  {
  }

  public function saveNewInvoice($invoiceNo, $invoiceStatus, $remakrs, $projectId, $submitedById, $submitDate, $description, $insertById, $retension, $vat, $totalAmnt, $totalWithVat, $netAmnt)
  {



    return  $insert = IncomeSource::insertGetId([
      'invoice_no' => $invoiceNo,
      'total_amount' => $totalAmnt,
      'vat' => $vat,
      'total_with_vat' => $totalWithVat,
      'debit_amount' => $retension,
      'invoice_status' => $invoiceStatus,
      'net_amount' => $netAmnt,
      'remarks' => $remakrs,
      'project_id' => $projectId,
      'submitted_by_id' => $submitedById,
      'submitted_date' => $submitDate,
      'description' => $description,
      'create_by_id' => $insertById,
      'created_at' => Carbon::now(),
    ]);
  }


  /*
  |--------------------------------------------------------------------------
  |  Report Income Expense
  |--------------------------------------------------------------------------
  */

  public function incomeExpenseReportUI()
  {
    $months = (new CompanyDataService())->getAllMonth();
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    return view('admin.report.income-expense.income_expense_report_ui', compact('months', 'projects'));
  }

  public function incomeExpenseReportProcess(Request $request)
  {
    return view('admin.report.income-expense.income_expense_report');
  }



  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index()
  {
    $all = $this->getAll();
    $project = $this->getProjectInfo();
    $employee = $this->getEmployee();
    $income = $this->incomeType();
    return view('admin.income.add', compact('all', 'project', 'employee', 'income'));
  }

  public function list()
  {
    $all = $this->getPending();
    return view('admin.income.list', compact('all'));
  }

  public function edit($id)
  {
    $edit = $this->findId($id);
    $project = $this->getProjectInfo();
    $employee = $this->getEmployee();
    $income = $this->incomeType();
    return view('admin.income.edit', compact('edit', 'project', 'employee', 'income'));
  }



  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */
}
