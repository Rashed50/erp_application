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
                size: A4 landscape;
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

        .title__part h6 {
            color: rgb(38, 104, 8);
            text-align: center;
            font-size:18px;
        }

        .title__part{
            text-align: center;
            font-size:18px;
        }
        .title_left__part{
            text-align: left;
            font-size:11px;
        }
        .address {
            font-size:13px;
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
            background-color: #B3E6FA; 
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            height: 30px;  
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

        /* .th_header {
            background-color: #B3E6FA; 
            height: 40px;  
            font-size: 12px;
            font-weight: bold;
        } */

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
        .td__amount {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: right;
            padding-right: 5px;
        }
        .td__total_amount {
            font-size:10px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
        }

        .td__others {
            font-size:10px;
            color: black; 
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
                <p> <strong> Employee Iqama Expense Renewal Details</strong></p>
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
                  <th> <span>Emp. ID</span> </th>
                  <th> <span>Employee Name</span> </th>
                  <th> <span>Iqama</span> </th>
                  <th> <span>Exp. <br> Date</span> </th>
                  <th> <span>Jawazat</span> </th>
                  <th> <span>Maqtab <br> Amal </span> </th>
                  <th> <span>Medi. <br>Insu.</span> </th>
                  <th> <span>Others</span> </th>
                  <th> <span>Jawazat <br> Penalty</span> </th> 
                  <th> <span>BD</span> </th> 
                  <th> <span>Total </span> </th>
                  <th> <span>Renewal <br> Date</span> </th>
                  <th> <span>Inserted <br> By</span> </th>
                  <th> <span>Remarks</span> </th>

                </tr>
                </thead>
                <tbody>
                    @php  $grand_total = 0;
                    @endphp
                @foreach ($employee as $emp)
                @php  $grand_total += $emp->total_amount;
                @endphp
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                   <td class="td__iqama"> {{ $emp->employee->employee_id }} </td>
                  <td class="td__emplyoee_info"> {{$emp->employee->employee_name }}</td>
                  <td class="td__iqama"> {{ $emp->employee->akama_no }} </td>
                  {{-- <td class="td__iqama"> {{ Carbon\Carbon::parse($emp->akama_expire_date)->format('d F Y') }}</td> --}}
                  <td class="td__iqama"> {{ $emp->akama_expire_date}}</td>
                  <td class="td__amount"> {{  $emp->jawazat_fee >0 ? $emp->jawazat_fee : "-"   }} </td>
                  <td class="td__amount"> {{ $emp->maktab_alamal_fee >0 ? $emp->maktab_alamal_fee : "-" }} </td>
                  <td class="td__amount"> {{  $emp->medical_insurance >0 ? $emp->medical_insurance : "-" }} </td>
                  <td class="td__amount"> {{ $emp->others_fee >0 ? $emp->others_fee : "-"  }} </td>
                  <td class="td__amount"> {{ $emp->jawazat_penalty >0 ? $emp->jawazat_penalty : "-" }} </td>
                  <td class="td__amount"> {{ $emp->bd_amount >0 ? $emp->bd_amount : "-" }} </td>
                  <td class="td__total_amount"> {{ $emp->total_amount }} </td>                  
                  {{-- <td class="td__others"> {{ Carbon\Carbon::parse( $emp->renewal_date)->format('d F Y') }}</td> --}}
                  <td class="td__iqama"> {{ $emp->renewal_date}}</td>
                  <td class="td__others"> {{ $emp->name }}</td>
                  <td class="td__others"> {{ $emp->remarks }}</td>


                 </tr>
                @endforeach
                <tr>
                    <td colspan="11" class="td__total_amount">Total</td>
                    <td class="td__total_amount">{{round($grand_total)}}</td>
                </tr>
              </tbody>

                <p style="page-break-after: always;"></p>
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
