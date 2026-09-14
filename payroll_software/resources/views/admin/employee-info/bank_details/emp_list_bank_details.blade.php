<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emp Bank Info</title>
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
                margin: 10mm 5mm 5mm 10mm;
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
            width: 98%;
            margin: 5px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .th_header {
            background-color: #B3E6FA; 
            height: 30px;  
            font-size: 12px;
            font-weight: bold;
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
            
            color: black;
            text-align: center;

        }

        .td__employee_id {
           
            color: red;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_info {
          
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        .td__iqama {
         
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__country{
           
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__emp_trade {
          
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__sponsor {
        
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
                <p> <strong> {{$report_title}}</strong></p>
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
                  <th class="th_header" > S.N </th>
                  <th  class="th_header"> ID </th>
                  <th class="th_header">  Employee Name  </th>
                  <th class="th_header">  Iqama No </th>
                  <th class="th_header">   Nation</span> </th>
                  <th class="th_header">  Project Name  </th>
                  <th class="th_header">  Trade  </th>
                  <th class="th_header">  Sponsor  </th>
                  <th class="th_header">  Salary <br> Paid By  </th> 
                  <th class="th_header">A/C Number</th>
                  <th class="th_header">IBAN</th>
                  <th class="th_header">Bank Name</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($employees as $emp)
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id"><span id="employee_id">{{$emp->employee_id}}</span> </td>
                  <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }} </td>               
                  <td class="td__country"> {{ Str::limit($emp->country_name,10) }}</td>
                  <td class="td__emplyoee_info"> {{ $emp->proj_name }} </td>
                  <td class="td__emp_trade"> {{ $emp->catg_name }} </td>
                  <td class="td__sponsor"> {{ $emp->spons_name }}</td>
                  <td class="td__sponsor"> {{ $emp->payment_method }}</td>
                  <td class="td__iqama"> {{$emp->acc_number}}   </td>
                  <td class="td__iqama"> {{$emp->acc_iban}}   </td>
                  <td class="td__iqama"> {{$emp->bn_name}}   </td>
                 </tr>
                @endforeach
              </tbody>
 
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
    <br><br>
    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br> {{$login_user}}</p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>

