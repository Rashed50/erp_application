@extends('layouts.admin-master')
@section('title') Company Vehicles @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Vehicles Information Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vehicles Report</li>
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


<!-- Single Vehicle Report -->
<div class="row">
    <div class="col-md-2"> </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div  class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                     <div class="col-md-2">
                        {{-- <select class="form-select" name="searchBy" id="searchBy" required>
                            <option value="employee_id">Searching By Employee ID</option>
                            <option value="akama_no">Searching By Iqama </option>
                            <option value="passfort_no">Searching By Passport</option>
                            <option value="employee_infos.email">Searching By Email</option>
                            <option value="1">Master Records Searching</option>
                        </select> --}}
                    </div>
                    
                   <label class="col-md-2 control-label d-block" style="text-align: left;">Vehicle Details By  </label>
                   
                    <div class="col-md-4">
                        <input type="text" placeholder="Enter Vehicle Number Or Registation Number"
                            class="form-control" id="searching_value" name="searching_value"
                            value="{{ old('empl_info') }}" required autofocus>                      
                    </div>
                    <div class="col-md-2">
                         {{-- <button type="button" id="empInfoPrintPreview" class="btn btn-primary waves-effect">Print Preview</button> --}}
                         <button type="submit" onclick="searchingVehicleInformation()" style="margin-top: 2px"
                         class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <div class="col-md-2"> </div>
</div>


<!-- Company All Vehicles Record report section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" target="_blank" action="{{ route('active-vehicle-report') }}"
            method="post">
            @csrf
            <div class="card">                 
                <div class="card-body card_form" style="padding-top: 10;">                   
                        <div class="col-md-10">                            
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Report Type </label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="report_type" required>
                                        <option value="1">All Vehicle List</option>
                                        <option value="2">Driver Not Assigned Vehicles List</option>
                                        <option value="3">Driver Assigned Vehicles List</option>
                                        <option value="4">Drivers List</option> 
                                        <option value="5">Vehicle Not Assigned Drivers List</option> 
                                    </select>
                                </div>                               
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Report Format </label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="report_format" required>
                                        <option value="1">Pdf</option>
                                        <option value="2">Excell</option>                                     
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                                    </div>
                                </div>
                            </div>
                        </div>                       
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>



<!-- Company All Vehicles Record report section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" target="_blank"
            action="{{ route('vehicle-wise-driver-infos') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">Company Driver Information for
                                Specific Vehicle </h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 10;">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Vehicle Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="veh_id" required>
                                        <option value="">Select Vehicle Name</option>
                                        @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->veh_id }}">{{ $vehicle->veh_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- All Vehicles Record report section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" target="_blank"
            action="{{ route('vehicle.type-company.name-wise-report') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">Vehicles with Driver Information Report</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 10;">
                    <div class="row">
                        <div class="col-md-1"></div>

                        <div class="col-md-10">

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Company Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="company_id">
                                        <option value="">Select Company Name</option>
                                        <option value="1">Asloob International Contracting Company</option>
                                        <option value="2">Asloob Bedda Contracting Company </option>
                                        <option value="3">Bedaa General Contracting Company</option>
                                        
                                    </select>
                                </div>
                                <div class="col-md-2"></div>
                            </div>


                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Vechicle Type:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <select class="form-select" name="veh_type_id">
                                        <option value="">Select Vechicle Type</option>
                                        <option value="1">Jeep</option>
                                        <option value="2">Pickup</option>
                                        <option value="3">Bus</option>
                                        <option value="4">Truck</option>
                                    </select>
                                </div>
                                <div class="col-md-2"></div>
                            </div>

                            <br>
                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" class="btn btn-primary waves-effect"> Process</button>
                            </div>

                        </div>

                        <div class="col-md-1"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- All Vehicles Record By Project Name report section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="project_wise_vehicle_report" target="_blank" action="{{ route('project-wise.vehicle.report') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">Project Wise Vehicles Record</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 10;">
                    <div class="row">
                        <div class="col-md-1"></div>

                        <div class="col-md-10">

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-4">Project Name:</label>
                                <div  class="col-sm-6">
                                    <select class="selectpicker" name="proj_id[]" multiple>
                                        <option value="">Select Project Name</option>
                                        @foreach ($projects as $item)
                                        <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2"></div>
                            </div>

                            <br>
                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" class="btn btn-primary waves-effect"> Process</button>
                            </div>

                        </div>

                        <div class="col-md-1"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<script type="text/javascript">
 function searchingVehicleInformation(){
       // var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#searching_value").val();

        // Create the query string with parameters
        const queryString = new URLSearchParams({
           // searchType: searchType,
            search_value: searchValue,
        }).toString();

        var parameterValue = queryString; // Set parameter value
        var url = "{{ route('vehicles.details.report.seach', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');
 }

</script>



<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous"></script>


@endsection
