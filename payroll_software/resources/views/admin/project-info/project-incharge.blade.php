@extends('layouts.admin-master')
@section('title') Add Project In-charge @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Add Project In-charge</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Add Project In-charge</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <!-- <form class="form-horizontal project-details-form" id="registration" method="post" action="{{ route('insert-project-info') }}" enctype="multipart/form-data">
          @csrf -->
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Add Project In-charge</h3>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">
                <!-- SEARCH Employee -->
                <div class="form-group row custom_form_group" style="margin-top:10px">
                    <div class="col-md-2"></div>
                    <label class="col-sm-3 control-label">Project In Charge:<span class="req_star">*</span></label>
                    <div class="col-sm-3">
                      <input type="text" class="form-control" name="employee_id" id="employee_id" required placeholder="Employee ID Type Here ">

                      <span id="error_through" style="color:red"></span>
                    </div>
                    <div class="col-sm-1" >
                      <button onclick="findEmployeeForIncharge()" class="btn btn-primary btn-sm emp-sarch">SEARCH</button>
                    </div>
                </div>
                <!-- Show Employee Details -->
                <div class="d-none" id="Incharge_some_info">

                  <div class="form-group row custom_form_group">
                      <div class="col-md-2"></div>
                      <div class="col-sm-8">
                          <ul id="list-group" class="list-group">
                              <li class="list-group-item active" aria-current="true">Employee Information</li>
                              <li class="list-group-item"> <strong>Name :</strong>  <span id="incharge_name"></span> </li>
                              <li class="list-group-item"> <strong>Department :</strong> <span id="incharge_department"></span> </li>
                              <li class="list-group-item"> <strong>Designation :</strong> <span id="incharge_designation"></span> </li>
                          </ul>
                      </div>
                      <div class="col-md-2"></div>
                  </div>
                </div>

                <!-- form -->
                <div id="finalySubmited" class="d-none card_footer_button text-center">
                  <form id="projectInchargeForm" action="{{ route('insert-project.incharge') }}" method="post">
                    @csrf
                    <!-- Select Project Option-->
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Select Project Name:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <select class="form-control" name="proj_name">
                            <option value="">Select Project</option>
                            @foreach($allProject as $proj)
                              <option value="{{ $proj->proj_id }}"> {{ $proj->proj_name }} </option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <input type="hidden" name="emp_id" id="incharge_id" value="">
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                  </form>
                </div>

              </div>
          </div>
        <!-- </form> -->
    </div>
    <div class="col-md-1"></div>
</div>

<script type="text/javascript">
    function findEmployeeForIncharge(){
      var emp_id = $("#employee_id").val();
      $.ajax({
        type:'POST',
        dataType: 'json',
        data:{ emp_id:emp_id },
        url:"{{ route('findEmployeeForIncharge') }}",
        success:function(response){
            if(response.status == 'error'){
              if(emp_id != ''){
                $("#error_through").text("dosn,t match");
                $("#Incharge_some_info").addClass('d-none').removeClass('d-block');
                $("#finalySubmited").addClass('d-none').removeClass('d-block');
              }

            }else{
              $("#employee_id").val("");
              $("#error_through").text('');
              $("#finalySubmited").removeClass('d-none').addClass('d-block');
              $("#Incharge_some_info").removeClass('d-none').addClass('d-block');
              $("input[id='incharge_id']").val(response.findEmployee.emp_auto_id);
              $("span[id='incharge_name']").text(response.findEmployee.employee_name);
              $("span[id='incharge_designation']").text(response.findEmployee.category.catg_name);
              $("span[id='incharge_department']").text(response.findEmployee.department.dep_name);
            }

        }
      });
    }

    function inchargeValidation(){
      var emp_id = $("#employee_id").val();
      $.ajax({
        type:'POST',
        dataType: 'json',
        data:{ emp_id:emp_id },
        url:"{{ route('check.valid-emp-id') }}",
        success:function(response){
          if(response.status == 'error'){
            // $("form[id='registration']").submit(false);
          }
        }
      });

    }
</script>
<!-- validation -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $("#projectInchargeForm").validate({
    rules: {
      proj_name: {
        required : true,
      },
    },

    messages: {
      proj_name: {
        required : "You Must Be Select This Field!",
      },
    },
  });
});
</script>
@endsection
