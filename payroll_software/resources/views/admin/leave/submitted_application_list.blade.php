@extends('layouts.admin-master')
@section('title') Leave Appl. @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Leave Approval</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Leave Approval</li>
        </ol>
    </div>
</div>

<!-- Session Message Flash -->
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
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
  </div>
 

<!-- Application List -->
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
                                        <th>S.N</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Iqama <br> Date</th>
                                        <th>Sponsor</th>
                                        <th>Contact</th>                                        
                                        <th>Trade <br> Project</th>
                                        <th>Leave For</th>
                                        <th>(Start-End)</th>
                                        <th>Vacation</th>
                                        <th>Appl. <br> Date</th>
                                        <th>Remarks</th>
                                        <th>Comments</th> 
                                        <th>Status</th> 
                                        <th>Action</th>                                         
                                    </tr>
                                </thead>
                                <tbody>
                                  @forelse($records as $arecord)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $arecord->employee_id }}</td>
                                        <td>{{ $arecord->employee_name }}</td>
                                        <td>{{ $arecord->akama_no }} <br> {{$arecord->akama_expire_date}} </td>
                                        <td>{{ $arecord->spons_name }}</td>  
                                        <td>{{ $arecord->mobile_no }}</td>                                                                     
                                        <td>{{ $arecord->catg_name }} <br> {{ $arecord->proj_name }} </td>
                                        <td>{{ $arecord->lev_reas_name }}</td>  
                                        <td>{{ $arecord->start_date }} <br> {{$arecord->end_date}}</td>
                                        <td>{{ $arecord->leav_days }} Days</td>
                                        <td>{{ $arecord->appl_date }}</td>     
                                        <td>{{ $arecord->description == null ? "": $arecord->description }}</td>                                  
                                        <td>{{ $arecord->admin_comments == null ? "" : $arecord->admin_comments}}</td> 
                                        <td>{{ $arecord->status_title }}</td>                                          
                                        
                                        <td>
                                            @can('leave_application_update')
                                            <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#leave_application_edit_modal" data-id="{{$arecord->leav_auto_id}}">
                                            <i class='fas fa-edit'></i> </a>|
                                            @endcan 
                                            @can('leave_application_rejection')
                                            <a href="#" onclick="rejectALeaveApplication({{ $arecord->leav_auto_id }})" title="Delete"> <i class="fa fa-trash fa-lg delete_icon"></i></a>|                            
                                            @endcan 
                                            <!--<a target="_blank" href="{{URL::to($arecord->leave_paper) }}">  <i class="fa fa-eye"></i> </a> -->
                                           <a target="_blank" href="{{ config('app.aws_s3_bucket_url').$arecord->leave_paper }}">  <i class="fa fa-eye"></i> </a> 
                                        </td>
                                    </tr>
                                  @empty
                                      <p class="data_not_found">Data Not Found</p>
                                  @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Leave Applciation Update Modal -->
<div class="modal fade" id="leave_application_edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Leave Application Update Form<span class="text-danger" id="errorData"></span></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
            <form id="leave_application_update" method="POST" action="{{route('leave.application.details.update')}}" >
            @csrf
                <div class="modal-body">
                
                        <input type="hidden" id="modal_emp_auto_id" name="modal_emp_auto_id" value="">             
                        <input type="hidden" id="modal_leave_auto_id" name="modal_leave_auto_id" value="">               
                        
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Employee Details</label>
                            <div class="col-sm-8">                                  
                                <span id ="employee_details" style="color:red"> </span>                             
                            </div>
                        </div> 

                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-2 control-label text-right">Project: </label> 
                            <div class="col-sm-10">
                                <label class="control-label" style="font-weight: 100;"  id="project_name_lbl"></label>                             
                            </div>
                        </div>     
                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-2 control-label text-right">Mobile:</label> 
                            <div class="col-sm-4">
                                <label class="control-label" style="font-weight: 100;"  id="mobile_no_lbl"></label>                             
                            </div>
                            <label class="col-sm-2 control-label text-right">Iqama:</label> 
                            <div class="col-sm-4">
                                <label class="control-label" style="font-weight: 100;" id="iqama_no_lbl"></label>                             
                            </div>
                        </div>  
                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-3 control-label text-right">Reference By: </label> 
                            <div class="col-sm-9">
                                <label class="control-label" style="font-weight: 100;"  id="reference_by_lbl"></label>                             
                            </div>
                        </div> 

                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-3 control-label text-right">Remarks: </label> 
                            <div class="col-sm-9">
                                <label class="control-label" style="font-weight: 100;"  id="description_lbl"></label>                             
                            </div>
                        </div> 
                        
                         <div class="form-group row custom_form_group"> 
                            <label class="col-sm-2 control-label text-right">App. Date</label>
                            <div class="col-sm-4">
                            <input type="date" id="appl_date"  name="appl_date" class="form-control"
                                max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" readonly>
                            </div>  
                            
                            <label class="col-sm-2 control-label text-right">Days </label>
                            <div class="col-sm-4">
                            <input type="text" class="form-control typeahead" 
                            name="leave_days" id="leave_days" >
                            </div>
                        </div> 
    

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Leave From</label>
                            <div class="col-sm-4">
                                <input type="date" id="leave_start_date" name="leave_start_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required >
                            </div>
                            
                            <label class="col-sm-2 control-label">To</label>
                            <div class="col-sm-4">
                                <input type="date" id="end_date"  name="end_date" class="form-control"
                                value="{{date('Y-m-d') }}" required>
                            </div>
                        </div> 
                        
                        <div class="form-group row custom_form_group">                    
                                <label class="col-sm-2 control-label">Type</label>
                                <div class="col-sm-4"> 
                                    <select class="form-control" name="leave_reason_id" id="leave_reason_id"  required>
                                    @foreach($leave_reasons as $arecord)
                                        <option value="{{ $arecord->lev_reas_id}}">{{ $arecord->lev_reas_name }}</option>
                                    @endforeach
                                    </select>                             
                                </div>

                                <label class="col-sm-2 control-label text-left">Status:<span class="req_star">*</span></label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="application_status" id="application_status" >                                         
                                        @foreach($application_status as $ar)
                                        <option value="{{ $ar->leav_sta_auto_id}}">{{ $ar->status_title }}</option>
                                    @endforeach
                                    </select>
                                </div>
                        </div>
                        
                        
                        <div class="form-group row custom_form_group">                 
                            
                            <label class="col-sm-2 control-label text-right">Comments:</label>
                            <div class="col-sm-10"> 
                                <textarea class="form-control" rows="5" placeholder="Comments Here"   id="admin_comments"
                                name="admin_comments"></textarea>
                            </div>                      
                        </div> 
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Exit Paper</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="exit_paper" id="imgInp4" >
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                        </div>
                        <br><br>
                    
                    <button type="submit" id="updatebtn" name="updatebtn" onclick=""  class="btn btn-success"  >Update</button>
                
                </div>              
            </form>
      </div>
  </div>
