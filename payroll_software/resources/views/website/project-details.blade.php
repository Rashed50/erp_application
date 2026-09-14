@extends('layouts.website')
@section('title') ..::ASLOOB || Project Details::.. @endsection
@section('content')
    <!--====================  project details area ====================-->
    <div class="project-section space__bottom--r120" style="margin:top 100px">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-12 space__bottom--30">
                    <div class="project-information">
                        <h4 class="space__bottom--15">Project Information</h4>
                        <ul>
                            <li><strong>In Charge:</strong> <a href="#"> @if( $proj->proj_Incharge_id == NULL) Not Assigned @else {{ $proj->employee->employee_name }} @endif </a></li>
                            <li><strong>Start:</strong> {{ $proj->starting_date }}</li>
                            <li><strong>Completed:</strong> {{ $proj->proj_deadling }}</li>
                            <li><strong>Value:</strong> {{ $proj->proj_budget }}</li>
                            <li><strong>Project Code:</strong> {{ $proj->proj_code }}</li>
                            <!-- <li><strong>Sector:</strong> <a href="project.html">Tunnel</a>, <a href="project.html">Transport</a></li> -->
                            <li><strong>Location:</strong> {{ $proj->address }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8 col-12 space__bottom--30">
                    <div class="project-details">
                        <h3 class="space__bottom--15">{{ $proj->proj_name }}</h3>
                        <p>{{ $proj->proj_description }}</p>
                    </div>
                </div>
                <div class="col-12">

                    <div class="row row-5 image-popup">
                      @foreach($muliple as $image)
                        <div class="col-xl-2 col-lg-4 col-sm-6 col-12 space__top--10">
                            <a href="{{ asset($image->photo_path) }}" class="gallery-item single-gallery-thumb"><img src="{{ asset($image->photo_path) }}" class="img-fluid" alt=""><span class="plus"></span></a>
                        </div>
                      @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================  End of project details area  ====================-->

@endsection
