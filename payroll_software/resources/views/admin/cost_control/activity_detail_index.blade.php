@extends('layouts.admin-master')
@section('title') Project Information @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Cost Control Information</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Cost Control</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>{{Session::get('success')}}</strong>  
    </div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
    <strong>{{Session::get('error')}}</strong>  
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>


<div class="row">
  <div class="col-md-3">
        <!-- <a data-toggle="modal" data-target="#activity_element_ui_modal" class="btn btn-md btn-primary waves-effect card_top_button">New Activity Element</a><hr/> -->
  </div>
  <div class="col-md-3">
        <!-- <a data-toggle="modal" data-target="#activity_name_ui_modal" class="btn btn-md btn-primary waves-effect card_top_button">New Activity</a><hr/> -->
  </div>
  <div class="col-md-3"> 
        <a data-toggle="modal" data-target="#activity_details_ui_modal" class="btn btn-md btn-primary waves-effect card_top_button">New Activity Details </a><hr/>
  </div>
</div>
 
<!-- Activity Detail Insert M0dal -->
<div class="modal fade" id="activity_details_ui_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Activity Details </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('costcontrol.activity.details.insert.request') }}">
                    @csrf 
                    <div class="form-group row custom_form_group{{ $errors->has('proj_name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Project<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                        <select class="form-select" name="proj_name" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('plot_name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Project Plot<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                        <select class="form-select" name="plot_name" id="plot_name" required>
                            <option value="">Select Plot</option>
                            
                        </select>
                        </div>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('activity_element') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Activity Element<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                        <select class="form-select" name="activity_element" required>
                            <option value="">Select Activity Element</option>
                            @foreach($activity_elements as $al)
                                <option value="{{ $al->act_ele_auto_id }}">{{ $al->act_ele_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('activity_name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Activity Name<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                        <select class="form-select" name="activity_name" required>
                            <option value="">Select Activity Name</option>
                            @foreach($activity_names as $an)
                            <option value="{{ $an->act_auto_id }}">{{ $an->act_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('working_shift') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Working Shift<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                        <select class="form-select" name="working_shift" required>
                            <option value="0">Dayshift</option>  
                            <option value="1">Nightshift</option>                            
                        </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-3">Supervisor:<span class="req_star">*</span></label>
                        <div class="col-md-5">
                            <input type="number" placeholder="Supervisor Emp. ID" class="form-control keyup-characters" id="emp_id" name="emp_id" required>
                        </div>
                    </div>  
                    <div class="form-group row custom_form_group{{ $errors->has('total_emp') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-3">Total Emp:<span class="req_star">*</span></label>
                        <div class="col-md-5">
                            <input type="number" placeholder="Input Number of Employees" class="form-control"  value="0" id="total_emp" min="1" name="total_emp" step="1" required>
                        </div>
                    </div> 
                    <div class="form-group row custom_form_group{{ $errors->has('working_hours') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-3">Working Hours:<span class="req_star">*</span></label>
                        <div class="col-md-5">
                            <input type="number" placeholder="Input Duty Hours"  class="form-control"   value="0" id="working_hours" name="working_hours" min="1" max="23" step= "0.5" required>
                        </div>
                    </div> 

                    <div class="form-group row custom_form_group{{ $errors->has('total_hours') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-3">Total Hours:<span class="req_star">*</span></label>
                        <div class="col-md-5">
                            <input type="number" value="0"  class="form-control keyup-characters" id="total_hours" name="total_hours"  >
                        </div>
                    </div>   

                    <div class="form-group row custom_form_group{{ $errors->has('working_date') ? ' has-error' : '' }}" >
                        <label class="control-label col-md-3">Working Date:</label>
                        <div class="col-md-5">
                            <input type="date" name="working_date" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                    </div>                   
                    <div class="form-group row custom_form_group" >
                        <label class="control-label col-md-3">Remarks </label>
                        <div class="col-md-9">
                            <!-- <input type="textarea"  class="form-control keyup-characters" id="remarks" name="remarks"> -->
                            <textarea name="textarea" class="form-control" id="remarks" name="remarks" style="width:350px;height:50px;"></textarea>
                        </div>
                        
                    </div> 
                    <button type="submit"   class="btn btn-primary waves-effect">SAVE</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-8">
            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i>Activity Details Records</h3>
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
                    <th>S.N</th>
                    <th>Project</th>
                    <th>Plot</th>
                    <th>Employee</th>
                    <th>Emp. ID</th>
                    <th>Activity Element</th>                    
                    <th>Work</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    <th>Total Worker</th>
                    <th>Hours</th>
                    <th>Total Hours</th>
                    <th></th>
                  </tr>                
                   
                </thead>
                <tbody>
                        @foreach($activity_details_records as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->proj_name ?? ''}}</td>
                                        <td>{{ $item->plo_name ?? ''}}</td>
                                        <td>{{Str::words($item->employee_name,3)}}</td>                                        
                                        <td>{{ $item->employee_id ?? ''}}</td>
                                        <td>{{ $item->act_ele_name ?? ''}}</td>
                                        <td>{{ $item->act_name ?? ''}}</td>
                                        <td>{{ $item->working_date ?? ''}}</td>
                                        <td>{{ $item->remarks ?? ''}}</td>                                  
                                        <td>{{ $item->total_worker ?? ''}}</td>
                                        <td>{{ $item->daily_hours ?? ''}}</td>
                                        <td>{{ $item->total_working_hours ?? ''}}</td>
                                        <td>
                                        <a href="#"
                                                onClick="deleteActivityDetailsRecord('{{ $item->act_det_rec_auto_id }}')"
                                                title="delete"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a>
                                            <!-- <a href="{{ url('admin/employee/view/'.$item->act_det_rec_auto_id) }}" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a>
                                            -->
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
 
  
<script type="text/javascript">

  // Clear Modal View Data
$('#activity_details_ui_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
  })
  
  $(document).ready(function() {
     
    
     $('#total_emp').on('input',function(){
        calculateTotalWorkingHours();
     })
     $('#working_hours').on('input',function(){
        calculateTotalWorkingHours();          
     })

     function calculateTotalWorkingHours(){

          var total_emp = $('#total_emp').val();
          if(total_emp <0){
              alert('Please Input Correct Value of Total Working Employees');
              return;
          }
          var hours = $('#working_hours').val();
          $('#total_hours').val(total_emp*hours);
     }


     $('select[name="proj_name"]').on('change', function(){

      
        var project_id = $(this).val();
        if(project_id==null) {
          return;
        }
       // alert(project_id);
            $.ajax({
                url: "{{  url('/admin/project/plot/info/get') }}/"+project_id,
                type:"GET",
                dataType:"json",
                success:function(response) {
                      var d =$('select[name="plot_name"]').empty(); 
                      $.each(response.data, function(key, value){
                          $('select[name="plot_name"]').append('<option value="'+ value.pro_plo_auto_id +'">' + value.plo_name + '</option>');
                         // alert(value.plo_name );
                      });
                    
                      
                },

            });
         

    });




  });

  function deleteActivityDetailsRecord(act_det_rec_auto_id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            
            if (willDelete) {
               $.ajax({
                    type: 'GET',
                    url: "{{  url('admin/cost-control/activity/details/delete-request') }}/" + act_det_rec_auto_id,
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }
        });
    }
</script>

@endsection