@extends('layouts.admin-master')
@section('title')Transaction @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Daily Transaction</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Daily Transanction</li>
        </ol>
    </div>
</div>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
</div>


<!-- Transaction Menu Section -->
<div class="row" id="">
    <div class="col-md-12">
             <div class="card">
                <div class="card-body card_form justify-content-center">
                    <div class="row justify-content-center">

                         <div class="col-sm-2" style="overflow:hidden">
                            @can('debit_invoice_add')
                                    <button type="button" id="invoice_report" data-toggle="modal" data-target="#daily_expense_form_modal" class="btn btn-primary waves-effect">New Expense</button>
                            @endcan
                           &nbsp; &nbsp;
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-1" style="overflow:hidden">
                            @can('debit_invoice_search')
                                <button type="button" onclick="showSearchingForm()" class="btn btn-primary waves-effect">Search</button>
                            @endcan
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-2" style="overflow:hidden">
                            @can('credit_invoice_add')
                                <button type="button"   data-toggle="modal" data-target="#cash_received_form_modal" class="btn btn-primary waves-effect">Cash Received</button>
                            @endcan
                        </div>
                        <div class="col-sm-1"> </div>

                        <div class="col-sm-2" style="overflow:hidden">
                             @can('debit_invoice_daily_report')
                             <button type="button" onclick="showReportProcessingSection()" class="btn btn-primary waves-effect">Report</button>
                            @endcan
                        </div>
                        {{-- <div class="col-sm-1">  </div> --}}
                        <div class="col-sm-2" style="overflow:hidden">
                            <button type="button" onclick="openPDFMergerWebsite()" class="btn btn-primary waves-effect">PDF Merger</button>
                         </div>
                    </div>
                </div>
            </div>
     </div>
</div>

