<!DOCTYPE html>
<html lang="en">

<head>
 <title>{{ $activity_name }} Report</title>
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
                margin: 5 auto ;


            }

            @page {
                size: A4 portrait;
                margin: 15mm 5mm 10mm 15mm;
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
            width: 99%;
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
            font-size: 12px;
        }

        .title_left__part {
            text-align: left;
            font-size: 12px;
        }

        .address {
            font-size: 12px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
            padding-right: 20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 98%;
            padding: 0px;
            align: center
        }

        table,
        tr {
            border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 11px;
            color: black;
            text-align: center;

        }
        
        .td__emp_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }

        .td__title_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }

        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform: capitalize
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
                <p> <strong>{{ $activity_name }} Employees <br>
                     From {{$report_title[0]}} To {{$report_title[1]}}   </strong></p>

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
                <p> <strong>Print Date:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th > <span>S.N</span> </th>
                        <th> <span>ID</span> </th>
                        <th> <span>Employee Name</span> </th>
                        <th> <span>Passport <br> Iqama</span> </th>
                        <!--<th> <span>Iqama </span> </th>-->
                        <th> <span>Sponsor</span> </th>
                        <th> <span>Basic <br>Hourly</span> </th>
                        <th> <span>Nat.</span> </th>
                        <th> <span>Activity <br> Date </span> </th>
                        <th> <span>Inserted By</span> </th>
                        <th> <span>Activity </span> </th>
                        <th> <span>Current Status </span> </th>
                        <th> <span>Notes</span> </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $emp)
                    <tr >
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__employee_id">  {{ $emp->employee_id }}</td>
                        <td class="td__emp_name"> {{ $emp->employee_name }} </td>
                        <td class="td__iqama"> {{ $emp->passfort_no }} <br> {{ $emp->akama_no }}  </td>
                        <!--<td class="td__iqama">  {{ $emp->akama_no }} </td>-->

                        <td class="td__emplyoee_info"> {{ $emp->spons_name }}</td>
                        <td class="td__emplyoee_info"> {{  $emp->hourly_employee == 1 ? "Hourly" : "Basic" }}</td>
                        <td class="td__country"> {{ Str::limit($emp->country_name,3) }}</td>
                        <td class="td__emplyoee_info"> {{ $emp->create_at }}</td>
                        <td class="td__emplyoee_info"> {{ $emp->name }}</td>
                        <td class="td__emplyoee_info"> {{ $emp->title }}</td>
                        <td class="td__emplyoee_info">

                            @if($emp->job_status == 1)
                                Active
                            @elseif($emp->job_status == 2)
                                Inactive
                            @elseif($emp->job_status == 3)
                                Final_Exit
                            @elseif($emp->job_status == 4)
                                Released
                            @elseif($emp->job_status ==5)
                                Vacation
                            @elseif($emp->job_status == 6)
                                Runaway
                            @elseif($emp->job_status == 0)
                                Waiting for Approval
                            @endif

                         </td>
                        <td class="td__emplyoee_info"> {{ $emp->remarks }}</td>

                       </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p>Prepared By<br /><br /><br></p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
