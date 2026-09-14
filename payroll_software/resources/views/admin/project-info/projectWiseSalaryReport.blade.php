<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Wise Total Work Hours</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">
    <style media="screen">
      tr th{
        font-size: 14px;
        font-weight: 600;
      }
      td span{
        font-size: 13px;
        font-weight: 600;
      }
      a.print-button{
        text-decoration: none;
        background: teal;
        color: #fff;
        padding: 5px 10px;
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
                    <h3><a href="#">Total Work Hours : </a></h3>
                    <div class="project_info">
                        <span class="project_name"> {{ $project->proj_name }} </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6"></div>
        </div>
        <!-- salary bottom header -->
        <div class="salary__header-bottom">
            <div class="row">
                <div class="col-md-3">
                    <p class="date">{{ Carbon\Carbon::now()->format('d/F/Y') }}</p>
                </div>
                <div class="col-md-6">
                  <div class="company_information" style="text-align:center">
                    <h4>{{$company->comp_name_en}}  <small>{{$company->comp_name_arb}} </small> </h4>
                    <address class="address">
                         {{$company->comp_address}}
                    </address>
                    <p> <span> {{$company->comp_phone1}} </span>  <span> {{$company->comp_phone2}} </span> </p>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="salary__download" style="text-align:right; display:flex; justify-content: right; align-items:center">
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
                                  <th>SL NO</th>
                                  <th>Empl ID</th>
                                  <th>Employee Name</th>
                                  <th>Iqama No</th>
                                  <th>Type</th>
                                  <th>Category</th>
                                  <th>Total Hours</th>
                                  <th>Total Day</th>
                                  <th>Salary</th>
                              </tr>

                          </thead>
                          <tbody>
                            @foreach($projectWiseTotalSalary as $data)
                            <tr class="salary-row-parent">
                              <td> <span>{{ $loop->iteration }}</span> </td>
                              <td> <span>{{ $data->employee_id }}</span> </td>
                              <td> <span>{{ $data->employee_name }}</span> </td>
                              <td> <span>{{ $data->akama_no }}</span> </td>
                              <td> <span>{{ $data->emp_type_id == 1 ? "Direct" : "Indire" }}</span> </td>
                              <td> <span>{{ $data->catg_name }}</span> </td>
                              <td> <span>{{ $data->slh_total_hours }}</span> </td>
                              <td> <span>{{ $data->slh_total_working_days }}</span> </td>
                              <td> <span>{{ $data->slh_total_salary }}</span> </td>
                            </tr>
                            @endforeach
                            <!-- total hours -->
                            <tr>
                              <td colspan="8"> <span style="display:block; text-align:right">Total Salary Amount</span> </td>
                              <td> <span>{{ $totalSalay }}</span> </td>

                            </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </section>
</body>
</html>
