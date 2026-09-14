@extends('layouts.admin-master')
@section('title') Update User @endsection
@section('internal-css')
<style media="screen">
    .switch {
    margin-left: 10px;
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    }

    .switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 5px;
    right: -5px;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }
</style>
@endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update User Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> User</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
             <strong> {{Session::get('success')}} </strong>
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

<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
         @method('PATCH')
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">

                      </div>
                      <div class="col-md-4 text-right">
                          <a href="{{ route('users.index') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> All User</a>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">



                <div class="form-group row custom_form_group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Name:<span class="req_star">*</span></label>
                      <input type="hidden" id = "id" , name ="id" value="{{$user->id}}" >
                    <div class="col-sm-7">
                      <input type="text" placeholder="Name" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
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
                      <input type="email" placeholder="Email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                      @if ($errors->has('email'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


                <div class="form-group row custom_form_group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Phone:</label>
                    <div class="col-sm-7">
                      <input type="number" placeholder="Phone Number" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required >
                      @if ($errors->has('phone_number'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('phone_number') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                 <div class="form-group custom_form_group row{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">New Password:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group" id="show_hide_password2">
                        <input class="form-control" type="password" placeholder="********" id="password" name="password" >
                        <div class="input-group-addon">
                          <a href="" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                      </div>
                      @if ($errors->has('password'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('new_password') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group custom_form_group row{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3"> Confirmed Password:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group" id="show_hide_password3">
                        <input class="form-control" type="password" placeholder="********" id="password_confirmation" name="password_confirmation">
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
                    <label class="control-label col-md-3">Select Role:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <div class="input-group">
                      <select class="form-control" name="roles">
                      <option value="{{ $existing_role }}">{{ $existing_role }}</option>
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

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Active / InActive: </label>
                    <label class="switch">
                        <input type="checkbox" name="lock_checkbox" id="lock_checkbox" {{ $user->status == 1 ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>

              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
              </div>
          </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
              <div class="card-header">
                  <div class="row">

                       <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table id="dt-vertical-scroll" class="table table-striped table-bordered" data-toggle="table" data-search="true" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Permission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user_permissions as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->id }}</td>
                                            <td> {{ $value->name }} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-2"></div>

                  </div>
              </div>
        </div>
    </div>
</div>

<script>

    $('#email').keyup(function() {

        this.value = this.value.toLocaleLowerCase();
    });


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
