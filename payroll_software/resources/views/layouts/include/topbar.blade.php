<div class="topbar">
    <div class="topbar-left">
        @php

                                $profile_photo = Auth::user()->profile_image;
                            @endphp

        <div class="text-center">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img class="thumb-md rounded-circle" src="{{ asset('contents/admin')}}/assets/images/circle_logo.png" />
                 {{-- <i class="md md-terrain"></i> --}}
                <span>{{ Auth::user()->branch_office_id == 1 ? "ASLOOB" : "ASLOOB"  }}</span>
            </a>
{{-- <script src="{{asset('contents/admin')}}/assets/js/accounts/journal_info.js"></script> --}}
        </div>
    </div>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <ul class="list-inline menu-left mb-0">
                <li class="float-left">
                    <a href="#" class="button-menu-mobile open-left">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>

            </ul>

            <ul class="nav navbar-right float-right list-inline">
                 <!-- Rop Right Profile Menu -->
                <li class="dropdown open">
                    <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true">
                            @php

                                $profile_photo = Auth::user()->profile_image;
                            @endphp
                            {{Auth::user()->name}}
                            @if($profile_photo == null || $profile_photo == "")
                            <img class="thumb-md rounded-circle" src="{{asset('uploads/employee')}}/avatar-1.jpg" alt="user-photo" />
                            @else
                                <img class="thumb-md rounded-circle" src="{{ asset($profile_photo) }}" />
                            @endif

                    </a>
                    <!-- Top Right Menu -->
                    <ul class="dropdown-menu">
                        <li><a href="#" class="dropdown-item"> {{ Auth::user()->name }} </a></li>
                        <li><a href="{{route('user.user-profile')}}" class="dropdown-item"><i class="md md-face-unlock mr-2"></i> Profile</a></li>
                        <!--<li><a href="javascript:void(0)" class="dropdown-item"><i class="md md-settings mr-2"></i> Settings</a></li>-->
                        <!--<li><a href="javascript:void(0)" class="dropdown-item"><i class="md md-lock mr-2"></i> Lock screen</a></li>-->
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item"><i class="md md-settings-power mr-2"></i> Logout</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </li>
                <!-- Full Screen Button -->
                <li class="d-none d-sm-block">
                    <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="md md-crop-free"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</div>
