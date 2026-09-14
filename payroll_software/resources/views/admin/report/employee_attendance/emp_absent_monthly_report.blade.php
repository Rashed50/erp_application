<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 0 auto 10px;


            }

            @page {
                size: A4 portrait;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 95%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
          border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 10px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_info {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        .td__iqama {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__country{
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__emp_trade {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }


    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">

                <p> <strong>Attendance of {{$report_title[0]}},{{$report_title[1]}} </strong></p>
                <p> <strong> Not Presented Minimum : {{$report_title[2]}} Days </strong></p>

            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th > <span>S.N</span> </th>
                  <th> <span>Empl ID</span> </th>
                  <th> <span>Employee Name</span> </th>
                  <th> <span>Iqama No</span> </th>
                  <th> <span>Working Shift</span> </th>
                  <th> <span>Project Name</span> </th>
                  <th> <span>Trade</span> </th>
                  <th> <span>Sponser</span> </th>
                  <th> <span>Emp. Type</span> </th>
                  <th> <span>Contact No</span> </th>
                    <th> <span>Joining Date</span> </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($absent_manpower_records as $emp)
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id">  {{ $emp->employee_id }}</td>
                  <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }} </td>
                  @if ($emp->isNightShift == 0)
                    <td class="td__emplyoee_info"> Day Shift </td>
                  @else
                    <td class="td__emplyoee_info">  Night Shift </td>
                  @endif
                  <td class="td__emplyoee_info"> {{ $emp->proj_name }} </td>
                  <td class="td__emp_trade"> {{ $emp->catg_name,10 }} </td>
                  <td class="td__emplyoee_info"> {{ $emp->spons_name }}</td>
                  @if ($emp->hourly_employee == 1)
                    <td class="td__emplyoee_info"> Hourly </td>
                  @else
                    <td class="td__emplyoee_info"> Basic Salary </td>
                  @endif
                  <td class="td__emplyoee_info"> {{ $emp->mobile_no }}, {{ $emp->phone_no }}</td>
                   <td class="td__emplyoee_info"> {{ $emp->joining_date }}</td>
                 </tr>
                @endforeach
              </tbody>

                </tbody>
            </table>
        </section>
        <!-- ---------- -->

    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br></p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>
