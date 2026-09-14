@extends('layouts.admin-master')
@section('title') My Profile @endsection
@section('content')

<style>
    .even{
        background-color: #f2f2f2;
        color: #000;
        font-weight: 300;
        font-size: 13px;
    }
    .odd{
        background-color: #fff;
        color: #000;
        font-weight: 300;
        font-size: 13px;
    }
</style>

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> My Profile Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> My Profile</li>
        </ol>
    </div>
</div>

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
            <strong>{{Session::get('error')}}</strong>, Please try Again.
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form id="user-profile" method="post" enctype="multipart/form-data" action="{{ route('user.user-profile-update') }}">
            @csrf
            <div class="card">
                <div class="card-body card_form">
                    <div class="form-group row custom_form_group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Profile Name:<span class="req_star">*</span></label>
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
                            <input type="email" placeholder="Email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled required>
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
                            <input type="text" placeholder="Phone" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}">
                        </div>
                    </div>

                    <div class="form-group custom_form_group row{{ $errors->has('current_password') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3">Current Password:<span class="req_star">*</span></label>
                            <div class="col-md-7">
                            <div class="input-group" id="show_hide_password1">
                                <input class="form-control" type="password" placeholder="********" id="current_password" name="current_password" autocomplete="new-password">
                                <div class="input-group-addon">
                                    <a href="#" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            @if ($errors->has('current_password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('current_password') }}</strong>
                                </span>
                            @endif
                            </div>
                    </div>

                    <div class="form-group custom_form_group row{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="control-label col-md-3">New Password:<span class="req_star">*</span></label>
                        <div class="col-md-7">
                            <div class="input-group" id="show_hide_password2">
                                <input class="form-control" type="password" placeholder="********" id="oldpassword" name="password" autocomplete="new-password">
                                <div class="input-group-addon">
                                    <a href="#" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
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
                                <input class="form-control" type="password" placeholder="********" id="password_confirmation" name="password_confirmation">
                                <div class="input-group-addon">
                                    <a href="#" style="color:#333"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            @if ($errors->has('password_confirmation'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Profile Photo:</label>
                        <div class="col-sm-4">
                          <div class="input-group">
                            <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                Browse… <input type="file" name="profile_photo" id="imgInp4" accept="image/x-png,image/gif,image/jpeg"  >
                              </span>
                            </span>
                            <input type="text" class="form-control" readonly>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          {{-- <img id='img-upload44' class="upload_image" /> --}}
                           <img class="thumb-xl rounded-circle" id="img-upload4" width="200px"   />
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary waves-effect">Update Profile</button>
                        </div>
                         <div class="col-sm-3"></div>
                    </div>
                </div>

            </div>
        </form>
    </div>

</div>
<div class="row">
    <div class="col-md-2"></div>
    {{-- permission --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title card_top_title">User Permissions Information</h3>
                <div class="table-responsive">
                    <table id="example2" class="display" style="width:100%">
                        <thead>
                            <tr style="background-color: #ddd; color:#000; font-weight: 500;">
                                <th style="text-align: center;">S.N</th>
                                <th style="text-align: center;">Permission Name</th>
                                <th style="text-align: center;">Guard Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter=1;
                            @endphp
                            @foreach($user_permissions as $value)
                            <tr @if($counter++ % 2 == 0) class="even" @else class="odd" @endif>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td style="text-align: left; padding:5px;"> {{ $value->name }}</td>
                                <td  style="text-align: center;"> {{ $value->guard_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

@section('script')
  <script src="{{ asset('contents/admin') }}/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ asset('contents/admin') }}/datatables/js/datatables.init.js"></script>
@endsection


<script type="text/javascript">


  // show hide password
    $(document).ready(function() {

        // current pass field
        $("#show_hide_password1 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password1 input').attr("type") == "text") {
            $('#show_hide_password1 input').attr('type', 'password');
            $('#show_hide_password1 i').addClass("fa-eye-slash");
            $('#show_hide_password1 i').removeClass("fa-eye");
            } else if ($('#show_hide_password1 input').attr("type") == "password") {
            $('#show_hide_password1 input').attr('type', 'text');
            $('#show_hide_password1 i').removeClass("fa-eye-slash");
            $('#show_hide_password1 i').addClass("fa-eye");
            }
        });


         // new pass field
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

 // new pass field confirm
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
