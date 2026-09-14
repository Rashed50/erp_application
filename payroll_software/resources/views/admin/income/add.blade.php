@extends('layouts.admin-master')
@section('title') Founding Source @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Founding Source</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Founding Source</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Income Source Information.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Income Source Information.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete This Information.
          </div>
        @endif
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
      <form class="form-horizontal" id="registration" action="{{ route('insert.income-source') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Founding Source</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">


              <div class="form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Project Name:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="project_id" required>
                        <option value="">Select Here</option>
                        @foreach($project as $proj)
                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
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
                        <option value="{{ $emp->emp_auto_id }}">{{ $emp->employee_name }}</option>
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
                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" value="{{ old('invoice_no') }}" placeholder="Invoice No" required>

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
                    <input type="text" class="form-control" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" placeholder="Total Amount" required>

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
                    <input type="text" class="form-control" name="vat" id="vat" value="{{ old('vat') }}" placeholder="VAT" required>

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
                    <input type="text" class="form-control" name="debit_amount" id="debit_amount" value="{{ old('debit_amount') }}" placeholder="Debit Amount" required>

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

                        <option value="0">Pending</option>
                        <option value="1">Released</option>
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
                    <textarea style="height:120px; resize:none" name="remarks" class="form-control" placeholder="Remarks" required>{{ old('remarks') }}</textarea>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Description:<span class="req_star">*</span></label>
                  <div>
                    <textarea style="height:120px; resize:none" name="description" class="form-control" placeholder="Description" required>{{ old('description') }}</textarea>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Submited Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="submitted_date" name="submitted_date" value="{{old('submitted_date')}}" required>
                  </div>
              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>


<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Founding Source </h3>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>

                                      <th>Project Name</th>
                                      <th>Employee Name</th>
                                      <th>Total Amount</th>
                                      <th>VAT</th>
                                      <th>Debit</th>
                                      <th>Pend/Releas Amount</th>
                                      <th>Status</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->project->proj_name }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->total_amount }}</td>
                                    <td>{{ $item->vat }}</td>
                                    <td>{{ $item->debit_amount }}</td>
                                    <td>
                                      @if($item->invoice_status == 1)
                                        {{ $item->relesed_amount }} (Rels)
                                      @else
                                        {{ $item->pending_amount }} (Pend)
                                      @endif
                                    </td>

                                    <td>
                                      @if($item->invoice_status == 1)
                                        <span class="badge bg-primary">Released</span>
                                      @else
                                        <span class="badge bg-primary">Pending</span>
                                      @endif
                                    </td>
                                    @if($item->invoice_status == 0)
                                    <td>
                                        <a href="{{ route('edit.income',$item->inc_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                        <a href="{{ route('remove.income',$item->inc_id ) }}" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                    </td>
                                    @else
                                      <td> --- </td>
                                    @endif
                                  </tr>
                                @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>

      </div>
    </div>
</div>




@endsection
