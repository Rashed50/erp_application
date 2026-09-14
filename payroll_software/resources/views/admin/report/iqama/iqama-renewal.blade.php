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
                        <span class="project_name">  <strong>Employee Name :</strong> {{ $findEmployee->employee_name ?? '' }} </span>
                        <span class="project_name">  <strong>Employee Id :</strong> {{ $findEmployee->employee_id ?? '' }} </span>
                       <br/> <span class="project_name">  <strong>Iqama No :</strong> {{ $findEmployee->akama_no ?? '' }} </span>
                    </div>
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
                                  <th> <span>Date</span> </th>
                                  <th> <span>Month</span> </th>
                                  <th> <span>Year</span> </th>
                                  <th> <span>Amount</span> </th>

                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($IqamaAdvance as $emp)
                              <tr class="salary-row-parent">
                                  <td> <span>{{ $loop->iteration }}</span> </td>
                                  <td> <span>{{ $emp->aph_date }}</span> </td>
                                  <td> <span>{{ date('F', strtotime($emp->aph_month ))}}</span> </td>
                                  <td> <span>{{ $emp->aph_year }}</span> </td>
                                  <td> <span>{{ $emp->amount }}</span> </td>
                              </tr>
                            @endforeach

                          </tbody>
                      </table>
                         <!--====#### Singel table ##### =====  -->
                      <div class="row">
                        <div class="col-md-8">
                          <table class="table employee-summary">
                            <thead>
                              <tr>
                                <th scope="col">ID : {{ $findEmployee->employee_id ?? '' }}</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td rowspan="6"></td>
                                <td><span>Adv Pay Amount</span></td>
                                <td><span>{{ $IqamaAdvance[0]->adv_pay_amount ?? '00' }}</span></td>
                              </tr>
                              <tr>
                                <td><span>Installes Month</span></td>
                                <td><span>{{ $IqamaAdvance[0]->installes_month ?? '00' }}</span></td>
                              </tr>
                              <tr>
                                <td><span>Total Paid</span></td>
                                <td><span>{{ $IqamaAdvance[0]->total_paid ?? '00' }}</span></td>
                              </tr>
                              <tr>
                                <td><span>Purpose</span></td>
                                <td><span>{{ $IqamaAdvance[0]->purpose ?? '00' }}</span></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-4">
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">SUB TOTAL IQAMA RENEWAL</p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ $IqamaAdvance[0]->adv_pay_amount ?? '00' }}</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">TOTAL PAID AMOUNT</p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ $IqamaAdvance[0]->total_paid ?? '00' }}</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">REMAINING AMOUNT </p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ @$IqamaAdvance[0]->adv_pay_amount-@$IqamaAdvance[0]->total_paid}}</p>
                            </div>
                          </div>
                        </div>
                      </div>


                  </div>
              </div>
          </div>
      </div>
  </section>


</body>
</html>
