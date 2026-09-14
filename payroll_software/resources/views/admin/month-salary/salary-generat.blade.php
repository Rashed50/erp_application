<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Generate</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <style>
        tbody, td, tfoot, th, thead, tr {
            border: 0;
        }
        .salary{
            margin-top: 30px;
        }
        .salary__header{

        }
        .salary__header h3{}
        .salary__header h3 a{
            color: #222;
            font-weight: 600;
            display: block;
            margin-bottom: 25px;

        }
        .salary__header-bottom{}
        .salary__header-bottom p.date{
            display: inline-block;
            background: yellow;
            padding: 2px 10px;
            color: teal;
            font-weight: 600;
            font-style: italic;
        }
        .salary__header-bottom p.name{
            font-size: 16px;
            font-weight: 700;
            text-transform: capitalize;
            color: #22B166;
        }
        .salary__table-wrap{}
        .salary__table{}
        .salary__table thead{

        }
        .salary__table thead tr{
            border: 2px solid #DFDFDF;
            padding: 10px !important;
        }
        .salary__table thead tr th{
            font-size: 11px;
        }
        .salary__table tbody tr.first-row{
            background-color: #E5E5E5;
            border-right: 2px solid #E5E5E5;
            border-left: 2px solid #E5E5E5;
        }
        .salary__table tbody tr.first-row td{
            padding: 0;
            font-size: 11px;
        }

        .salary__table tbody tr.second-row{
            border: 2px solid #E5E5E5;
        }
        .salary__table tbody tr.second-row td{
            padding: 0;
            font-size: 11px;
        }
        .salary__table tbody tr.second-row td.signature_data{
            width: 15%;
        }
        .salary__table tbody tr.second-row td.signature_data span{
          font-size: 11px;
          color: red;
          display: inline-block;
          border: 1px solid #ddd;
          padding: 2px 19px 10px 5px;
          margin-top: 0px;
          width: 150px;
          height: 28px;
        }
        .salary__table tbody tr.third-row td.total_amount span{
            color: #2F72BC;
            font-size: 14px;
            font-weight: bold;
            font-style: italic;
            border: 1px solid #ddd;
            padding: 2px 10px;
        }
        .salary__table tbody tr.third-row td{
            padding: 0;
        }



        .salary__table thead tr th{
            text-align: center;
        }
        .salary__table tbody tr td{
            text-align: center;
        }
        .salary__table tbody tr td.name_data{
            width: 10%;
            text-transform: uppercase;
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
                        <h3><a href="#">Salary Sheet</a></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="salary__download">
                        <a target="_blank" href="{{ route('download-pdf') }}">Download PDF</a>
                    </div>
                </div>
            </div>
            <!-- salary bottom header -->
            <div class="salary__header-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <p class="date">Jun/2021</p>
                    </div>
                    <div class="col-md-6">
                        <p class="name">saiful </p>
                    </div>
                </div>
            </div>
            <!-- salary table -->
            <div class="salary__table-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-responsive salary__table">
                            <thead>
                              <tr>
                                <th scope="col">Sr. Num</th>
                                <th scope="col">Empl ID</th>
                                <th scope="col">Empl Name</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col">Rate/Hour</th>
                                <th scope="col">Work HRS.Days</th>
                                <th scope="col">Salary Amount</th>
                                <th scope="col">OT Amount <a href="#">OT HRs</a></th>
                                <th scope="col">Food Alwnce.</th>
                                <th scope="col">Absent</th>
                                <th scope="col">Absent.1</th>
                                <th scope="col">Absent.2</th>
                                <th scope="col">Iqama Renewal</th>
                                <th scope="col">Total Amount</th>
                                <th scope="col">Signature</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <!-- first tr -->
                                <tr class="first-row">
                                    <td>1</td>
                                    <td><span style="color: #2F72BC; font-weight: bold;"> {{ $findemployee->employee_id }}</span></td>
                                    <td class="name_data">{{ $findemployee->employee_name }}</td>
                                    <td></td>
                                    <td></td>
                                    <td style="font-weight: bold; width: 6%;">2445400696</td>
                                    <td style="color: #22B68F; font-size: 13px; font-weight: 900; text-transform: uppercase;">Other</td>
                                    <td style="color: red; text-transform: uppercase; text-align: right;">Bang</td>
                                    <td colspan="2" style="color: red; text-transform: capitalize;">Carpenter</td>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- second tr -->
                                <tr class="second-row">
                                    <td></td>
                                    <td></td>
                                    <td><span style="color:#22B68F">100+500</span></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="color: #22B68F; font-size: 13px; font-weight: 900; text-transform: uppercase;">{{ $salary->hourly_rent }}</td>
                                    <td>{{ $totalWorkingHours }} <br> {{ $totalWorkingdays }}</td>
                                    <td>2,470</td>
                                    <td>0 <br> 0</td>
                                    <td>{{ $salary->others1 }}</td>
                                    <td>{{ $salary->others2 }}</td>
                                    <td>{{ $salary->others3 }}</td>
                                    <td>200</td>
                                    <td>0</td>
                                    <td>{{ $total }}</td>
                                    <td class="signature_data"> <span></span></td>
                                </tr>
                                <!-- third tr -->
                                <tr class="third-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_amount"><span>{{ $total }}</span></td>
                                    <td></td>
                                </tr>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <form id="salaryHistory">
      @csrf
      <input type="text" id="emp_auto_id" name="emp_auto_id" value="{{ $findemployee->emp_auto_id }}">
      <input type="text" id="emp_id" name="emp_id" value="{{ $findemployee->employee_id }}">
      <input type="text" id="totalHours" name="totalHours" value="{{ $totalWorkingHours }} ">
      <input type="text" id="totalSalary" name="totalSalary" value="{{ $total }} ">
      <input type="text" id="totalDays" name="totalDays" value="{{ $totalWorkingdays }}">
      <input type="text" id="month" name="month" value="{{ $month }}">
      <input type="text" id="year" name="year" value="{{ $year }}">
      <input type="text" id="salaryDate" name="salaryDate" value="{{ $salaryDate }}">
      <button type="submit">Add History</button>
    </form>

    <script src="{{asset('contents/admin')}}/assets/js/ajax/jquery-ajax.min.js"></script>
    <script type="text/javascript">
          $("#salaryHistory").submit(function(e){
              e.preventDefault();
              // detected value
              let emp_auto_id = $("#emp_auto_id").val();
              let emp_id = $("#emp_id").val();
              let totalHours = $("#totalHours").val();
              let totalSalary = $("#totalSalary").val();
              let totalDays = $("#totalDays").val();
              let month = $("#month").val();
              let year = $("#year").val();
              let salaryDate = $("#salaryDate").val();
              let _token = $("input[name=_token]").val();
              /* ===== ajax request ===== */
              $.ajax({
                url: "{{ route('add-salary-history') }}",
                type: 'POST',
                data: {
                  emp_auto_id:emp_auto_id,
                  emp_id:emp_id,
                  totalHours:totalHours,
                  totalSalary:totalSalary,
                  totalDays:totalDays,
                  month:month,
                  year:year,
                  salaryDate:salaryDate,
                  _token:_token,
                },
                success:function(response){
                    if(response){
                      alert('Success');
                    }else {
                      alert('Error');
                    }
                }
              });
          });
    </script>
</body>
</html>
