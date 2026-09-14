<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report For Founding Source</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">
    <style media="screen">
        @media print {
          .noPrint { display: none; }
        }
    </style>
</head>
<body>
  <section class="salary">
      <div class="container">
        <!-- salary header -->
        <div class="row align-center">
            <div class="col-md-9">
                <div class="salary__header">
                    <h3><a href="#">Founding Source Report For : </a></h3>
                    <div class="project_info">
                        <span class="project_name"> {{ $start_date }} to {{ $end_date }} </span>
                        <span class="project_code">  </span>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-6"></div> -->
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
                    <a href="#" onclick="window.print()" class="btn btn-sm btn-secondary waves-effect">PRINT</a>
                    <a style="margin-left: 5px" href="{{ route('download-pdf-income_report') }}" class="btn btn-sm btn-success waves-effect">PDF</a>
                  </div>
                </div>
            </div>
        </div>


          <div class="salary__table-wrap">
              <div class="row">
                  <div class="col-md-12">
                      <table class="table table-responsive salary__table">
                        <thead>
                            <!-- first-head-row -->
                            <tr class="first-head-row">
                                <th> <span>Srl.NO</span> </th>
                                <th> <span>Description</span> </th>
                                <th> <span>Invoice No</span> </th>
                                <th> <span>Submitted Date</span> </th>
                                <th> <span>Total Amount</span> </th>
                                <th> <span>VAT</span> </th>
                                <th> <span> Debit Amount </span> </th>
                                <th> <span> Invoice Status </span> </th>
                                <th> <span> Pending Amount </span> </th>
                                <th> <span> Total Released Amount </span> </th>
                                <th> <span> Remark </span> </th>
                            </tr>
                        </thead>

                          <tbody>
                            @foreach($incomeReport as $report)
                            <tr class="salary-row-parent">
                                <td> <span class="basic-salary-data">{{ $loop->iteration }}</span> </td>
                                <td> <span class="hourly_rate">{{ $report->description }}</span> </td>
                                <td> <span class="overtime-data">{{ $report->invoice_no }}</span> </td>
                                <td> <span class="employee_name">{{ $report->submitted_date }}</span> </td>
                                <td> <span class="mobile-data">{{ $report->total_amount }}</span> </td>
                                <td> <span class="mobile-data">{{ $report->vat }}</span> </td>
                                <td> <span class="medical-data">{{ $report->debit_amount }}</span> </td>
                                <td> <span class="medical-data">
                                  @if($report->invoice_status == 1) Released  @else Pending @endif
                                </span> </td>
                                <td> <span class="medical-data">
                                  @if($report->invoice_status == 0) {{$report->net_amount}} @else 0.00 @endif
                                </span> </td>
                                <td> <span class="medical-data">
                                  @if($report->invoice_status == 1) {{$report->net_amount}} @else 0.00 @endif
                                </span> </td>
                                <td> <span class="medical-data"> </span> </td>
                            </tr>
                            @endforeach
                            <tr>
                                  <td colspan="4"> <span style="display: block; margin-right: 30px; text-align: right">Total</span> </td>

                                  <td> <span class="total-amount">{{ $total_amount }}</span> </td>
                                  <td> <span class="total-amount">{{ $vat_amont }}</span> </td>
                                  <td> <span class="total-amount">{{ $debit_amont }}</span> </td>
                                  <td></td>
                                  <td> <span class="total-amount">{{ $pending_amount }}</span> </td>
                                  <td> <span class="total-amount">{{ $released_amount }}</span> </td>
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
