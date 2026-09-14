<div class="card" style="width: 18rem; padding-top: 10px">
  @if( $edit->employee->profile_photo == "")
    <img class="card-img-top user-profile-image" src="{{asset('uploads/employee')}}/logo_icon.png" alt="Card image cap"/>
  @else
    <img class="card-img-top user-profile-image" src="{{ asset($edit->employee->profile_photo) }}" alt="Card image cap"/>
  @endif

    <ul class="list-group list-group-flush">
      <a href="{{ route('add-manage.user') }}" class="btn btn-primary btn-sm btn-block"> Go Back </a>
      <li class="btn-primary btn-sm btn-block" style="text-align:center">Be able to Change Role </li>
      <li class="btn-primary btn-sm btn-block" style="text-align:center">Be able to Change Password</li>
    </ul>
</div>
