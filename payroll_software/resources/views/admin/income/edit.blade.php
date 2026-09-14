@extends('layouts.admin-master')
@section('title') Update Founding Source @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update Founding Source</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Update Founding Source</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('update.income-source') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i>Update Founding Source</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <input type="hidden" name="id" value="{{ $edit->inc_id }}">
              <div class="form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Project Name:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="project_id" required>
                        <option value="">Select Here</option>
                        @foreach($project as $proj)
                        <option value="{{ $proj->proj_id }}" {{ $edit->project_id == $proj->proj_id ? 'selected' : '' }}>{{ $proj->proj_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('project_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('project_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Employee Name:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="employee_id" required>
                        <option value="">Select Here</option>
                        @foreach($employee as $emp)
                        <option value="{{ $emp->emp_auto_id }}" {{ $emp->emp_auto_id == $edit->submitted_by_id ? 'selected' :'' }}>{{ $emp->employee_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('employee_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('employee_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Invoice No:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" value="{{ $edit->invoice_no }}" placeholder="Invoice No" required>

                    @if ($errors->has('invoice_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('invoice_no') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('total_amount') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Total Amount:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" name="total_amount" id="total_amount" value="{{ $edit->total_amount }}" placeholder="Total Amount" required>

                    @if ($errors->has('total_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('total_amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Vat Amount:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" name="vat" id="vat" value="{{ $edit->vat }}" placeholder="VAT" required>

                    @if ($errors->has('vat'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vat') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('debit_amount') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Debit Amount:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" name="debit_amount" id="debit_amount" value="{{ $edit->debit_amount }}" placeholder="Debit Amount" required>

                    @if ($errors->has('debit_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('debit_amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Invoice Status:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" id="invoice_status" name="invoice_status" required>
                        <option value="">Select Here</option>

                        <option value="0" {{ $edit->invoice_status == 0 ? 'selected':'' }}>Pending</option>
                        <option value="1" {{ $edit->invoice_status == 1 ? 'selected':'' }}>Released</option>
                    </select>
                    @if ($errors->has('invoice_status'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('invoice_status') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>



              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Remarks:<span class="req_star">*</span></label>
                  <div>
                    <textarea style="height:120px; resize:none" name="remarks" class="form-control" placeholder="Remarks" required>{{ $edit->remarks }}</textarea>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Description:<span class="req_star">*</span></label>
                  <div>
                    <textarea style="height:120px; resize:none" name="description" class="form-control" placeholder="Description" required>{{ $edit->description }}</textarea>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Submited Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="submitted_date" name="submitted_date" value="{{ $edit->submitted_date }}" required>
                  </div>
              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
