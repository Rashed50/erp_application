@extends('layouts.admin-master')
@section('title') Office Building @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Accommodation Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Villa Information</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <form class="form-horizontal" id="office_buildings-validation" action="{{ route('rent.new-building.insert') }}"
            enctype="multipart/form-data" method="post">
            @csrf
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body" style="padding-top: 0;">

                    <div class="form-group row custom_form_group" id="searchEmployeeId">
                        <label class="control-label col-md-4">Building Manager Emp ID:<span class="req_star">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control typeahead" placeholder="Input Employee ID"
                                name="empId" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()"
                                onblur="hideResult()">

                            <div id="showEmpId"></div>
                            <span id="error_show" class="d-none" style="color: red"></span>
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Building Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="ofb_name" value="{{ old('ofb_name') }}" placeholder="Input Building Name">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Rent Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="ofb_rent_date" value="{{date('Y-m-d')}}" >
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group custom_form_group">
                        <!-- <label class="control-label d-block" style="text-align: left;">Rent Form:<span class="req_star">*</span></label> -->
                        <div>
                            <input type="hidden" class="form-control" name="ofb_rent_form"
                                value="{{ old('ofb_rent_form') }}" placeholder="Input Rent Form">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Owner Mobile:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="ofb_owner_mobile"
                                value="{{ old('ofb_owner_mobile') }}" placeholder="Input Owner Mobile">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Accommodation Capacity:<span
                                class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="ofb_accomodation_capacity"
                                value="{{ old('ofb_accomodation_capacity') }}"
                                placeholder="Enter Building Accomodation Capacity">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Amount Per Month:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="ofb_rent_amount"
                                value="{{ old('ofb_rent_amount') }}" placeholder="Input Amount Per Month">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Advance Payment:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="ofb_advance_amount"
                                value="{{ old('ofb_advance_amount') }}" placeholder="Input Advance Payment">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Agrement Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="ofb_agrement_date" value="{{date('Y-m-d')}}" >
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Expiration date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="ofb_expiration_date" value="{{date('Y-m-d')}}"
                                min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> City Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="ofb_city_name"
                                value="{{ old('ofb_city_name') }}" placeholder="Input City Name">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4"> Location Details<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <textarea class="form-control" name="ofb_location_details"
                                placeholder="Input Building Location Details">{{ old('ofb_location_details') }}</textarea>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Agreement Papers:</label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file btnu_browse">
                                        Browse… <input type="file" name="ofb_dead_papers" id="imgInp3">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <img id='img-upload3' class="upload_image" />
                        </div>
                    </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Office Building List</h3>
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
                                        <th>Building Name</th>
                                        <th>Owner Mobile</th>
                                        <th>City</th>
                                        <th>Manager Name</th>                                        
                                        <th>Amount (Per Month) </th>
                                        <th>Advance Amount</th>
                                        <th>Agrement Date</th>
                                        <th>Rent Date</th>
                                        <th>Experation Date</th>
                                        <th>Dead Pepars</th>
                                        
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td> {{ $item->ofb_name }} </td>
                                        <td> {{ $item->ofb_owner_mobile }} </td>
                                        <td> {{ $item->ofb_city_name }} </td>
                                        <td>  - </td>
                                        <td> {{ $item->ofb_rent_amount }} </td>
                                        <td> {{ $item->ofb_advance_amount }} </td>
                                        <td> {{ $item->ofb_agrement_date }} </td>
                                        <td> {{ $item->ofb_rent_date }} </td>
                                        <td> {{ $item->ofb_expiration_date }} </td>
                                        <td>  <a href="#">Click Here </a> </td>
                                        <td>
                                            <a href="{{ route('rent.new-building.edit',$item->ofb_id) }}"
                                                title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                            <a href="{{ route('rent.new-building.delete',$item->ofb_id) }}" id="delete"
                                                title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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
</div>


<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        $("#office_buildings-validation").validate({
            /* form tag off  */
            // submitHandler: function(form) { return false; },
            /* form tag off  */
            rules: {
                empId: {
                    required: true,
                },
                ofb_name: {
                    required: true,
                },
                ofb_rent_date: {
                    required: true,
                },
                ofb_agrement_date: {
                    required: true,
                },
                ofb_expiration_date: {
                    required: true,
                },
                
                ofb_city_name: {
                    required: true,
                },
                ofb_owner_mobile: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },

                ofb_rent_amount: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },
                ofb_advance_amount: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },

            },

            messages: {
                empId: {
                    required: "Please Input This Field!",
                },
                ofb_name: {
                    required: "Please Input This Field!",
                },
                ofb_rent_date: {
                    required: "You Must Be Select This Field!",
                },
                ofb_agrement_date: {
                    required: "You Must Be Select This Field!",
                },
                ofb_expiration_date: {
                    required: "You Must Be Select This Field!",
                },
                
                ofb_city_name: {
                    required: "Please Input This Field!",
                },
                ofb_owner_mobile: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
                ofb_rent_amount: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
                ofb_advance_amount: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
            },

            errorPlacement: function (error, element) {
                if (element.is(":radio")) {
                    error.appendTo(element.parents('.gender'));
                }
                else if (element.is(":file")) {
                    error.appendTo(element.parents('.passfortFiles'));
                }
                else {
                    error.insertAfter(element);
                }

            }




        });
    });
</script>


@endsection
