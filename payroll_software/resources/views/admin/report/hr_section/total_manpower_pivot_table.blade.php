<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manpower Pivot Table
    </title>
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
                margin: 5mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            }

            /* th.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            } */

            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 96%;
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
            border: 1px solid #333;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        /* table tr {} */

        table th {
            font-size: 10px;
            border: 1px solid #333;
            font-weight: bold;
            color: #000;

        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px solid #333;
            padding: 0px;
            margin: 0px;
            height:20px;

            /* Top,Right,Bottom,left */
        }
        

        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;
            width:20px;
            padding: 5px;
        } 
       
        .td__trade_name {
            font-size: 11px;
            color: Black;
            font-weight: 200;
            text-align: left;
            padding-left: 4px;
            width:130px;

        }  

        .td__total {
            font-size: 10px;
            color: black;
            text-align: center;
            padding: 7px;
        }

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
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
                 
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
                <br>
                <address class="address"> 
                        Total Employees Pivot Table 
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
                        <th>S.N</th>
                        <th>Trade Name</th>                     
                           @foreach($project_list as $aproject)
                           <th>{{$aproject->proj_name}}</th>
                           @endforeach  
                           <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                        @php 
                        $counter = 0;
                        $all_trade_total_emp = 0;
                        @endphp

                @foreach($update_trade_list as $al)
                
                    <tr style="border-bottom:0;">
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__trade_name"> {{ Str::limit($al->catg_name,100) }}  </td>
                               @php 
                                $records = $al->project_info; 
                                $all_trade_total_emp += $al->trade_wise_total_emp;                               
                               @endphp 
                        @foreach($records as $aproject)                                                          
                                <td class="td__total">
                                     @if($aproject->total_emp > 0)  {{ $aproject->total_emp }}
                                     @else 
                                       -
                                       @endif
                                     </td> 
                        @endforeach
                        <td class="td__s_n"> {{ $al->trade_wise_total_emp }}</td>
                            
                    </tr>                   
                @endforeach
                <tr style="border-bottom:0;">
           
                    <td class="td__s_n" colspan ="2">Projectwise Total </td>                   
                    @foreach($project_list as $aproject)
                     <td class="td__total">{{$aproject->total_emp}}</td>
                    @endforeach
                    <td class="td__total"> {{ $all_trade_total_emp}}</td>
                </tr> 
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br>
    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
              <p>   <b> {{$reporter_name}}  </b> <br> Prepared By</p>
                <p><br>Checked By</p>
                <p><br>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>