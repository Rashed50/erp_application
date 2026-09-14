@extends('layouts.admin-master')
@section('title')Form @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Download Form</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Form </li>
        </ol>
    </div>
</div>
<!-- Session Message Flash -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
</div>


<!-- Download Form -->
<div class="row">
    <div class="card">
        {{-- <div class="card-header"><h4 style="text-align: center">Click on Button to Download Blank Form</h4></div> --}}
        <div class="card-body card_form form-group row custom_form_group">
            <div class="col-md-2"></div>

            <div class="col-md-2"> <a href="{{ url('uploads/all_form/advance_blank_paper.pdf') }}" class="btn btn-success" target="_blank"> Advance Form</a> </div>
            <div class="col-md-2">
                <!--<a href="{{ url('uploads/all_form/vacation_form.pdf') }}" class="btn btn-success" target="_blank">Vacation Form</a> -->

                <button type="button" id="leave_form_btn" onclick="openLeaveApplicationBlankForm()" class="btn btn-success">Vacation Form</button>
            </div>
            <div class="col-md-2"> @can('increment_form_download') <a href="#" data-toggle="modal" class="btn btn-success" data-target="#increment_form_modal" target="_blank">Increment Form</a> @endcan</div>

            <div class="col-sm-2" style="overflow:hidden">
                <button type="button" id="invoice_report" data-toggle="modal" data-target="#cash_received_form_modal" class="btn btn-success">Cash Received Form</button>
            </div>
             <div class="col-sm-2" style="overflow:hidden">
                <button type="button" id="expense_form_btn" data-toggle="modal" data-target="#expense_form_modal" class="btn btn-success">Expense Form</button>
            </div>
        </div>
    </div>
</div>

<!-- Employees Advance Paper Create Section -->
<div class="row" id="employees_advance_paper_create_section">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <form class="form-horizontal" id="employees_advance_paper_form" target="_blank" action="{{ route('emp.advance.papers.create.request')}}" method="POST">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            <h3 class="card-title card_top_title salary-generat-heading">#1 Multiple Employees Advance Paper</h3>
                        </div>
                        <div class="col-md-3">
                            {{-- <a href="{{ url('uploads/all_form/advance_blank_paper.pdf') }}" target="_blank">Advance Paper (Blank)</a> --}}
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <input type="hidden" value="3" name="form_type">

                            <div class="form-group row custom_form_group">
                                <label class="col-md-3 control-label">Amount</label>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="adv_amount" placeholder="Enter Amount Here" name="adv_amount"  autofocus >
                                </div>
                                <label class="col-md-2 control-label">Date<span class="req_star">*</span></label>
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="adv_date" value="<?= date("Y-m-d") ?>">
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-md-3 control-label">Emp. ID</label>
                                <div class="col-md-9">
                                <input type="text" class="form-control" id="emp_id"
                                placeholder="Multiple Employee ID Type Here (e.g 1020,1030)" name="adv_emp_ids" >
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-md-3 control-label">Remarks</label>
                                <div class="col-md-9">
                                    <textarea class="form-control"  name="remarks" id="remarks" rows="3"></textarea>

                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <button type="submit" class="btn btn-primary waves-effect">Create Paper</button>
                            </div>

                </div>
            </form>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>



<!-- Increment Form Modal-->
<div class="modal fade" id="increment_form_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Increment Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="increment_form" action="{{ route('form.download.emp.increment') }}" method="GET" target="_blank" >
            <div class="modal-body">
                <input type="hidden" value="1"  name="form_type"  id="form_type" >
                <div class="form-group row custom_form_group">
                    <label for="emp_id" class="col-sm-3">Employee ID</label>
                    <input type="text" class="form-control col-sm-7" name="employee_id" id="employee_id" placeholder="Enter Employee ID"   required>
                </div>
                <div class="form-group row custom_form_group">
                    <label for="in_amount" class="col-sm-3">Increment Amount </label>
                    <input type="number" class="form-control col-sm-7" name="amount" id="amount" placeholder="Enter Increment Amount" min="0" step="1" required>
                </div>
                <div class="form-group row custom_form_group">
                    <label for="eff_date" class="col-sm-3">Effective Date</label>
                    <input type="date" class="form-control col-sm-7" name="effective_date" id="effective_date" value="{{ date('Y-m-d') }}" >
                </div>
                <div class="form-group row custom_form_group">
                    <label for="min_duration" class="col-sm-3">Salary Type</label>
                    <div class="col-md-7">
                        <select name="new_salary_type" id="new_salary_type" class="form-select">
                            <option value="1">Hourly</option>
                            <option value="2">Basic</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label for="min_duration" class="col-sm-3">Minimum Duration</label>
                    <div class="col-md-7">
                        <select name="duration" id="duration" class="form-select">
                            <option value="1">1 Year</option>
                            <option value="2">2 Years</option>
                            <option value="3">3 Years</option>
                            <option value="4">4 Years</option>
                            <option value="5">5 Years</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label for="remarks" class="col-sm-3">Remarks</label>
                    <textarea class="col-sm-7" name="remarks" id="remarks" cols="30" rows="5"></textarea>
                </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" id="btn_bank_save"   class="btn btn-primary waves-effect">Create</button>
            </div>
        </form>
      </div>
    </div>
