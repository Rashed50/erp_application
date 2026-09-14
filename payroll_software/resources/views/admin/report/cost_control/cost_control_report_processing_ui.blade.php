@extends('layouts.admin-master')
@section('title')HR Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Activity Details Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Activity </li>
        </ol>
    </div>
</div>


<!-- Multiple Employee ID Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('costcontrol.activity.details.report.multiple.emp.id') }}"
            method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading"> Multiple Employee-ID  Actvity Details</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                <div  class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Employees ID:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="multiple_employee_Id"  required>
                    </div>
                </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Cost Controll Activities Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('costcontrol.activity.details.report.project.plot.element') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">Activity Details Report </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project:</label>
                        <div class="col-md-6">
                            <!-- <select class="selectpicker" name="project_id_list[]" multiple required > -->
                            <select class="form-select" name="project_id_list" required>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Plot:</label>
                        <div class="col-md-6">
                            <!-- <select class="selectpicker" name="plot_name[]" id="plot_name"  multiple> -->
                            <select class="form-select" name="plot_name" required>
                                @foreach($plots as $aplot)
                                <option value="{{ $aplot->pro_plo_auto_id }}">{{ $aplot->plo_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Activit Element:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="activity_element_list[]" multiple required >
                                @foreach($activity_elements as $al)
                                    <option value="{{ $al->act_ele_auto_id }}">{{ $al->act_ele_name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Work:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="activity_name_list[]" multiple required >
                                @foreach($activity_names as $an)
                                <option value="{{ $an->act_auto_id }}">{{ $an->act_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('working_shift') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Working Shift<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                        <select class="form-select" name="working_shift" >
                            <option value="">All</option>  
                            <option value="0">Dayshift</option>  
                            <option value="1">Nightshift</option>                            
                        </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('working_shift') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Report Format<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                        <select class="form-select" name="report_format" >
                            <option value="1">Print</option>  
                            <option value="2">Excell</option>                            
                        </select>
                        </div>
                    </div>


                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Show Summary</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
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


$('select[name="project_id_list"]').on('change', function(){
 
        var project_id = $(this).val();
        if(project_id==null) {
        return;
        }    
        $.ajax({
            url: "{{  url('/admin/project/plot/info/get') }}/"+project_id,
            type:"GET",
            dataType:"json",
            success:function(response) {
                $('select[name="plot_name"]').empty(); 
                 $.each(response.data, function(key, value){
                    $('select[name="plot_name"]').append('<option value="'+ value.pro_plo_auto_id +'">' + value.plo_name + '</option>');
                });
                
                
            },

        });        
});
    
</script>
@endsection
