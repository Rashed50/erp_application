<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Adv.Receipt</title>
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
                margin: auto ;


            }

            @page {
                size: A4 landscape;
                margin: 10mm 5mm 10mm 10mm;
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
            margin: 5px auto;
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
            width: 99%;
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
            width:10px;

        }
        .td__employee_id{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: bold;
            width:10px;            
        }

        .td__emp_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
            width:100px;
        }         
       .td__project{
             font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            width:80px;
       }
        .td__iqama{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
            width:20px;
        }
        .td__mobile{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
            width:80px;
        }
        .td__emp_trade_sp{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
            width:40px;
        }

      
        .td__advance_paid_by{
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;
            width:60px;
        }

        .td__advance_amount {
            font-size: 13px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right: 3px;
            width:10px;
        }

        .td__advance_date {
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;
            width:40px;
        }
        .box__signature {         
            color: black;
            font-weight: 100;
            text-align: center;
            width: 80px;          
        }

        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: bold;
            text-align: center;
            text-transform: capitalize
        }
         .th_header {
            font-size: 12px;
            color: black;
            background:#B3E6FA;
            font-weight: bold;
            text-align: center;
            text-transform: capitalize
        }
 
    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
    <section class="header__part">
            <!-- Left Part -->
            <div class="title_left__part">
                 <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Employee Advance Receipt </strong></p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h3>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h3>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- Right Part -->
            <div class="print__part">
                 <button type="" onclick="window.print()" class="print__button">Print</button>
                 <img src="{{ asset($company->com_logo) }}"  alt="Not Found" width="180px" height="75px">
                
            </div>
        </section>
        
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th class="th_header"> <span>S.N</span> </th>
                        <th class="th_header"> <span>Emp. ID</span> </th>
                        <th class="th_header"> <span>Emp. Name</span> </th>
                        <th class="th_header"> <span>Iqama </span> </th>
                        <th class="th_header"> <span>Passport<br>Mobile</span> </th>
                        <th class="th_header"> <span>Trade</span> </th>
                        <th class="th_header"> <span>Sponsor</span> </th>
                        <th class="th_header"> <span>Project</span> </th>
                        <th class="th_header"> <span>Paid By</span> </th>
                        <th class="th_header"> <span>Advance <br> Date</span> </th>
                        <th class="th_header"> <span>Amount <br>(SAR)</span> </th>                        
                        <th class="th_header"> <span>Signature</span> </th>
                        <th class="th_header"><span>Remarks</span></th>
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grand_total = 0;
                    @endphp
                @foreach ($records as $emp)
                @php
                        $grand_total += $adv_amount;
                @endphp
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id">  {{ $emp->employee_id }}</td>
                  <td class="td__emp_name"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }}  </td>
                  <td class="td__mobile">{{ $emp->passfort_no }}  </td>
                  <td class="td__emp_trade_sp"> {{ $emp->catg_name }} </td>
                  <td class="td__emp_trade_sp"> {{ $emp->spons_name }} </td>
                  <td class="td__project">{{ $emp->proj_name}}   </td>
                  <td class="td__advance_paid_by"> {{$prepared_by}} </td>
                  <td class="td__advance_date"> {{ $adv_date }} </td>
                  <td class="td__advance_amount">{{ $adv_amount > 0 ? number_format($adv_amount, 2, '.', ',') : '' }} </td>
                  <td class="box__signature"> </td>
                  <td class="td__mobile">  </td>
                 </tr>
                @endforeach
                @if($grand_total >0)
                    <tr>
                        <td colspan="10"> <b>Total </b>  </td>
                        <td class="td__advance_amount"> {{ number_format($grand_total, 2, '.', ',')}} </td>
                    </tr>
                @endif
              </tbody> 
            </table>
        </section>
        <br><br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p style="font-size: 13px; font-weight:bold">Prepared By<br/><br/><br/><b>{{$prepared_by}} </b></p>
                    <p style="font-size: 13px; font-weight:bold">Checked By</p>
                    <p style="font-size: 13px; font-weight:bold">Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
