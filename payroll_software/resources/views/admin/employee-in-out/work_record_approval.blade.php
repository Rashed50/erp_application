@extends('layouts.admin-master')
@section('title')Work Approval @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Work Record Approval </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Work Record Approval</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
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
    <div class="col-md-2"></div>
</div>




<!-- Employee Info View With Modal End -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form group row">
                            <div class="col-sm-3"></div>
                            <label for="month" class="col-sm-1"> Work Year</label>
                            <div class="col-sm-2">                                
                                <select name="year" id="year" class="form-select" >
                                    @foreach(range(date('Y'), date('Y')-1) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="month" class="col-sm-1"> Month</label>
                            <div class="col-sm-2">
                                <select name="month" id="month" class="form-select">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">Auguest</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>                            
                        </div>
                                           
                    </div>                     
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
                                        <th>Month, Year</th>
                                        <th>Total Manpower</th>
                                        <th>Basic Hours</th>
                                        <th>Overtime</th>
                                        <th>Total Hours</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody id="montly_work_records">
                                    @foreach($records as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->proj_name }}</td>
                                        <td> {{ date('F', mktime(0, 0, 0, $item->emp_io_month, 10)) }}, {{ $item->emp_io_year }} </td>
                                        <td>{{ $item->total_emp }}</td>
                                        <td>{{ $item->basic_hours }}</td>                                        
                                        <td>{{ $item->over_time }}</td>
                                        <td>{{ $item->basic_hours + $item->over_time }}</td>
                                        <td>{{$item->approval_status->name}}</td>
                                        <td>
                                            
                                            <input type="hidden" value="{{$item->approval_status->value}}" id="appr_status-{{$item->atten_appro_auto_id}}">  
                                            @if($item->approval_status->value == 0)
                                                <a href="#" onclick="apporveOfMonthlyWorkRecord({{ $item->atten_appro_auto_id }})" class="approve_button"><i class="fa fa-thumbs-up"></i></a>
                                            
                                            @else
                                                @can('month_work_record_approval_edit')
                                                    <a href="#" onclick="apporveOfMonthlyWorkRecord({{ $item->atten_appro_auto_id }})" class="approve_button"><i class="fa fa-thumbs-up"></i> Edit</a>
                                                @endcan
                                            @endif
                                            

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

 
<script>

    function apporveOfMonthlyWorkRecord(atten_appro_auto_id) {      
       var approved_status = ($('#appr_status-'+atten_appro_auto_id).val()) == true ? 0:1;

        swal({
            title: 'Do you want to Approve ?', 
            icon: "warning",
            buttons: ["Cancel", "OK"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if(willDelete){
                $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            atten_appro_auto_id:atten_appro_auto_id,
                            approved_status:approved_status,
                        },
                        url: "{{ route('monthly.attendance.approval.request2') }}",
                        success: function (response) {                      
                            
                            if(response.status == 200){
                                showSweetAlertMessage('success',response.message);
                                location.reload();
                            }else {
                                showSweetAlertMessage('error',response.message);
                            }                            
                        },
                        error:function(){
                            showSweetAlertMessage('success','sdfdsfds');
                        }

                });
            }else {

            }
        });

        
    }

    
    $(document).ready(function() {     
        $('#month').change(function(){

            var month = $('#month').val();
            var year = $('#year').val();
       
             $.ajax({
                type:"GET",
                url:"{{route('monthly.attendance.work.record.search')}}",
                data:{
                    month:month,
                    year:year,
                },
                success:function(response){
                     
                        var counter = 1;
                        var records = "";

                        $.each(response.data, function(key, value) {  
                            counter++; 
                            var my = value.emp_io_month+','+value.emp_io_year  ;
                                                                                         
                            records +=
                                    '<tr>'+
                                        '<td>'+counter+'</td>'+                                       
                                        '<td>'+value.proj_name+'</td>'+
                                        '<td>'+  my  +'</td>' +
                                        '<td>'+value.total_emp+'</td>' +
                                        '<td>'+value.basic_hours+'</td>' +
                                        '<td>'+value.over_time+'</td>' +
                                        '<td>'+(value.basic_hours + value.over_time)+'</td>' +
                                        '<td>'+value.approval_status+'</td>';
    
                                        var edit_url = "#" ;// "{{  url('admin/edit/iqama-anual/fee') }}/" + value.atten_appro_auto_id;                                 

                                        if(value.approval_status == 0) {                                           
                                            records += '<td>' + '<a  href=' + edit_url+'><i class="fa fa-pencil-square fa-lg edit_icon"></i></a></td>';                                      
                                            
                                        } else  if(value.approval_status == 1) {
                                            @can('month_work_record_approval_edit')
                                            records += '<td>' + '<a href=' + edit_url+' onclick="apporveOfMonthlyWorkRecord('+value.atten_appro_auto_id+')" class="approve_button" ><i class="fa fa-thumbs-up"></i></a></td>';
                                            records += '<td><input type="hidden" value='+value.atten_appro_auto_id+' id="appr_status-'+value.atten_appro_auto_id+'">' +'</td>';
                                            @endcan
                                            
                                        }
                                        
                                records += '</tr>';

                        }); 
                         $('#montly_work_records').html(records); 
                },
                error:function(){
                    alert(404);
                },
             });
        });
    });
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
</script>

@endsection

