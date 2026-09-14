<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Log Book Report</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">
    <style media="screen">
      td span.data{
        font-size: 14px;
      }
      th span.dataHead{
        font-size: 15px;
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
                    <h3><a href="#">Report For : </a></h3>
                    <div class="project_info">
                        <span class="project_name"> Vehicle Log Book</span>
                        <span class="project_code">  </span>
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
                  <div class="salary__download" style="text-align:right">
                    <a target="_blank" href="{{ route('download.pdf-salary',$company) }}">Download PDF</a>
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
                              <tr class="first-head-row">
                                  <th> <span class="dataHead">Fuel Purchase Date</span> </th>
                                  <th> <span class="dataHead">Vehicle Name</span> </th>
                                  <th> <span class="dataHead">Present Miles</span> </th>
                                  <th> <span class="dataHead">Fouel Amount(Liters)</span> </th>
                                  <th> <span class="dataHead">Total Cost</span> </th>

                                  <th> <span class="dataHead">Avarage Miles</span> </th>
                              </tr>
                          </thead>
                          <tbody>
                            @forelse($LogBook as $Lgb)
                            <tr class="salary-row-parent">
                                <td><span class="data">{{ $Lgb->date }}</span></td>
                                <td><span class="data">{{ $Lgb->vehicle->veh_name }}</span></td>
                                <td><span class="data">{{ $Lgb->present_miles }}</span></td>
                                <td><span class="data">{{ $Lgb->fouel_amount }}</span></td>
                                <td><span class="data">{{ $Lgb->total_cost }}</span></td>
                                <td><span class="data">{{ $Lgb->average_miles }}</span></td>
                            </tr>
                            @empty
                            <p style="color: red"> Report Not Found!</p>
                            @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </section>
</body>
</html>
