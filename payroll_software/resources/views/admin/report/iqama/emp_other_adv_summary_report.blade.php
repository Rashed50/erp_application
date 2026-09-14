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
                margin: 5 auto ;
            }

            @page {
                size: A4 landscape;
                margin: 15mm 5mm 5mm 15mm;
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
            width:20px;

        }
        .td__emp_id {
            font-size: 11px;
            color: black;
            text-align: center;
            width:30px;

        }
        .td__emp_iqama {
            font-size: 11px;
            color: black;
            text-align: center;
            width:40px;

        }

        .td__emp_name {
            font-size: 11px;
            color: black;
            text-align: left;
      
        }

        .td__project_name {
            font-size: 11px;
            color: black;
            text-align: left;
         
        }         
     

        .td__title_name {
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
           
        }

        .td__total {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: right;
            padding-right: 5px;
            width:45px;
        }
        .td__gr_total {
            font-size: 11px;
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
                <p> <strong>Employee Other Advance Summary <br>
                      Project: {{$report_title[0]}}   
                     </strong></p>  

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
                        <th> <span>Emp.ID</span> </th>
                        <th> <span>Employee Name</span> </th>
                        <th> <span>Iqama</span> </th>
                        <th> <span>Sponser</span> </th>
                        <th> <span>Project</span> </th>
                        <th> <span>Salary Type</span> </th>
                        <th> <span>Total Adv.</span> </th>
                        <th> <span>Total Paid</span> </th>
                        <th> <span>Due</span> </th>
                        
                    </tr>
                </thead>
                <tbody>

                        @php 
                            $gr_total_adv = 0;
                            $gr_total_paid = 0;
                        @endphp
                    @foreach ($records as $emp)
                        @php 
                            $gr_total_adv += $emp->Total_Advance;
                            $gr_total_paid +=  $emp->Total_Paid ;
                        @endphp
                    <tr>
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__emp_id">  {{ $emp->employee_id }}</td>
                        <td class="td__emp_name"> {{ $emp->employee_name }} </td>
                        <td class="td__emp_iqama"> {{ $emp->akama_no }}</td> 
                        <td class="td__title_name"> {{ $emp->spons_name }}</td>
                        <td class="td__project_name"> {{ $emp->proj_name }} </td> 
                        <td class="td__title_name"> {{  $emp->hourly_employee == 1 ? "Hourly" : "Basic Salary" }}</td>                         
                        <td class="td__total"> {{ $emp->Total_Advance }}</td>
                        <td class="td__total"> {{ $emp->Total_Paid }}</td>
                        <td class="td__total"> {{ $emp->Total_Advance - $emp->Total_Paid }}</td>                                          
                       </tr>
                    @endforeach

                    <tr>
                        <td colspan="7" class="td__gr_total" >Total </td>                                               
                        <td class="td__gr_total"> {{ $gr_total_adv  }}</td>
                        <td class="td__gr_total"> {{ $gr_total_paid }}</td>
                        <td class="td__gr_total">  </td>                                          
                       </tr>

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
 
 
