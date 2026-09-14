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
            font-size:12px;
        }
        .address {
            font-size:11px;
        }
        .print__part{
            text-align: right;
            font-size:12px;
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


        .td__s_n {
            font-size: 12px;
            color: black;
            text-align: center;

        }
 
        .td__emplyoee_info {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }
        .td__amount {
            font-size: 12px;
            color: black;
            font-weight: 300;
            text-align: right;
            padding-right: 5px;
        }
        .td__total_amount {
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
                <p> <strong>{{$report_title}}</strong></p>
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
                    <tr class="first-head-row">
                        <th> <span>S.N</span> </th>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Iqama</th>
                        <th>Passport</th>
                        <!--<th>Adv. Purpose </th>-->
                        <th> Advance <br>Date </th>                       
                        <th> Inserted By </th>
                        <th> Inserted At </th>
                        <th> Remarks </th>
                        <th> Working <br>Project </th>
                        <th>Amount</th>                       
                    </tr>
                </thead>
                <tbody>
                  @php 
                    $total_amount = 0;
                  @endphp

                  @foreach($advance_records as $emp)
                    @php 
                      $total_amount += $emp->adv_amount
                    @endphp
                    <tr class="salary-row-parent">
                        <td> <span>{{ $loop->iteration }}</span> </td> 
                        <td class="td__emplyoee_info"> {{ $emp->employee_id }} </td>
                        <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                        <td class="td__emplyoee_info"> {{ $emp->akama_no }} </td>
                        <td class="td__emplyoee_info"> {{ $emp->passfort_no }} </td>                      
                        <!--<td class="td__emplyoee_info"> {{ $emp->purpose }} </td>-->
                        <td class="td__emplyoee_info"> <span>{{ Carbon\Carbon::parse($emp->date)->format('D, d F Y') }}</span> </td>
                        <td class="td__emplyoee_info"> <span>{{ $emp->name }}</span> </td>
                        <td class="td__emplyoee_info"> <span>{{ $emp->created_at }}</span> </td>                     
                        <td class="td__emplyoee_info"> <span>{{ $emp->adv_remarks }}</span> </td>
                         <td class="td__emplyoee_info"> <span>{{ $emp->proj_name  }}</span> </td>
                        <td class="td__amount"> <span>{{ $emp->adv_amount }}</span> </td>

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
                <p>Prepared By<br/><br/><br><b> {{$prepared_by}} </b> </p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>
