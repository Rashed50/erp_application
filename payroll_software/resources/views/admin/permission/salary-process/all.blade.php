@extends('layouts.admin-master')
@section('title') Salary Process Permission @endsection
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

    .td__serial_no {
        font-size: 11px;
        color: black;
        font-weight: 100;
        text-align: center;
    }
        
    .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    }

    .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }

</style>



@endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary Process Permission </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Process Permision</li>
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


<!-- Process New Permission Modal Start -->
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
                <form method="POST" action="{{ route('salary.process.permission.update.or.insert') }}">
                    @csrf

                    {{-- Month List --}}
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Salary
                            Month:</label>
                        <div class="col-sm-8">
                            <select class="form-select" name="month" required>
                                @foreach($month as $item)
                                <option value="{{ $item->month_id }}" {{ $item->month_id == $item->month
                                    ? 'selected' :'' }}>{{
                                    $item->month_name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('month'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('month')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    {{-- Year List --}}
                    <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Salary
                            Year:</label>
                        <div class="col-sm-8">
                            <select class="form-select" name="year">
                                @foreach(range(date('Y')-1,
                                date('Y')) as $y)
                                <option value="{{$y}}" {{$y==$item->
                                    year ? 'selected' : ''}}> {{$y}}
                                </option>
                                @endforeach
                            </select>

                            @if ($errors->has('year'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('year')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('lock_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Expiration Date:</label>
                        <div class="col-sm-8">
                            <input type="date" name="lock_date" value="<?= date("Y-m-d",strtotime(' + 1 years')) ?>" class="form-control">


                            @if ($errors->has('lock_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{
                                    $errors->first('lock_date')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">                        
                            <label class="control-label col-sm-4">Lock/Unlock : </label>
                            <label class="switch"> 
                                <input type="checkbox" name="lock_checkbox" id="lock_checkbox" >
                                <span class="slider round"></span>
                            </label>
                    </div>

                    <br>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">SAVE INFO</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Employee Info View With Modal End -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row"> 
                    <div class="col-md-6 text-right">
                        <a data-toggle="modal" data-target="#loginModal"
                            class="btn btn-md btn-primary waves-effect card_top_button"><i
                                class="fa fa-plus-circle mr-2"></i>Add New Permission</a>
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
                                        <th>Access Year</th>
                                        <th>Access Month</th>                                        
                                        <th>Lock Status</th>
                                        <th>Expire At</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->year }}</td>
                                        <?php
                                             
                                             $monthName = date('F', mktime(0, 0, 0, $item->month, 10));
                                        ?>                                        
                                        <td>{{ $monthName}}</td>
                                        
                                        <td>
                                            @if ($item->is_Lock == 1)
                                            <span class="badge badge-pill badge-success">Lock</span>
                                            @else
                                            <span class="badge badge-pill badge-danger">Open</span>
                                            @endif
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($item->lock_date)->format('D, d F Y') }}</td>

                                        <td>

                                            <a href="#" title="Edit" class="approve_button" data-toggle="modal"
                                                data-target="#demoModalEdit{{$item->id}}">Edit</a>

                                        </td>


                                        <!-- Edit Form Modal Example Start -->
                                        <div class="modal fade" id="demoModalEdit{{$item->id}}"
                                            value="{{ $item->id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="card-title">Salary Process Permission Update</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" id="closeModal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <form class="form-horizontal" id="employee-info-form"
                                                                method="post"
                                                                action="{{ route('salary.process.permission.update.or.insert') }}">
                                                                @csrf
                                                                <div class="card">
                                                                    <div class="card-body card_form">
                                                                        <input type="hidden" name="permission_id"
                                                                            value="{{$item->id }}">

                                                                        {{-- Month List --}}
                                                                        <div class="form-group row custom_form_group">
                                                                            <label class="control-label col-sm-4">Salary
                                                                                Month:</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-select"
                                                                                    name="month" required>
                                                                                    @foreach($month as $mt)
                                                                                    <option
                                                                                        value="{{ $mt->month_id }}" {{
                                                                                        $mt->month_id == $item->month
                                                                                        ? 'selected' :'' }}>{{
                                                                                        $mt->month_name }}</option>
                                                                                    @endforeach
                                                                                </select>

                                                                                @if ($errors->has('month'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{ $errors->first('month')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        {{-- Year List --}}
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-sm-4">Salary
                                                                                Year:</label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-select"
                                                                                    name="year">
                                                                                    @foreach(range(date('Y'),
                                                                                    date('Y')-1) as $y)
                                                                                    <option value="{{$y}}" {{$y==$item->
                                                                                        year ? 'selected' : ''}}> {{$y}}
                                                                                    </option>
                                                                                    @endforeach
                                                                                </select>

                                                                                @if ($errors->has('year'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{ $errors->first('year')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row custom_form_group{{ $errors->has('lock_date') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-sm-4">Lock
                                                                                Date:</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="date" name="lock_date"
                                                                                    value="{{$item->lock_date}}"
                                                                                class="form-control">

                                                                                @if ($errors->has('lock_date'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('lock_date')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row custom_form_group">                        
                                                                                <label class="control-label col-sm-4">Lock/Unlock : </label>
                                                                                <label class="switch"> 
                                                                                    <input type="checkbox" name="lock_checkbox" id="lock_checkbox"                                                                                     
                                                                                          {{ $item->is_Lock == 1 ? 'checked' : '' }}>
                                                                                    <span class="slider round"></span>
                                                                                </label>
                                                                        </div>

                                                                        <br>

                                                                        <div class="text-center">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Update</button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Edit Form Modal Example End-->

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



@endsection

@section('script')
<script>


</script>

@endsection
