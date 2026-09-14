@extends('layouts.admin-master')
@section('title') Add Country @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Agency</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Agency</li>
        </ol>
    </div>
</div>

<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> Successfully Added New Agency.
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Please Try Again With New Agency.
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-1">  </div>
    <div class="col-md-10">
        <form class="form-horizontal" action="{{ route('agency.add-agencry.form.submit') }}" method="post" onsubmit="return agencyFormValidation()">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> New Agency</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="form-group custom_form_group{{ $errors->has('country_name') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Agency Name:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" placeholder="please enter agency name" class="form-control keyup-characters" id="agency_name" name="agency_name" value="{{old('agency_name')}}">
                            {{-- <span class="error"></span> --}}
                            @if ($errors->has('country_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('country_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group custom_form_group{{ $errors->has('country_name') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Address:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" placeholder="please enter address" class="form-control keyup-characters" id="address" name="address" value="{{old('address')}}">
                            {{-- <span class="error"></span> --}}
                            @if ($errors->has('address'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group custom_form_group{{ $errors->has('country_name') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Contact No:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" placeholder="please enter contact number" class="form-control keyup-characters" id="contact_no" name="contact_no" value="{{old('contact_no')}}">
                            {{-- <span class="error"></span> --}}
                            @if ($errors->has('contat_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('contact_no') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer card_footer_button text-center">
                    @can('agency_new_add')
                        <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                    @endcan
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>


<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>Agency Name</th>
                                        <th>Office Address</th>
                                        <th>Contact No.</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $item->agc_title }}</td>
                                        <td>{{ $item->office_address }}</td>
                                        <td>{{ $item->contact_no }}</td>
                                        <td>
                                                @can('agency_update')
                                                    <a href="" id="record_edit_button" data-toggle="modal" data-target="#employee_salary_closing_modal" title ="Edit" data-id="${value.efcr_auto_id}">
                                                    <i class="fa fa-edit fa edit_icon"></i></a>
                                                @endcan

                                        </td>
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
    <div class="col-md-1"></div>
</div>

<script type="text/javascript">
    function agencyFormValidation() {
        var name = $("#agency_name").val();
        var address = $("#address").val();
        var contact_no = $("#contact_no").val();

        if (name == "") {
            $("span[class='error']").text('please enter Agency Name!');
            return false;
        } else if (address == "") {
            $("span[class='error']").text('please enter Agency Address!');
            return false;
        } else if (contact_no == "") {
            $("span[class='error']").text('please enter Agency Contact No!');
            return false;
        }

    }
</script>

@endsection
