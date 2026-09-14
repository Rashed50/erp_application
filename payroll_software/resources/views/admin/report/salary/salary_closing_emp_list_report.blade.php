<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Closing Emps</title>
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
                margin: 0 auto 5px;
            }

            @page {
                size: A4 landscape;
                margin: 10mm 0mm 5mm 10mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

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
            font-size:14px;
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
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 12px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }

        .th_header {
            font-size: 12px;
            color: black;
            background:#B3B6B7;
            font-weight: bold;
            text-align: center;
            text-transform: capitalize;
              height:30px;
        }

        .td__s_n {
            font-size: 12px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_info {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        .td__iqama {
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__country{
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__emp_trade {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__amount {

            font-size: 12px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
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
                <p> <strong>  {{$report_title}}</strong></p>
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
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now()  }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th class="th_header" > <span>S.N</span> </th>
                  <th class="th_header"> <span>ID</span> </th>
                  <th class="th_header"> <span>Employee Name</span> </th>
                  <th class="th_header"> <span>Iqama</span> </th>
                  <th class="th_header">Contact <br>Nation</th>
                  <th class="th_header"> <span>Trade</span> </th>
                  <th class="th_header"> <span>Sponsor</span> </th>                   
                  <th class="th_header"> <span>Type</span> </th> 
                  <th class="th_header"> <span>Start Date</span> </th>
                  <th class="th_header"> <span>End Date</span> </th>
                  <th class="th_header"> Updated By </th>                   
                  <th class="th_header">Balance</th> 
                  <th class="th_header">Remarks</th>

                </tr>
                </thead>
                <tbody>
                @foreach ($report_records as $emp)
               
                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id">{{$emp->employee_id}}</span> </td>
                  <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }} <br> {{ $emp->akama_expire_date }} </td>
                  <td class="td__emp_trade"> {{ $emp->mobile_no }} <br>{{ Str::limit($emp->country_name,10) }} </td>
                  <td class="td__emp_trade"> {{ $emp->catg_name,10 }} </td>
                  <td class="td__emp_trade"> {{ $emp->spons_name }}</td>                  
                  <td class="td__country"> {{  $emp->hourly_employee == 1 ? "Hourly": "Basic"  }}</td>
                  <td class="td__country"> {{$emp->start_date}} </td>
                  <td class="td__country"> {{$emp->end_date}} </td>
                  <th class="td__country"> {{ $emp->updated_by }}</th>
                  <td class="td__amount"> {{ $emp->balance_amount  }} </td> 
                  <td class="td__country"> {{$emp->remarks ==  null ? "-" : $emp->remarks }} </td>
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
                <p>Prepared By<br/><br/><br>   {{$login_name}}</p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>

