@extends('layouts.admin-master')
@section('title') Inv. Sub Store @endsection
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
        <h4 class="pull-left page-title bread_title">Sub Store Informations</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Inventore Sub Store </li>
        </ol>
    </div>
</div>

<!-- Message Display Section !-->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
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




<!-- Employee Search UI !-->

<div class="row">

    <div class="col-md-12">
        <div class="card">
            <!-- Single Employee Shift Change With Modal Start !-->
            <div class="card-body">
                <div class="row">
                    {{-- Modal Body Start --}}
                    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>New Sub Store Name Add</h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                        id="closeModal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">

                                        <form class="form-horizontal" id="Inventory-item-brand-form" method="post"
                                            action="{{ route('insert.inventory-sub-store-info') }}">
                                            @csrf
                                            <div class="card">
                                                <div class="card-body card_form">

                                                    <div
                                                        class="form-group row custom_form_group{{ $errors->has('sub_store_name') ? ' has-error' : '' }}">
                                                        <label class="control-label col-sm-4">Sub Store Name:</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                name="sub_store_name" id="invSubStoreName"
                                                                value="{{old('sub_store_name')}}"
                                                                placeholder="Inventory Sub Store Name Here" required
                                                                autofocus>
                                                            @if ($errors->has('sub_store_name'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('sub_store_name') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-primary">Add Info</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- Modal Body End --}}
                    <!-- <div class="col-md-6"></div> -->
                </div>
            </div>
            <!-- Single Employee Shift Change With Modal End !-->

            {{-- Project Wise All Employee Search Form --}}
            <div class="card-body row card_form">
                <div class="col-md-6"></div>
                <div class="col-md-2"></div>
                <div class="col-md-4 text-center">
                    <div class="form-group row custom_form_group">
                        <div class="col-sm-6">
                            <button class="btn btn-primary icon" type="button" data-toggle="modal"
                                data-target="#cartModal" id="">Add Sub Store</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Sub Store List -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Sub Store Name</th>
                                        <th>Sub Store Code</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $item->sub_store_name }} </td>
                                        <td> {{ $item->sub_store_code }} </td>
                                        <td> {{ $item->user->name }} </td>
                                        <td>
                                            @if ($item->sub_store_status == 1)
                                            <span class="badge badge-pill badge-success">Available</span>
                                            @else
                                            <span class="badge badge-pill badge-danger">Not Available</span>
                                            @endif
                                        </td>
                                        {{-- <td> {{ Carbon\Carbon::parse($item->assign_date)->format('D, d F Y') }}
                                        </td> --}}

                                        <td>

                                            <a href="#" title="Edit" class="approve_button" data-toggle="modal"
                                                data-target="#demoModalEdit{{ $item->sub_store_id }}">Edit</a>

                                        </td>


                                        <!-- Edit Form Modal Example Start -->
                                        <div class="modal fade" id="demoModalEdit{{ $item->sub_store_id }}"
                                            value="{{ $item->sub_store_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="card-title">Sub Store Name Update</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" id="closeModal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <form class="form-horizontal" id="employee-info-form"
                                                                method="post"
                                                                action="{{ route('update.inventory-sub-store-info') }}">
                                                                @csrf
                                                                <div class="card">
                                                                    <div class="card-body card_form">
                                                                        <input type="hidden" name="sub_store_id"
                                                                            value="{{$item->sub_store_id }}">

                                                                        <input type="hidden" name="sub_store_code"
                                                                            value="{{$item->sub_store_code }}">


                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('sub_store_name') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-sm-4">Sub
                                                                                Store Name:</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control"
                                                                                    name="sub_store_name"
                                                                                    id="invSubStoreNameUp"
                                                                                    value="{{ $item->sub_store_name }}"
                                                                                    placeholder="Inventory Sub Store Name Here"
                                                                                    required autofocus>
                                                                                @if ($errors->has('sub_store_name'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('sub_store_name')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

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



<script type="text/javascript">

    $(document).ready(function () {
        // Make Company Name Upper Case from modal
        $('#invSubStoreName').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });

        // Make Company Name Upper Case from modal
        $('#invSubStoreNameUp').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
    });

</script>

@endsection
