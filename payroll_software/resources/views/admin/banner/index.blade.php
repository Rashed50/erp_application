@extends('layouts.admin-master')
@section('title') Banner Information @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Banner Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Banner Information</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i>Banner List</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('add-banner-info') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-plus-circle mr-2"></i>Add New Banner</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-7">
                        @if(Session::has('success_soft'))
                          <div class="alert alert-success alertsuccess" role="alert">
                             <strong>Successfully!</strong> delete Banner information.
                          </div>
                        @endif

                        @if(Session::has('success_update'))
                          <div class="alert alert-success alertsuccess" role="alert">
                             <strong>Successfully!</strong> update Banner information.
                          </div>
                        @endif

                        @if(Session::has('error'))
                          <div class="alert alert-warning alerterror" role="alert">
                             <strong>Opps!</strong> please try again.
                          </div>
                        @endif
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Subtitle</th>
                                        <th>Description</th>
                                        <th>Caption</th>
                                        <th>Company Name</th>
                                        <th>Entered By</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @forelse($all as $item)
                                    <tr>
                                        <td class="image_td">
                                          <img src="{{ asset('uploads/banner/'.$item->ban_image) }}" alt="" class="list_image">
                                        </td>
                                        <td>{{ $item->ban_title }}</td>
                                        <td>{{ $item->ban_subtitle }}</td>
                                        <td>{{ $item->ban_description }}</td>
                                        <td>{{ $item->ban_caption }}</td>
                                        <td>{{ $item->ban_caption }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>
                                            <a href="#" title="view"><i class="fa fa-plus-square fa-lg view_icon"></i></a>

                                            <a href="{{ route('edit-banner-info',[$item->ban_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                            <a href="{{ route('delete-banner-info',[$item->ban_id]) }}" title="delete" id="delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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
@endsection
