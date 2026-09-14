@extends('layouts.admin-master')
@section('title') Pending Salary list @endsection
@section('content')



<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Advance Paper Upload</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Upload</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}} </strong> 
        </div>
        @elseif(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{Session::get('error')}} </strong>  
        </div>
        @endif        
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Searching Form -->
<div class="row">
    <div class="card-header">  
        <form method="post" action="{{ route('advance.paper.upload.request') }}" id="advance_paper_upload_form" enctype="multipart/form-data">
            @csrf              
                 
                <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">Advance Date</label>
                    <div class="col-sm-2">
                        <input type="date" id="advance_date"  name="advance_date" value="<?= date("Y-m-d") ?>" class="form-control">
                    </select>
                    </div>
                    <label class="col-sm-2 control-label">Remarks</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="remarks" >
                    </div>                     
                </div>    
                <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">Advance Paper</label>
                    <div class="col-sm-4">
                        <input type="file" class="form-control" id ="advance_file_name" name="file_name">
                    </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" id ="upload_button" class="btn btn-primary waves-effect">Upload</button>
                    </div>  
                    <div class="col-sm-2"> 
                        <button type="button"  id ="search_button"  onclick="searchUploadedAdvancePaper()" class="btn btn-primary waves-effect">Search</button>
                    </div>                    
                </div>         
        </form>
    </div>
</div>

<!-- Searching List -->
<div class="row d-none" id="advance_paper_list_section">      

    <div class="table-responsive">
                <span id="data_not_found" class="d-none">Data Not Found!</span>
                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Advance Date</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Remarks</th>
                            <th>Manage</th>                           
                        </tr>
                    </thead>
                    <tbody id="advance_paper_list_table"></tbody>
                </table>
    </div>
   
</div>


<script>
    // Datepicker 

    function showMessage(message,operationType){
         //  start message
         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
           
                Toast.fire({
                    type: operationType,
                    title: message
                })
            
    }

      $('#advance_paper_upload_form').submit(function(event) {

            function advancePaperUploadRequest( ){
                e.preventDefault();
                let formData = new FormData(this);    
                          
                $.ajax({
                        type:"POST",
                        url:"{{route('advance.paper.upload.request')}}",    
                        dataType:"multipart/form-data",
                        data: formData,
                        success:function(response){
        
                                if(response.status == 200){        
                                    reset();
                                    showMessage(response.message,'success');
                                }else {
                                    showMessage(response.message,'error');
                                }                       
                            },
                        error:function(reponse){
                                showMessage("Operation Failed, Please Try Aggain",'error');
                        }
                    });


            }
      });
      
      
      function getMonthName(monthid){
          if(monthid == 1)
          return "January";
          else if(monthid == 2)
          return "February";
          else if(monthid == 3)
          return "March";
          else if(monthid == 4)
          return "April";
          else if(monthid == 5)
          return "May";
          else if(monthid == 6)
          return "June";
          else if(monthid == 7)
          return "July";
          else if(monthid == 8)
          return "August";
          else if(monthid == 9)
          return "September";
          else if(monthid == 10)
          return "October";
          else if(monthid == 11)
          return "November";
          else if(monthid == 12)
          return "December";
      }


      function searchUploadedAdvancePaper(){
        var advace_date = $('#advance_date').val();      
        $.ajax({
            type:"GET",
            url: "{{  url('admin/advance/paper/search') }}/" + advace_date,
            success:function(response){   
                if(response.status == 200){           

                    $("#advance_paper_list_section").removeClass("d-none").addClass("d-block");  
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) {
                        var manthName = getMonthName(value.month);
                        rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td> ${value.advance_date}</td>
                                        <td>${manthName}</td>
                                        <td>${value.year}</td>
                                        <td>${value.remark}</td>
                                        <td><a target="_blank" href="{{ url('${value.file_path}') }}" class="btn btn-success">View </a>
                                        <a target="_blank" href="#" class="btn btn-danger">Delete</a>
                                        </td>
                                          
                                          
                                    </tr>
                                    `
                    });
                    $('#advance_paper_list_table').html(rows);
                }else {
                    showMessage('Data Not Found','error');
                }
            },
            error:function(response){
               // alert(response.message);
                showMessage(response.message,'error');

            }
        });
      }

</script>
@endsection