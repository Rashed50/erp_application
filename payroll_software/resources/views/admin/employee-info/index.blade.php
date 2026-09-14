@extends('layouts.admin-master')
@section('title') Employee list @endsection
 
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Information Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employees</li>
        </ol>
    </div>
</div>
<div class="row">   
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">  <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> {{'Totall Active Emlpoyees '.': '.$total_active_emp }} </h3></div>
                    <div class="col-md-3">
                        <div class="form-group row custom_form_group">                          
                                <input type="text" class="form-control" placeholder="Search By Employee ID" name="employee_id" id="employee_id" autofocus>                            
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="searchingEmployee()"  style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                    <div class="clearfix"></div>
                </div>                
            </div>
            <div class="card-body">
                <div class="row d-none" id="employee_list_section">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="employeeTable" class="table table-bordered table-hover custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Iqama No</th>
                                        <th>Sponsore</th>
                                        <th>Project</th>
                                        <th>Trade</th>
                                        <th>Country</th>
                                        <th>Type</th>
                                        <th>Photo</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody id="employee_list_table_body">
                                    {{-- @foreach($all as $key=> $item)
                                    <tr>
                                        <td>{{ $item->employee_id }}</td>
                                        <td>{{Str::words($item->employee_name,3)}}</td>
                                        <td>{{ $item->akama_no }}</td>
                                        <td>{{ $item->sponsor->spons_name ?? ''}}</td>
                                        <td>{{ $item->project->proj_name ?? ''}}</td>
                                        <td>{{ $item->category->catg_name ?? '' }}</td>
                                        @if($item->country_id == NULL)
                                        <td>Not Assigned</td>
                                        @else
                                        <td>{{ $item->country->country_name }}</td>
                                        <!--country-->
                                        @endif
                                        <td>{{ $item->emp_type_id == NULL ? 'Not Assigned' : $item->employeeType->name }}</td>
                                        <!--employeeType-->
                                        <td>
                                            <img src="{{ asset($item->profile_photo) }}" alt="Not Assigned" width="80">
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/employee/view/'.$item->emp_auto_id) }}" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a>
                                            <a href="{{ url('admin/employee/edit/'.$item->emp_auto_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>           
        </div>    
</div>

 

<script>
 

$('#employee_id').keydown(function(e) {
    if (e.keyCode == 13) {
        searchingEmployee();
    }
})

 function showMessage(message,type){
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
 
 function searchingEmployee() {

        var searchType = 'employee_id' ;// $('#searchBy').find(":selected").val();
        var searchValue = $("#employee_id").val();
        
        if (searchValue.length === 0) {
            $('#employee_list_section').removeClass('d-block').addClass('d-none');
            showMessage('Please Input Valid Data');
            return;
        }  
        $.ajax({
                type: 'POST',
                url: "{{ route('employee.searching.searching-with-multitype.parameter') }}",  
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function(response) { 
                        
                        $('#employee_list_section').removeClass('d-none').addClass('d-block');
                        var rows = "";
                        var counter = 1;
                       
                        $.each(response.findEmployee, function (key, value) {
                            var editurl = "{{ url('admin/employee/edit')}}" + "/" + value.emp_auto_id;
                          //  var viewurl = "{{ url('admin/employee/view')}}" + "/" + value.emp_auto_id;
                            rows += `
                                <tr>                                    
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.akama_no} </td>
                                    <td>${value.spons_name} </td>
                                    <td>${value.proj_name} </td>
                                    <td>${value.catg_name} </td>                                   
                                    <td> ${value.country_name}</td>
                                    <td> ${value.name }</td> 
                                    <td></td>
                                    <td>
                                        <a href="${editurl}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                    </td>                                  
                                </tr>
                                `
                        });
                         $('#employee_list_table_body').html(rows);                        
                          
                     
                }, // end of success
                error:function(response){
                    showMessage('Network Error, Please Refresh Website','error');
                }
        });  
          
}
</script>

@endsection
 