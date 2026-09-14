<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Generate</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">
    <style media="screen">
        a.print-button{
          text-decoration: none;
          background: teal;
          color: #fff;
          padding: 5px 10px;
        }
        p.toEndDate{}
        p.toEndDate strong{ font-size: 14px }
        p.toEndDate span {
          font-size: 14px;
          font-weight: 600;
          margin-left: 2px;
        }
        div.officer-signature{
          display: flex;
          justify-content: space-between;
        }
        /* Salary Table style */
        .salary__table thead{
          background: #2B4049;
        }
        .salary__table thead tr th {
        	padding: 10px !important;
        }
        .salary__table thead tr th span{
        	color: #fff;
          font-size: 13px;
        }
    </style>
</head>
<body>
  <section class="salary">
      <div class="container">
          <!-- salary header -->
          <div class="row align-center">
              <div class="col-md-6">
                  <div class="salary__header">

                  </div>
              </div>
              <div class="col-md-6"></div>
          </div>
          <!-- salary bottom header -->
          <div class="salary__header-bottom">
              <div class="row">
                  <div class="col-md-3">
                    <div class="project_info" style="margin-left:0">
                        <span class="project_name">  <strong>Sponsor Name :</strong> {{ $sponsor }} </span>
                    </div>
                  </div>


                  <div class="col-md-6">
                    <div class="company_information" style="text-align:center">
                      <h4>{{$company->comp_name_en}}  <small>{{$company->comp_name_arb}} </small> </h4>
                      <address class="address">
                           {{$company->comp_address}}
                      </address>
                     </div>
                  </div>
                  <div class="col-md-3">
                    <div class="salary__download" style="text-align:right; display:flex-; justify-content: right; align-items:center">
                      <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                      <a href="#" class="print-button" onclick="window.print()">PDF Or Pirnt</a>
                    </div>
                  </div>
              </div>
          </div>
          <!-- salary table -->


          <div class="salary__table-wrap">
              <div class="row">
                  <div class="col-md-12">
                      <table class="table table-responsive salary__table">
                          <thead>
                              <!-- first-head-row -->
                              <tr class="first-head-row">
                                  <th> <span>S.N</span> </th>
                                  <th> <span>Empl ID</span> </th>
                                  <th> <span>Employee Name</span> </th>
                                  <th> <span>Sponser</span> </th>
                                  <th> <span>Iqama No</span> </th>
                                  <th> <span>Join Date</span> </th>

                                  <th> <span>Nation</span> </th>
                                  <th> <span>Type</span> </th>
                                  <th> <span>Project</span> </th>
                                  <th> <span>Photo</span> </th>

                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($employee as $emp)
                              <tr class="salary-row-parent">
                                  <td> <span>{{ $loop->iteration }}</span> </td>
                                  <td> <span>{{ $emp->employee_id }}</span> </td>
                                  <td> <span>{{ $emp->employee_name }}</span> </td>
                                  <td> <span>{{ $emp->sponser->spons_name }}</span> </td>
                                  <td> <span>{{ $emp->akama_no }}</span> </td>
                                  <td> <span>{{ $emp->joining_date }}</span> </td>
                                  <td> <span>{{ Str::limit($emp->country->country_name,6) }}</span> </td>
                                  <td> <span>{{ Str::limit($emp->employeeType->name,7) }}</span> </td>
                                  <td> <span>{{ Str::limit($emp->project->proj_name,13) }}</span> </td>
                                  <td> <img src="{{ asset($emp->profile_photo) }}" alt="" style="width: 50px; height: 50px"> </td>
                              </tr>
                            @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </section>


</body>
</html>
