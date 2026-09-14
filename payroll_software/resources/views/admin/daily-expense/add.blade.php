@extends('layouts.admin-master')
@section('title') Create Daily Expenses @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">New Expense Details</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          
        </ol>
    </div>
</div>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{Session::get('success')}} </strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{Session::get('error')}} </strong>
          </div>
        @endif
    </div>
</div>

<div class="row">
 
    <div class="col-md-1"></div>
    <div class="col-md-10">
        
      <form class="form-horizontal" id="registration" action="{{ route('company.daily.new.expesne.insert.request') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
        <br>
            <div class="card-body card_form" style="padding-top: 0;">


              <div class="row form-group custom_form_group{{ $errors->has('sub_comp_name') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label d-block" style="text-align: left;">Sub Company Name :<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="sub_comp_name" required>
                            <option value="">Select Here</option>
                            @foreach($subCompanies as $subComp)
                            <option value="{{ $subComp->sb_comp_id }}">{{ $subComp->sb_comp_name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('sub_comp_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('sub_comp_name') }}</strong>
                            </span>
                        @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('cost_type_id') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense Head :<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="cost_type_id" required>
                            <option value="">Select Here</option>
                            @foreach($expenseHeads as $aExpenseHead)
                            <option value="{{ $aExpenseHead->cost_type_id }}">{{ $aExpenseHead->cost_type_name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('cost_type_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cost_type_id') }}</strong>
                            </span>
                        @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expensed Project Name :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <select class="form-control" name="project_id" required>
                        <option value="">Select Here</option>
                        @foreach($projects as $proj)
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
               
              <div class="row form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense By:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="employee_id" name="employee_id" value="{{old('employee_id')}}" placeholder="Expense By Employee ID" required>
                    @if ($errors->has('employee_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('employee_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="row form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Vouchar No :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="vouchar_no" name="vouchar_no" value="{{old('vouchar_no')}}" placeholder="Vouchar No" required>
                    @if ($errors->has('vouchar_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vouchar_no') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('expire_date') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Date :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="date" class="form-control" id="voucher_date" name="voucher_date" value="{{Date('Y-m-d')}}" required>
                    @if ($errors->has('expire_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('expire_date') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('description') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense Note :</label>
                  <div class="col-sm-7">
                    <textarea name="description" id="description" cols="48" rows="2" placeholder="Comments"></textarea>
                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('gross_amount') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Gross Amount :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" placeholder="Gross Amount" id="gross_amount" name="gross_amount" value="0" required>
                    @if ($errors->has('gross_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('gross_amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Vat :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" placeholder="VAT Amount" id="vat" name="vat" value="0" required>
                    @if ($errors->has('vat'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vat') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('total_amount') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Total Amount :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" placeholder="Total Amount" id="total_amount" name="total_amount"  required>
                    @if ($errors->has('total_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('total_amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group {{ $errors->has('vouchar') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">Vouchar :</label>
                <div class="input-group col-sm-7">
                    <span class="input-group-btn">
                        <span class="btn btn-default btn-file btnu_browse">
                            Browse… <input type="file" id="vouchar" name="vouchar" id="imgvouchar">
                        </span>
                    </span>
                    <input type="text" class="form-control" readonly>
                </div>
                @if ($errors->has('vouchar'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('vouchar') }}</strong>
                    </span>
                @endif
                <img id='img-vouchar'/>
              </div>



            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-1"></div>
</div>

<!-- Daily Expenses list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
           
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Expense Head</th>
                                      <th>Expensed for Project</th>
                                      <th>Expense By</th>
                                      <th>Amount</th>
                                      <th>Date</th>
                                      <th>Vouchar</th>
                                      <th>Status</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($allexpenses as $item)
                                  <tr>
                                    <td>{{ $item->costType->cost_type_name }}</td>
                                    <td>{{ $item->project->proj_name }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->total_amount }}</td>
                                    <td>{{ Carbon\Carbon::parse($item->expire_date)->format('D, d F Y') }}</td>
                                    <td>
                                      <img src="{{ asset('uploads/vouchar/'.$item->vouchar) }}" alt="Vouchar" style="width: 80px">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->status }}</span>
                                    </td>
                                    @if($item->status == 'pending')
                                    <td>
                                      <a href="{{ route('company.daily.expesne.edit.form',$item->cost_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                      <a href="{{ route('company.daily.expesne.delete.request',$item->cost_id) }}" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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


<script type="text/javascript">
$(document).ready(function () {
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function (event, label) {

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
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img-vouchar').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgvouchar").change(function () {
        readURL(this);
    });


 // Total Amount Calculation
    
    $('#gross_amount').on('focus', function() {
      
      if ($('#gross_amount').val() == "0" || $('#gross_amount').val() == null) {
       $('#gross_amount').val('');
      
      }
    });

     

    $('#gross_amount').on('keyup', function() {
      
        if ($('#gross_amount').val() == "" || $('#gross_amount').val() == null) {
        $('#gross_amount').val('0'); }

        var gross_amount = parseFloat($('#gross_amount').val()).toFixed(2);
        var vat = (gross_amount * 0.15).toFixed(2);
        var total_amount = (parseFloat(vat)+parseFloat(gross_amount)).toFixed(2);
        $('#total_amount').val(total_amount);
        $('#vat').val(vat);
    }); 

    $('#vat').on('keyup', function() {

        $('#vat').focus();
            
        var gross_amount = parseFloat($('#gross_amount').val());
        var vat =  parseFloat($('#vat').val()); 
        var total_amount = (vat+gross_amount).toFixed(2);
        $('#total_amount').val(total_amount);
       
    });


});
</script>

@endsection
