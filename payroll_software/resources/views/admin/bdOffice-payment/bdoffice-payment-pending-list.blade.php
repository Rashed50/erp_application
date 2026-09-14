@extends('layouts.admin-master')
@section('title') Office Payment @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Payment Pending Employee List </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
 


<!-- Payment Pending Employee list -->

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
                                        <th>Employee ID</th>
                                        <th>Emp. Name</th>
                                        <th>Emp. Type</th>
                                        <th>Approved Amount</th>
                                        <th>Manage</th>

                                    </tr>
                                </thead>
                                <tbody id="workrecords">
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->employee->employee_id }}</td>
                                        <td>{{ $item->employee->employee_name }}</td>
                                        <td>{{ $item->employee->employeeType->name }}</td>
                                        <td>{{ $item->approved_amount }}</td>
                                        <td>
                                            <a href="{{ route('bdoffice-payment-pending.employee-details',$item->bdofpay_auto_id ) }}"
                                                title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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

    
    function deleteEmpMothlyWorkRecord(id) {
        // alert(id);

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
                        url: "{{  url('admin/delete/work') }}/" + id,
                        dataType: 'json',
                        success: function (response) {
                            window.location.reload();
                        }
                    });


                }
            });


    }



</script>
@endsection