<!-- Daily Expense Modal !-->
<div class="modal fade" id="daily_expense_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center">Daily Expense Insertion Form </h5>
                <hr>
                 <form class="form-horizontal" id="daily_expense_form" enctype="multipart/form-data" action="{{route('company.daily.transaction.expesne.store')}}" method="POST" >
                 @csrf

                        <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Tran.Type <span class="req_star">*</span> </label>
                                <div class="col-sm-8">
                                    <select class="form-select" name="credit_account_id" id="credit_account_id"  required>
                                        <option value="">Please Select One </option>
                                        <option value="205">Cash Flow Transaction </option>
                                        <option value="200">Other Transaction </option>
                                    </select>

                                </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                            <div class="col-sm-8">
                                <input type="date" id="expense_date" name="expense_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                             </div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('expense_type') ? ' has-error' : '' }}">
                            {{-- <input type="hidden" class="form-control" name="request_type" value="2">                                                          --}}
                            <input type="hidden"  id ="dr_vou_auto_id"  name="dr_vou_auto_id"  >

                            <label class="col-sm-4 control-label">Expense Type <span class="req_star">*</span> </label>
                            <div class="col-sm-8">
                                <select class="form-select" name="expense_type" id="expense_type"  required>
                                    <option value="">Please Select One </option>
                                    @foreach($expense_types as $r)
                                    <option value="{{ $r->cost_type_id }}">{{$r->cost_type_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('expense_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expense_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">


                            <label class="col-sm-4 control-label">Employee ID</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="employee_id" id="employee_id"   placeholder="Enter Employee ID Here...">
                            </div>
                        </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Project </label>
                        <div class="col-sm-8">
                            <select class="form-select" name="project_id" id="project_id">
                                <option value="">Select Project Name </option>
                                @foreach($projects as $pr)
                                <option value="{{ $pr->proj_id }}">{{$pr->proj_name}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Payment Method </label>
                            <div class="col-sm-8">
                                <select class="form-select" name="expense_method" id="expense_method"  required>
                                       <option value="">Select One</option>
                                       <option value="CASH">CASH</option>
                                       <option value="BANK">BANK</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount Here..."   min="0" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Remarks</label>
                            <div class="col-md-8">
                                <textarea class="form-control" rows="2"  name="remarks" id="remarks"  style="resize:none" ></textarea>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Paper Type </label>
                            <div class="col-sm-8">
                                <select class="form-select" name="upload_paper_type" id="upload_paper_type">
                                       <option value="">Select One</option>
                                       <option value="1">Advance Given</option>
                                       <option value="2">Voucher Received</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Choose File</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" accept="image/*,.pdf" name="dr_invoice_path" id="daily_transaction_file_selector">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <div class="col-md-9">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" id="dr_invoice_submit_btn"   class="btn btn-primary waves-effect"  >Save</button>
                            </div>
                        </div>
                </form>
                <div class="form-group row custom_form_group">
                    {{-- <label class="col-md-3 control-label">File Preview:</label> --}}
                    <div class="col-md-12">
                            <img id="daily_transaction_file_previewer" class="upload_image" style="width:100%; max-height: 300px; object-fit: contain; display:none;" alt="Image Preview">
                    <!-- PDF File Preview Here -->
                    </div>
                </div>
              </div>
          </div>
    </div>
</div>



   <!-- Daily Cash Receive Modal !-->

<div class="modal fade" id="cash_received_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 style="color: green; text-align:center">Cash Received Form </h5>
                <hr>
                <form class="form-horizontal" id="daily_cash_received_form"  action="{{route('company.daily.transaction.cash.receive')}}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="form-group row custom_form_group">
                        {{-- <input type="hidden" class="form-control" name="request_type" value="2"> --}}
                        <input type="hidden" class="form-control" id="cr_vou_auto_id" name="cr_vou_auto_id">

                        <label class="col-sm-4 control-label">Cheque/Receipt No.</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="receipt_number" id="receipt_number"
                                placeholder="Cheque/Receipt No. Here...">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Receiving Source </label>
                        <div class="col-sm-8">
                            <select class="form-select" name="cash_receive_method" id="cash_receive_method" required>
                                <option value="CASH">CASH</option>
                                <option value="BANK">BANK</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Bank Name </label>
                        <div class="col-sm-8">
                            <select class="form-select" name="bank_id" id="bank_id">
                                <option value="">Select Bank Name</option>
                                @foreach($bank_list as $bn)
                                <option value="{{$bn->id}}"> {{ $bn->bank_name}}-{{ substr($bn->account_no, -4) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="receive_amount" id="receive_amount"
                                placeholder="Enter Amount Here..." min="0" step="1" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="cash_receive_date" name="cash_receive_date" value="<?= date("Y-m-d")
                                ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Remarks</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="cash_remarks" id="cash_remarks"></textarea>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-md-4 control-label">File</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file btnu_browse">
                                        Browse… <input type="file" name="cr_invoice_path"  id="imgInp4">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                    </div>


                      <button type="submit" id="cr_invoice_submit_btn" class="btn btn-primary waves-effect"
                        style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>



  <!-- Transaction Search Section-->
<div class="row d-none" id="search_section">
    <div class="col-md-12">

         <div class="card">
            {{-- <h5 class="card-title"> Searching Transaction Records</h5> --}}
            <div class="form-group mt-2 row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                 <div class="col-md-3 row">
                    <label class="col-md-4 control-label d-block">Type</label>
                    <div class="col-md-8">
                     <select class="form-select" name="transaction_searchBy" id="transaction_searchBy" required>
                        <option value="1">Expense</option>
                        <option value="2">Cash Received </option>
                    </select>
                </div>
                </div>
                <div class="col-md-2">
                        <input class="form-control" type="number" id="search_employee_id" name="search_employee_id"  placeholder="Employee ID">
                </div>
                <div class="col-md-4 row">
                    <label class="col-md-2 control-label d-block">From</label>
                    <input type="date" class="col-md-4 form-control" id="from_date" value="<?= date("Y-m-d") ?>" required>
                    <label class="col-md-2 control-label d-block">To</label>
                    <input type="date" class=" col-md-4 form-control" id="to_date" value="<?= date("Y-m-d") ?>" required>
                </div>
                <div class="col-md-2">
                    &nbsp; &nbsp;&nbsp; &nbsp;
                    <input class="form-check-input" type="checkbox" id="search_by_inserted_date" name="search_by_inserted_date" value="1">
                    <label class="form-check-label">Search by <br> Insert Date</label>
                </div>
                <div class="col-md-1">
                    <button type="submit" onclick="searchingTransactionRecords()"
                        class="btn btn-primary waves-effect">SEARCH</button>
                </div>
            </div>
        </div>


        <div class="card-body" id="searching_result_section">

            {{-- Expense Records --}}
            <div class="row d-none" id="expense_search_records_section">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>ID</th>
                                    <th>Emp. Name</th>
                                    <th>Expense Type</th>
                                    <th>PaidBy</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Inserted</th>
                                    <th>Amount</th>
                                    <th style="width: 130px;">Manage</th>
                                </tr>
                            </thead>
                            <tbody id="expense_records_table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Cash Received Records --}}
            <div class="row d-none" id="cash_search_records_section">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Bank Name</th>
                                    <th>Received By</th>
                                    <th>Date</th>
                                    <th>Inserted By</th>
                                    <th>Remarks</th>
                                    <th>Amount</th>
                                    <th style="width: 100px;">Manage</th>
                                </tr>
                            </thead>
                            <tbody id="cash_received_records_table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Transaction report Section-->
<div class="row d-none" id="report_section">
    <div class="col-md-12">
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#transaction_recport_form_modal" class="btn btn-primary waves-effect">Balance</button>
            </div>
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#expense_by_empid_report_modal" class="btn btn-primary waves-effect">Search By Employee</button>
            </div>
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#trans_report_date_date" class="btn btn-primary waves-effect">Date By Date</button>
            </div>
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#expense_type_report" class="btn btn-primary waves-effect">Expense Type</button>
            </div>
            <div class="btn-group" role="group" aria-label="Third group">
                <!--<button type="button"   data-toggle="modal" data-target="#transaction_recport_form_modal" class="btn btn-primary waves-effect">Balance Report</button> -->
                <button type="button" data-toggle="modal" data-target="#bank_cash_form_modal"
                            class="btn btn-primary waves-effect">Cash Receive Report</button>
            </div>

        </div>
    </div>
</div>

<!-- Daily Transaction Report !-->
<div class="modal fade" id="transaction_recport_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daily Transaction Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                   <form   action="{{ route('company.daily.transaction.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="date" id="report_date" name="report_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>
                     <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label"></label>
                        <div class="col-sm-6 form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="only_selected_date" name="only_selected_date" />
                            <label class="form-check-label" for="only_selected_date">Only Cash Received & Expensed</label>
                        </div>
                    </div>
                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                 </form>
              </div>
          </div>
    </div>
</div>

<!-- Date by Date Summary Transaction Report !-->
<div class="modal fade" id="trans_report_date_date" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Date by Date Summary Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                   <form   action="{{ route('company.datebydate.trans.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf
                    <div class="form-group row custom_form_group">
                        <label class="col-md-2 control-label d-block">Type </label>
                        <div class="col-md-10">
                            <select class="form-select" name="report_type" id="report_type">
                                <option value="">Cash Received-Expensed & Balance</option>
                                <option value="1">Expensed</option>
                                <option value="2">Cash Received </option>
                                <option value="5">Cash Received & Expensed Details</option>
                                <option value="7">Cash Received & Expensed Summary</option>
                                <option value="6">Cash Received & Expensed Month by Month </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">From<span class="req_star">*</span> </label>
                        <div class="col-sm-10">
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">To<span class="req_star">*</span> </label>
                        <div class="col-sm-10">
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Process</button>
                 </form>
              </div>
          </div>
    </div>
</div>

<!-- Expense by Emp ID Report !-->
<div class="modal fade" id="expense_by_empid_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daily Expense By Employee Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">

                   <form   action="{{ route('company.datebydate.trans.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf
                    <input type="hidden" class="form-control" name = "report_type"  value="3" >

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="number" id="employee_id" name="employee_id" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                 </form>
              </div>
          </div>
    </div>
</div>

<!-- Expense Head Transaction Report !-->
<div class="modal fade" id="expense_type_report" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Type Date To Date Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">

                    <form   action="{{ route('daily.transaction.datebydate.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf
                    <input type="hidden" class="form-control" name = "report_type"  value="4" >
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Expense Type <span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <select class="form-select" name="expense_type" id="expense_type">
                                <option value="">Please Select One </option>
                                @foreach($expense_types as $r)
                                <option value="{{ $r->cost_type_id }}">{{$r->cost_type_name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('expense_type'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('expense_type') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>
                      <div class="form-group row custom_form_group">
                        <label class="control-label col-md-4"  >Report Type</label>
                        <div class="col-sm-6">
                        <select class="form-select" name="report_type" >
                            <option value="1">Date By Date Expense Details</option>
                            <option value="2">Date To Date All Expense Summary</option>
                            <option value="3">Expense Summary Month by Month</option>
                        </select>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Process</button>
               </div>
               </form>
          </div>
    </div>
</div>

<!-- Bank Cash Receive report Modal !-->
<div class="modal fade" id="bank_cash_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 style="color: green; text-align:center">Cash Receive From Bank Report </h5>
                <hr>
                <form action="{{route('company.daily.transaction.bank')}}" method="POST" class="form-horizontal" id="bank_cash_received_form" target="_blank">
                    @csrf
                     <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Bank Name </label>
                        <div class="col-sm-8">
                            <select class="selectpicker" name="bank_id[]" id="bank_id[]" multiple required>                               >
                                 <option value="">All</option>
                                <option value="-1">Cash Received</option>
                                @foreach($bank_list as $bn)
                                <option value="{{$bn->id}}"> {{ $bn->bank_name}}-{{ substr($bn->account_no, -6) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Date From<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="cash_receive_date" name="date_from" value="<?= date("Y-m-d")
                                ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Date To<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="cash_receive_date" name="date_to" value="<?= date("Y-m-d")
                                ?>"
                            class="form-control" required>
                        </div>
                    </div>

                    <button type="submin" class="btn btn-primary waves-effect"
                        style="border-radius: 15px; width: 120px; height: 35px; letter-spacing: 1px;">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



<script>

    function showSearchingForm(){
        $('#report_section').removeClass("d-block").addClass('d-none');
        $('#search_section').removeClass("d-none").addClass('d-block');
    }
    function showReportProcessingSection(){
        $('#search_section').removeClass("d-block").addClass('d-none');
        $('#report_section').removeClass("d-none").addClass('d-block');
    }

     $(document).ready(function () {
            // Debir/Expense Invoice form validation
            $("#daily_expense_form").validate({
                submitHandler: function (form) {
                    return false;
                },
                rules: {
                    expense_type: {
                        required: true,
                    },
                    amount: {
                        required: true,
                    },
                    pay_type:{
                        required:true,
                    },
                    expense_date:{
                        required:true,
                    }

                },
                messages: {
                    expense_type: {
                        required: "You Must Be Select Expense Type",
                    },
                    amount: {
                        required: "You Must Be Input This Field",
                    },
                    pay_type:{
                        required:"You Must Be Select Payment Method",
                    },
                    expense_date:{
                        required:"You Must Be Select Expense Date",
                    }

                },
            });

        // new Debir/Expense Invoice form submit
        $("#daily_expense_form").submit(function (e) {

                e.preventDefault();

                // Validate form first
                if(!$("#daily_expense_form").valid()) {
                     return false;
                }

               // var form = $("#daily_expense_form")[0]; // Get DOM element
               // var formData = new FormData(form); // Create FormData from the form element

                var form = $("#daily_expense_form");
                var formData =  new FormData($(this)[0]);  // if same name two form then o index

                var action = form.attr("action");
                 document.getElementById("dr_invoice_submit_btn").disabled = true;

                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                        },
                })
                .done(function(response) {

                        document.getElementById("dr_invoice_submit_btn").disabled = false;
                        if(response.status == 200){
                            showMessage(response.message,'success');

                             $('#daily_expense_form')[0].reset();
                             $("#daily_expense_form_modal").modal('hide'); // hide modal

                        }else {
                            showMessage(response.message,'error');
                        }
                })
                .fail(function(xhr) {
                    document.getElementById("dr_invoice_submit_btn").disabled = false;
                    showMessage("Operation Failed, Please Try Aggain",'error');
                 });
        });

         // reset Debit/Expense form
        $('#daily_expense_form_modal').on('hidden.bs.modal', function (e) {
            $(this)
            .find("input,textarea,select,file").val('').end()
            .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
            document.getElementById('daily_transaction_file_previewer').src = '';

        })

         // Cash Recieved Invoice form submit ajax function working
        $("#daily_cash_received_form").submit(function (e) {

                e.preventDefault();
                var form = $("#daily_cash_received_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                document.getElementById("cr_invoice_submit_btn").disabled = true;

                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                        },
                })
                .done(function(response) {

                        document.getElementById("cr_invoice_submit_btn").disabled = false;
                        if(response.status == 200){
                             $('#daily_cash_received_form')[0].reset();
                             $("#cash_received_form_modal").modal('hide'); // hide modal
                             showMessage(response.message,'success');
                        }else {
                            showMessage(response.message,'error');
                        }
                })
                .fail(function(xhr) {
                    document.getElementById("cr_invoice_submit_btn").disabled = false;
                    showMessage("Operation Failed, Please Try Aggain",'error');
                 });
        });



    });



    function searchingTransactionRecords(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var search_type = $('#transaction_searchBy').val();    // 1 = expense , 2= cash received
        var search_by_inserted_date =  document.getElementById("search_by_inserted_date").checked;
        var employee_id = $('#search_employee_id').val();


        $('#advance_paper_list_table').html('');
        $.ajax({
            type:"get",
            url: "{{  route('company.daily.transaction.searching') }}",
            data:{
                from_date:from_date,
                to_date:to_date,
                search_type:search_type,
                search_by_inserted_date:search_by_inserted_date == true ? 1:0,
                employee_id:employee_id
            },
            success:function(response){
                if(response.status == 200){
                    if(search_type == 1){
                        showExpenseRecords(response)
                    }else {
                        // cash received records
                        showCashReceivedRecords(response);
                    }
                }else {
                    showMessage('Record Not Found','error');
                }
            },
            error:function(response){
                showMessage('Operation Failed','error');
            }
        });
    }


    function showExpenseRecords(response){

        $("#cash_search_records_section").removeClass("d-block").addClass("d-none");
        $("#expense_search_records_section").removeClass("d-none").addClass("d-block");
        const s3_bucket_url = "{{ config('app.aws_s3_bucket_url') }}";

        var rows = "";
        var counter = 1;
        var total_amount = 0;
        $.each(response.data, function (key, value) {
                total_amount += parseFloat(value.Amount);
             rows += `
                <tr>
                    <td>${counter++}</td>
                    <td>${value.employee_id}</td>
                    <td>${value.employee_name}</td>
                    <td>${value.cost_type_name}</td>
                    <td>${value.PaymentType} <br> ${value.CreditedFromId == 205? 'C.Flow':"Other"} </td>
                    <td>${value.ExpenseDate}</td>
                    <td> ${value.Remarks != null ? value.Remarks : '-'}</td>
                    <td> ${value.name}</td>
                    <td>${value.Amount}</td>
                    <td>
                        @can('debit_invoice_edit')
                             <a href="#" onClick="searchingDailyTransactionRecordForEditing(${value.dr_vou_auto_id},1)"  title="Edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>|
                        @endcan

                        @can('debit_invoice_delete')
                            <a href="#" onClick="deleteDailyTransaction(1,${value.dr_vou_auto_id })" title="Delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>|
                        @endcan
                        <a target="_blank" href="${s3_bucket_url+value.invoice_path}" > ${value.invoice_path != null ? '<i class="fas fa-eye fa-lg view_icon"></i>' : 'NF'} </a>|
                        <a target="_blank" href="${s3_bucket_url+value.invoice_path2}" > ${value.invoice_path2 != null ? '<i class="fas fa-eye fa-lg view_icon"></i>' : 'NF'} </a>
                    </td>
                </tr>
                `
        });
             rows += `
                <tr>
                    <td style="font-weight: bold;" colspan="9">Total Amount</td>
                    <td colspan="2">${total_amount.toFixed(2)}</td>

                </tr>
                `
        $('#expense_records_table').html(rows);
    }

    function showCashReceivedRecords(response){

        $("#expense_search_records_section").removeClass("d-block").addClass("d-none");
        $("#cash_search_records_section").removeClass("d-none").addClass("d-block");
        const s3_bucket_url = "{{ config('app.aws_s3_bucket_url') }}";
        var rows = "";
        var counter = 1;
        var total_amount = 0;
        $.each(response.data, function (key, value) {
            // <a target="_blank" href="{{ url('${value.cr_invoice_path}') }}" > ${value.cr_invoice_path != null ? '<i class="fas fa-eye fa-lg view_icon"></i>' : ''} </a>
            total_amount += parseFloat(value.Amount);
            rows += `
                <tr>
                    <td>${counter++}</td>
                    <td>${value.bank_name != null ? value.bank_name : "-"}</td>
                    <td>${value.ReceiveMethod}</td>
                    <td>${value.ReceivedDate}</td>
                    <td> ${value.created_by_name}</td>
                    <td> ${value.Remarks != null ? value.Remarks : '-'}</td>
                    <td>${value.Amount}</td>
                    <td>
                        @can('credit_invoice_edit')
                            <a href="#" onClick="searchingDailyTransactionRecordForEditing(${value.cr_vou_auto_id},2)"  title="Edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a> |
                        @endcan

                        @can('credit_invoice_delete')
                            <a href="#" onClick="deleteDailyTransaction(2,${value.cr_vou_auto_id})" title="Delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                        @endcan
                        <a target="_blank" href="${s3_bucket_url+value.cr_invoice_path}" > ${value.cr_invoice_path != null ? '<i class="fas fa-eye fa-lg view_icon"></i>' : ''} </a>
                    </td>
                </tr>
                `
        });
          rows += `
                <tr>
                    <td style="font-weight: bold;" colspan="6">Total Amount</td>
                    <td colspan="2">${total_amount.toFixed(2)}</td>

                </tr>
                `
        $('#cash_received_records_table').html(rows);
    }


    // Delete Both Cash Recive and Expense record
    function deleteDailyTransaction(deleteOperationType,record_auto_id){

       swal({
           title: "Are you sure?",
           text: "Once deleted, You will not be able to recover this Record!",
           icon: "warning",
           buttons: true,
           dangerMode: true,
       })
       .then((willDelete) => {
           if (willDelete) {
               $.ajax({
                   type: 'POST',
                    url:"{{ route('company.daily.transaction.delete') }}",
                    data:{
                        operation_type:deleteOperationType,
                        record_auto_id:record_auto_id,
                    },
                    datatype:"json",
                    success:function(response){

                        if(response.success ==true){
                            $("#daily_expense_form_modal").modal('hide'); // hide modal
                            showMessage(response.message,'success');
                            searchingTransactionRecords();
                        }else {
                            showMessage(response.message,'error');
                        }
                      //  showMessage('Operation Under Development','success');
                    },
                    error:function(response){

                        showMessage('Network Error','error');
                    }
               });
           }
       });
       //  window.location.reload();
    }

    // Open Expense/Debit Record for editing
    function searchingDailyTransactionRecordForEditing(record_auto_id,record_type){

        // var record_type =   1;

        $.ajax({
            type:"GET",
            url: "{{  route('company.daily.transaction.edit') }}",
            data:{
                record_auto_id:record_auto_id,
                record_type:record_type,
            },
            success:function(response){
                if(response.status == 200){

                    var arecord = response.data;
                     if(record_type == 1){
                      //  expense/debit inovice record
                        $("#dr_vou_auto_id").val(arecord.dr_vou_auto_id);
                        $("#employee_id").val(arecord.employee_id);
                        $("#expense_method").val(arecord.PaymentType);
                        $("#amount").val(arecord.Amount);
                        $("#remarks").val(arecord.Remarks);
                        $("#expense_date").val(arecord.ExpenseDate);
                        $("#expense_type").val(arecord.DrTypeId);
                        $("#project_id").val(arecord.project_id);
                        $('#credit_account_id').val(arecord.CreditedFromId);
                        $("#daily_expense_form_modal").modal('show');

                    }else {
                        // cash/credit inovice records
                        $("#cr_vou_auto_id").val(arecord.cr_vou_auto_id);
                        $("#receipt_number").val(arecord.receipt_number);
                        $("#cash_receive_method").val(arecord.ReceiveMethod);
                        $("#receive_amount").val(arecord.Amount);
                        $("#cash_remarks").val(arecord.Remarks);
                        $("#cash_receive_date").val(arecord.ReceivedDate);
                        $("#cash_received_form_modal").modal('show');
                    }

                }else {
                    showMessage('Advance Record Not Found','error');
                }
            },
            error:function(response){
                 showMessage('Operation Failed','error');
            }
        });
    }


    function showTransactionReport(){
        var report_date = $('#report_date').val();
        var url = "{{ route('company.daily.transaction.report', ':parameter') }}";
        url = url.replace(':parameter', report_date);
        window.open(url, '_blank');

   }





    $('#cash_received_form_modal').on('hidden.bs.modal', function (e) {
        $(this)
        .find("input,textarea").val('').end() ;
    })

    function showMessage(message,operationType){

         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })

                Toast.fire({
                    type: operationType,
                    title: message
                })

    }





    // file upload preview system
    $(document).ready(function() {
            $(document).on('change', '.btn-file :file', function() {
                var input = $(this),
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [label]);
            });

            $('.btn-file :file').on('fileselect', function(event, label) {

                var input = $(this).parents('.input-group').find(':text'),
                    log = label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log)
                        alert(log);
                }

            });



        function readURL(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    var preview = $('#daily_transaction_file_previewer');
                    var fileType = file.type;
                    var fileName = file.name.toLowerCase();

                    // If the file is an [Image]
                    if (fileType.startsWith('image/')) {
                        preview.attr('src', e.target.result);
                        preview.show();
                    }
                    // If PDF
                    else if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
                        preview.hide();
                        $('#pdf-preview-container').remove();

                        var pdfHtml = `
                            <div id="pdf-preview-container" style="text-align:center; padding:15px; border:2px dashed #ccc; border-radius:10px; background:#f9f9f9;">
                                <!-- PDF Icon -->
                                <img src="https://img.icons8.com/color/96/000000/pdf.png" alt="PDF Icon" style="width:20px; height:20px;">
                                <span style="margin:10px 0 5px; font-weight:bold; color:#333;">${file.name}</span>
                                <p style="margin:0; color:#666; font-size:14px;">PDF File Selected</p>
                                <br>
                                <embed src="${e.target.result}" type="application/pdf" width="100%" height="300px" style="border:1px solid #ddd; border-radius:8px;">
                            </div>
                        `;

                        preview.after(pdfHtml);
                    }
                    else {
                        preview.hide();
                        $('#pdf-preview-container').remove();
                        preview.after(`
                            <div id="pdf-preview-container" style="text-align:center; padding:20px; background:#f8f9fa; border:1px dashed #ccc; border-radius:8px;">
                                <p>Selected File: <strong>${file.name}</strong></p>
                            </div>
                        `);
                    }
                }

                reader.readAsDataURL(file);
            }
        }

        $("#daily_transaction_file_selector").change(function() {
            readURL(this);
        });

    });

    // external PDF Merger website open
function openPDFMergerWebsite() {
    window.open('https://www.onlineprise.com', '_blank');
}


</script>

@endsection
