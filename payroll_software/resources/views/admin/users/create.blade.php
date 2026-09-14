@extends('layouts.admin-master')
@section('title') Create New User @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> User Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">
            <a href="{{ route('users.index') }}"> Users</a>
            </li>
        </ol>
    </div>
</div>

<!-- Error massege start -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
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
    <div class="col-md-2"></div>
</div>
<!-- Error massege end -->

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <!-- Serach Employee By ID Start -->
        <div class="card"><br>
            <div class="card-body card_form" style="padding-top: 10;">
                <div class="row">
                    <div class="col-md-2">
                     </div>
                    <div class="col-md-8">
                        <div class="form-group row custom_form_group{{ $errors->has('empId') ? ' has-error' : '' }}">
                            <label class="control-label col-md-4">Search By Employee ID:</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" id="empId" name="empId"
                                    value="{{old('empId')}}" placeholder="Enter Employee ID Here" autofocus>
                                    <span id="employee_not_found_error_show" class="d-none"
                                    style="color: red"></span>
                                @if ($errors->has('empId'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('empId') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="col-md-2">
                       <button type="submit" onclick="emoloyeeDetailsForUserAdd()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Serach Employee By ID End -->
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="registration" method="post" action="{{ route('users.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="card">

              <div class="card-body card_form">

                <input type="hidden" name="empAutoIDForThisUser" id="empAutoID">

                <div class="form-group row custom_form_group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="Name" class="form-control" id="employee_name" name="name" value="{{old('name')}}" required>
                      @if ($errors->has('name'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('name') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Email:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="email" placeholder="Email" class="form-control" id="employee_email" name="email" value="{{old('email')}}" required>
                      @if ($errors->has('email'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Phone:</label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="Phone" class="form-control" id="employee_own_mobile_no" name="phone_number" value="{{old('phone_number')}}">
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label text-right">Branch Office:<span
                            class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-select" name="branch_office" id="branch_office">
                                @foreach($company_branches as $br)
                                <option value="{{$br->braoff_auto_id}}">{{$br->branch_name_en}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group custom_form_group row{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">New Password:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group" id="show_hide_password2">
                        <input class="form-control" type="password" placeholder="********" id="oldPassword" name="password" autocomplete="new-password" required>
                        <div class="input-group-addon">
                          <a href="" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                      </div>
                      @if ($errors->has('password'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group custom_form_group row{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3"> Confirmed Password:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group" id="show_hide_password3">
                        <input class="form-control" type="password" placeholder="********" iid="password_confirmation" name="password_confirmation" required>
                        <div class="input-group-addon">
                          <a href="" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                      </div>
                      @if ($errors->has('password_confirmation'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('password_confirmation') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


                <div class="form-group custom_form_group row{{ $errors->has('roles') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">User Role:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group">
                      <select class="form-select" name="roles">
                        @foreach($roles as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                      </div>
                      @if ($errors->has('roles'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('roles') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


                {{-- <div class="form-group row custom_form_group{{ $errors->has('ban_image') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Profile Image:</label>
                    <div class="col-sm-4">
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" name="ban_image" id="imgInp3" accept="image/x-png,image/gif,image/jpeg">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                      @if ($errors->has('ban_image'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('ban_image') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="col-md-3">
                      <img id='img-upload3' width="200"/>
                    </div>
                </div> --}}


              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect">SAVE</button>
              </div>
          </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    function resetUI() {
        $("input[id='employee_name']").val('');
        $("#employee_own_mobile_no").val('');
        $("#employee_email").val('');
        $("#empAutoID").val('');
    }
    function employeeSearchingInputFeild() {
        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
    }

    // Enter Key Press Event Fire
    $('#empId').keydown(function(e) {
        if (e.keyCode == 13) {
            emoloyeeDetailsForUserAdd();
        }
    })

    function emoloyeeDetailsForUserAdd() {
        var empID = $("#empId").val();

        if ($("#empId").val().length === 0) {
            //  start message
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            if ($.isEmptyObject(empID)) {
                Toast.fire({
                    type: 'error',
                    title: "Please Fill This Input Field First !!!"
                })
            } else {
                Toast.fire({
                    type: 'success',
                    title: "Employee Informations are"
                })
            }
            //  end message

        } else {

           var searchType = "employee_id";
            resetUI();
            $.ajax({
                type: 'POST',
                url: "{{ route('employee.searching.searching-with-multitype.parameter') }}", //typeWise.employee-details
                data: {
                    search_by: searchType,
                    employee_searching_value: empID
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == false) {
                        $("span[id='employee_not_found_error_show']").text('Employee Not Found');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass(
                            'd-none');
                        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");
                        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none");

                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass(
                            'd-none');

                            if (response.findEmployee.length == 1) {
                                var anEmp = response.findEmployee;
                                showSearchingEmployee(anEmp[0]);
                            }else {
                                alert("Error, Multiple Employee Found")
                            }
                    }



                    console.log(response.findEmployee);

                } // end of success
            }); // end of ajax calling
        }
        // End of Method for Router calling
    }

    $('#employee_email').keyup(function() {
        this.value = this.value.toLocaleLowerCase();
    });

    function showSearchingEmployee(findEmployee) {

        $("input[id='empAutoID']").val(findEmployee.emp_auto_id);
        $("input[id='employee_name']").val(findEmployee.employee_name);
        $("input[id='employee_own_mobile_no']").val(findEmployee.mobile_no);
        $("input[id='employee_email']").val(findEmployee.email == null ? "" : findEmployee.email.toLowerCase());

    }


    // show hide password
    $(document).ready(function() {

      $("#show_hide_password2 a").on('click', function(event) {
        event.preventDefault();
        if ($('#show_hide_password2 input').attr("type") == "text") {
          $('#show_hide_password2 input').attr('type', 'password');
          $('#show_hide_password2 i').addClass("fa-eye-slash");
          $('#show_hide_password2 i').removeClass("fa-eye");
        } else if ($('#show_hide_password2 input').attr("type") == "password") {
          $('#show_hide_password2 input').attr('type', 'text');
          $('#show_hide_password2 i').removeClass("fa-eye-slash");
          $('#show_hide_password2 i').addClass("fa-eye");
        }
      });
      $("#show_hide_password3 a").on('click', function(event) {
        event.preventDefault();
        if ($('#show_hide_password3 input').attr("type") == "text") {
          $('#show_hide_password3 input').attr('type', 'password');
          $('#show_hide_password3 i').addClass("fa-eye-slash");
          $('#show_hide_password3 i').removeClass("fa-eye");
        } else if ($('#show_hide_password3 input').attr("type") == "password") {
          $('#show_hide_password3 input').attr('type', 'text');
          $('#show_hide_password3 i').removeClass("fa-eye-slash");
          $('#show_hide_password3 i').addClass("fa-eye");
        }
      });
    });


  </script>
@endsection
