<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Transaction</title>
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
                size: A4 portrait;
                margin: 5mm 0mm 5mm 0mm;
                /* top, right,bottom, left last value was= 10, left */

            }

            a[href]:after {
                content: none !important;
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
            width: 90%;
            margin: 5px auto;
            margin-top: 5px;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: right;
            margin-bottom: 5px;
        }

        .title__part {
            align-items: center;
        }

        .print__part {}

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 5px;
        }

        table,
        tr {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        table th {
            font-size: 11px;
        }

        table td {
            text-align: left;
            font-size: 11px;
        }

        th,
        td {
            padding: 2px 2px ;
            /* Top,Right,Bottom,left */
        } 
 
        .th__header {
            background-color: #B3E6FA; 
            height: 17px; 
            font-weight: bold;
            text-align: center;
        }

        .td__sn {
            color: black;
            font-weight: 100;
            text-align: center;
            
        } 
      
        .td__date {
            color: black;
            font-weight: 100;
            text-align: center;
            width:60px;
         
        }
        
      

        .td__gross_salary {
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
           
        }
  

        /* Employee Information Table */
         #employeeinfo {
            font-family: "Times New Roman", Times, serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table__part {
            display: flex;
        }


        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 11px;
            padding: 4px;
            
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }  
    </style>
</head>

<body>
      

    <div class="main__wrap">
        <!-- Report Header part-->
        <section class="header__part">
            <div class="date__part">                                 
            </div>
            <!-- title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address" style="text-align:center;">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>               
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <br>
        <h4 style="text-align: center;padding-bottom:10px"> Transacation Summary From {{$from_date}} To  {{$to_date}}</h4>
        <!-- Credit Invoice Record -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead> 
                    <tr>
                        <td colspan="7" class="td__gross_salary">Closing Balance on {{ $previous_date}}</td> 
                        <td class="td__gross_salary"> {{ $previous_balance  }}</td>
                    </tr>
                     <tr>
                        <th class="th__header">S.N</th> 
                        <th class="th__header">Receipt No.</th>
                        <th class="th__header">Received Method</th>
                        <th class="th__header">Bank</th>
                        <th class="th__header">Date</th>
                        <th class="th__header">Inserted By</th>
                        <th class="th__header">Remarks</th>
                        <th class="th__header">Amount</th>                         
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_cr_amount = 0;
                    @endphp

                    @foreach($cash_received_records as $ar)

                    @php 
                        $total_cr_amount += $ar->Amount
                    @endphp  
                    <tr>
                        <td class="td__sn">{{ $loop->iteration }}</td>
                        <td class="td__receipt_no"> {{ $ar->receipt_number != null ? $ar->receipt_number : '-'  }} </td> 
                        <td class="td__paytype"> {{ $ar->ReceiveMethod  }} </td>
                        <td class="td__emp_name"> {{ $ar->bank_name != null ? $ar->bank_name : '-' }} </td>
                        <td class="td__date"> {{ $ar->ReceivedDate  }}  </td>
                        <td class="td__emp_name"> {{ $ar->name != null ? $ar->name : '-'  }} </td>
                        <td class="td__remarks"> {{ $ar->Remarks != null ? $ar->Remarks : '-'  }}</td>
                        <td class="td__gross_salary">{{ number_format($ar->Amount,2)}} </td>                      
                    </tr> 
                    @endforeach
                    <tr>
                        <td colspan="7" class="td__gross_salary"> Total Balance </td>
                        <td class="td__gross_salary"> {{ number_format($total_cr_amount+$previous_balance,2)}} </td>    
                    </tr>
                </tbody>
            </table>
        </section>

        <br>
        <!-- Credit Invoice Record -->
        <section class="table__part">
            <table id="employeeinfo">
                <caption>Date By Date Expense</caption>
                <thead>
                     <tr>
                        <th class="th__header">S.N</th>
                        <th class="th__header">Date</th>                       
                        <th class="th__header">Amount</th> 
                    </tr>
                </thead>
                <tbody>

                    @php
                        $total_dr_amount = 0;
                    @endphp

                    @foreach($expense_records as $ar)

                    @php 
                        $total_dr_amount += $ar->Total_Amount
                    @endphp

                        <tr>
                            <td class="td__sn">{{ $loop->iteration }}</td>
                            <td class="td__date"> {{ $ar->ExpenseDate }} </td> 
                            <td class="td__gross_salary"> {{ number_format($ar->Total_Amount,2)}} <br></td>                       
                        </tr> 
                    @endforeach
                    <tr>
                        <td colspan="2" class="td__gross_salary"> Total Expense </td>
                        <td class="td__gross_salary"> {{ number_format($total_dr_amount,2)}} </td>    
                    </tr>
                    <tr>
                        <td colspan="2" class="td__gross_salary"> Current Balance </td>
                        <td class="td__gross_salary"> {{ number_format(($total_cr_amount+$previous_balance - $total_dr_amount),2) }}</td>    
                    </tr>
                    
                </tbody>
            </table>
        </section>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Accountant</p>
                    <p>Verified</p>
                    <p>General Manager</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>