</div>



<!-- date and days -->
<script type="text/javascript">
function EmpLeaveDays(){
  var start_date = $("#leave_start_date").val();
  var end_date = $("#end_date").val();

  var st_date = new Date(start_date);
  var en_date = new Date(end_date);
  var total = (en_date - st_date);
  var days = total/1000/60/60/24;

  if(en_date < st_date){
    $("span[id='error_invalid_date']").html('Invalid Date!');
  }else{
    $("#show_days").val(days).prop('disabled', true);
    $("#leave_start_date").click(function(){
      $("#show_days").val(days).prop('disabled', false);
    });
  }
}

    // Open Modal For Update leave application
    $(document).on("click", "#leave_application_edit_button", function(){
          var leav_auto_id = $(this).data('id');
       //   alert(leav_auto_id)
          $.ajax({
              type: "GET",
              url: "{{ route('a.leave.application.details') }}",
              data: {leav_auto_id: leav_auto_id},
              datatype:"json",
              success: function(response){
                  if(response.status == 200){  
                     
                      var arecord = response.data;
                      $('#modal_emp_auto_id').val(arecord.emp_auto_id);
                      $('#modal_leave_auto_id').val(arecord.leav_auto_id);                    
                      var salary_type = arecord.hourly_rent == 1 ? "Hourly":"Basic"
                      $('#employee_details').text(arecord.employee_id+", "+arecord.employee_name+", Salary Type: "+salary_type);
                      $('#project_name_lbl').text(arecord.proj_name);
                      $('#mobile_no_lbl').text(arecord.mobile_no);  
                      $('#iqama_no_lbl').text(arecord.akama_no);  
                      $('#description_lbl').text(arecord.description == null ? "" : arecord.description);                                          
                      $('#leave_reason_id').val(arecord.lev_reas_id);
                      $('#leave_days').val(arecord.leav_days+" Days");
                      $('#appl_date').val(arecord.appl_date);
                      if(arecord.leave_start_date == null)
                      {
                        $('#leave_start_date').val(arecord.start_date);
                      }
                     else{
                        $('#leave_start_date').val(arecord.leave_start_date);
                     }
                     $('#admin_comments').text(arecord.admin_comments == null ? "": arecord.admin_comments)
                      $('#end_date').val(arecord.end_date);
                      $('#application_status').val(arecord.appl_status);
                      $('#reference_by_lbl').text(arecord.reference_by);                       
                     
                    //   $('#modal_slh_month').val(arecord.approval_date);         
                    //   $('#modal_slh_year').val(arecord.appl_status); 
                     
                     // document.getElementById('salary_paid__status').checked = true;

                  }else{
                       
                  }                   
              },
              error:function(response){
                  showSweetAlert('Operation Failed ','error');  
              }
          })
    });


        // Modal View Showing Event
    $('#leave_application_edit_modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
        $('#new_food_amount').select();
    });

    //  Modal View Hidden Event, Reset Modal Previous Data 
    $('#leave_application_edit_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
     // debugger;
    })

    function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })            
                Toast.fire({
                    type: type,
                    title: message,
                })
    }

    function rejectALeaveApplication(leav_auto_id){

        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: 'GET',
                        url: "{{  url('admin/leave/application/application-rejection') }}/" +leav_auto_id,                    
                        dataType: 'json',
                        success: function (response) {
                            if(response.status == 200){
                                showSweetAlertMessage('success', response.message);  
                                window.location.reload();                              
                            }else {
                                showSweetAlertMessage('error', response.message);
                            }                           
                        },
                        error:function(response){
                            showSweetAlertMessage('error', "Operation Failed, Please Try Again");
                        }
                    });


                }
            }); 

    } 



</script>

@endsection
