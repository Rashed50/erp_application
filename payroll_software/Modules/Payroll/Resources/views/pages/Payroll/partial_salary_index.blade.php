@extends('layouts.admin-master')
@section('title', 'Partial Salary')
@section('content')

@section('content')
{{-- <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Partial Salary Histories</h3>
                </div>
                <div class="card-body">
                    {{ json_encode($projects) }}
                    <partial-salary-history-manager
                        :projects="{{ json_encode($projects) }}"
                    ></partial-salary-history-manager>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div id="app">
    <partial-salary-history-manager :projects="{{ json_encode($projects) }}"></partial-salary-history-manager>
</div>
@endsection
