@extends('layouts.admin-master')
@section('title') Unapproval Iqama list @endsection
@section('internal-css')
<style media="screen">
    .approve_button {
        background: #2B4049;
        color: #fff;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 5px;
    }

    .approve_button:hover {
        color: #fff;
    }
</style>
@endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employees Unapproval Iqama List </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Unapproval Iqama</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
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
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Employee</th>
                                        <th>Basic/Rate</th>                                         
                                        <th>Jawazat</th>
                                        <th>Mak.Aml</th>
                                        <th>VISA</th>
                                        <th>Insur.</th>
                                        <th>Others</th>
                                        <th>J.Penalty</th>
                                        <th>Total</th>
                                        <th>Duration</th>
                                        <th>Renew/Expire Date</th>
                                        <th>Remarks</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $item->employee_id }}</td>
                                        <td>{{Str::words($item->employee_name,3)}} <br> {{ $item->akama_no }}</td>
                                        <td>{{ $item->basic_amount }}/ {{$item->hourly_rent}}</td>                                        
                                        <td>{{ $item->jawazat_fee }}</td>
                                        <td>{{ $item->maktab_alamal_fee }}</td>
                                        <td>{{ $item->bd_amount }}</td>
                                        <td>{{ $item->medical_insurance }}</td>
                                        <td>{{ $item->others_fee }}</td>
                                        <td>{{ $item->jawazat_penalty }}</td>
                                        <td>{{ $item->total_amount }}</td>
                                        <td>{{ $item->duration }} Months</td>
                                        <td>{{ $item->renewal_date}} , {{ $item->iqama_expire_date}}</td>
                                        <td>{{ $item->remarks }}</td>
                                        <td>
                                            <a href="{{ route('edit-iqamarenewal-fee',$item->IqamaRenewId ) }}" title="edit" class="edit_button"><i class="fa fa-edit"></i></a>
                                            &nbsp&nbsp<a href="{{ route('pending-iqamarenewal-fee-view',$item->IqamaRenewId) }}" title="View" target="_blank" class="view_button"><i class="fa fa-eye"></i></a>
                                            <br><br> <a href="{{ route('pending-iqamarenewal-fee-approved',$item->IqamaRenewId) }}" title="Approve" id="approved" class="approve_button"><i class="fa fa-thumbs-up"></i></a>
                                            <a href="{{ route('anemp.iqama.renewal.expense.delete.before.approval',$item->IqamaRenewId) }}"
                                                title="Delete" id="delete" class="approve_button">Delete</a>
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

{{-- Modal for view Unapproval Emplyoee Informations  --}}

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModal"> Add New Permission </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    function iqamaDetailsView(){
      var emplId = $('input[name=emp_id]').val();
            alert(emplId);

    }
</script>
@endsection