</div>


<!-- Advance Received FORM Modal !-->
<div class="modal fade" id="cash_received_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center"> ADVANCE RECEIVE FORM </h5>
                <hr>
                <form class="form-horizontal" id="cash_received_form" method="post" target="_blank" action="{{ route('emp.advance.papers.create.request') }}">
                 @csrf

                    <div  class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label"> Project</label>
                        <div class="col-sm-6">
                            <select class="form-select" id="project_id" name="project_id" autofocus >
                                <option value="">Select Working Project</option>
                                @foreach($projects as $ap)
                                <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Form Type <span class="req_star">*</span> </label>
                            <div class="col-sm-6">
                                <select class="form-select" name="form_type" id="form_type"  required>
                                    <option value="">Please Select One </option>
                                    <option value="1">Advance Received</option>
                                    <option value="2">Cash Receipt </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <input type="hidden" class="form-control" name="request_type" value="2">

                            <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Enter Employee ID Here..." required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Payment By </label>
                            <div class="col-sm-6">
                                <select class="form-select" name="payment_method" id="payment_method"  required>
                                       <option value="CASH">CASH</option>
                                       <option value="BANK">BANK</option>
                                </select>

                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount Here..." value="">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                            <div class="col-sm-6">
                                <input type="date" name="received_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                             </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Receiver Type<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-select" name="receiver_type" id="receiver_type" required>
                                    <option value="">Please Select One </option>
                                    <option value="Employee"> Employee</option>
                                    <option value="Subcontract"> Subcontract</option>
                                    <option value="Other-Bus"> Other-Bus</option>
                                    <option value="Office"> Office</option>
                                    <option value="Site Expense"> Site Expense</option>
                                    <option value="Catering Expense"> Catering Expense</option>
                                    <option value="Travel Expense"> Travel Expense</option>
                                    <option value="Sweet Water"> Sweet Water</option>
                                    <option value="Waste Water"> Waste Water</option>
                                    <option value="Advance Payment"> Advance Payment</option>
                                    <option value="Medical Treatment">Medical Treatment</option>
                                    <option value="Overtime Payment">Overtime Payment</option>
                                    <option value="Donation">Donation</option>
                                    <option value="Camp or Villa">Camp or Villa</option>
                                    <option value="Iqama Renewal">Iqama Renewal</option>
                                    <option value="Final Exit">Final Exit</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="3"  name="remarks" id="remarks" ></textarea>
                            </div>
                        </div>

                        <button type="submit" id="invoice_report_button"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Create Form</button>
                </form>
              </div>
          </div>
    </div>
</div>

<!-- Expense Invoice Printable Template  Modal !-->
<div class="modal fade" id="expense_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center"> EXPENSE INVOICE FORM </h5>
                <hr>
                <form class="form-horizontal" id="cash_received_form" method="post" target="_blank" action="{{ route('emp.advance.papers.create.request') }}">
                 @csrf
                    <input type="hidden" class="form-control" name="request_type" value="3">
                    <div  class="form-group row custom_form_group">
                        <label class="col-md-4 control-label"> Project</label>
                        <div class="col-md-8">
                            <select class="form-select" id="project_id" name="project_id" autofocus >
                                <option value="">Select Project</option>
                                @foreach($projects as $ap)
                                <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                        <div class="form-group row custom_form_group">


                            <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Employee ID Or Name Here..." required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Invoice Ref. </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="invoice_no" id="invoice_no" placeholder="Enter Invoice Reference Here...">
                            </div>
                        </div>
                         <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Issue Date<span class="req_star">*</span> </label>
                            <div class="col-md-8">
                                <input type="date" name="issue_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount Here..." value="">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Submit Date<span class="req_star">*</span> </label>
                            <div class="col-md-8">
                                <input type="date" name="received_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Invoice Description</label>
                            <div class="col-md-8">
                                <textarea class="form-control" rows="3"  name="remarks" id="remarks" ></textarea>
                            </div>
                        </div>

                        <button type="submit" id="invoice_report_button"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Create Form</button>
                </form>
              </div>
          </div>
    </div>
</div>



<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
    integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
    integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- form validation -->
<script type="text/javascript">

    $(document).ready(function () {

        $('#increment_form_modal').on('shown.bs.modal', function (e) {
             $('#employee_id').focus();
        })

        $('#increment_form_modal').on('hidden.bs.modal', function (e) {
            $(this)
            .find("input,textarea,select").val('').end()
            .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
        })




    });


        function openLeaveApplicationBlankForm() {
            // from loading from HRManagement module
            const queryString = new URLSearchParams({
                    form_type: 1,
                }).toString();
            const url = `/admin/hrmanagement/leave/application-form/print-preview?${queryString}`;
            window.open(url, '_blank');
        }
</script>
@endsection
