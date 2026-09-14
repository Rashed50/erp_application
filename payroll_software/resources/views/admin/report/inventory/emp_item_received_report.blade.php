<!DOCTYPE html>
<html lang="en">

<head>
    <title>Emp. Received Item</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 96%;
                margin: 5 auto ;


            }

            @page {
                size: A4 landscape;
                margin: 25mm 5mm 20mm 25mm;
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

        .td__title_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }
        .td__emplyoee_info{
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }
        .td__project_title{
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
          
            
        }
         .td_common{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
         }

        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform: capitalize
        }
    </style>
 </head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong>  </strong></p>  

            </div>
            <!-- title -->
            <div class="title__part">
                <h3>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h3>
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
                        <th> <span>Name</span> </th> 
                        <th> <span>Sponsor</span> </th>
                        <th> <span>Basic/Hour</span> </th>
                        <th> <span>Store</span> </th>
                        <th> <span>Item Name</span> </th>
                         <th> <span>Received Date</span> </th>
                        <th> <span>Provided By</span> </th>
                                          
                     </tr>
                </thead>
                <tbody>
                    @foreach ($records as $emp)
                    <tr >
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td_common">  {{ $emp->employee_id }}</td>
                        <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>    
                        <td class="td_common"> {{ $emp->spons_name }}</td>
                        <td class="td_common"> {{  $emp->hourly_employee == 1 ? "Hourly" : "Basic" }}</td> 
                        <td class="td_common"> {{ $emp->sub_store_name }}</td>
                        <td class="td_common"> {{ $emp->item_deta_name }}</td>
                        <td class="td_common"> {{ $emp->received_date }}</td>
                        <td class="td_common">  </td>
                        

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
 