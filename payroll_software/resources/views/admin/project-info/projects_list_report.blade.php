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
            font-size:12px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
            padding-right:20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
            align:center
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
            font-size: 11px;
            border: 1px ridge gray; 
            padding: 5px;
            margin: 0px; 
            text-transform:capitalize;
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
            padding-left: 5px;
            text-transform:capitalize
        }
        .td__project_code {
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;
            text-transform:capitalize;
            width:60px;
        }
        .td__amount {
            font-size: 11px;
            color: black;
            font-weight: 400;
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
                 <p> <strong> Project List {!! nl2br($report_title) !!}      </strong></p>              
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
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th> <span>S.N</span> </th>
                  <th> <span>Project Name</span> </th>
                  <th> <span>Code</span> </th>
                  <th> <span>Location</span> </th>
                  <!--<th> <span>Project </span> </th>-->
                  <th> <span>BOQ Clear.</span> </th>
                  <th> <span>Wroking Status</span> </th>      
                  <th> <span>Starting Date</span> </th>  
                  <th>Color Code</th>          
                </tr>
                </thead>
                <tbody>
                @foreach ($project_records as $arecord)
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__title_name"> {{Str::upper($arecord->proj_name)}}  </td>
                  <td class="td__project_code"> {{ $arecord->proj_code }} </td>               
                  <td class="td__title_name"> {{ Str::limit($arecord->address,50) }}</td>
                  <!--<td class="td__amount"> {{ $arecord->proj_budget }} </td>-->
                  <td class="td__project_code"> {{ $arecord->boq_clearance_duration }} Days</td>
                  <td class="td__project_code"> {{ $arecord->working_status == 1 ? 'Running' : 'Completed' }}</td>
                  <td>{{ Carbon\Carbon::parse($arecord->starting_date)->format('d-m-Y') }}</td>
                  <td class="td__project_code" style="background-color: #{{$arecord->color_code}}"> {{$arecord->color_code  }} </td>
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