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
            .download_link{
                visibility:hidden;
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

        .td__info {
            font-size: 12px;
            color: black;
            text-align: left;
            font-weight: 300;
            padding: 5px;
        }
        .td__info_center {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: 300;
            padding: 5px;
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
                <p> <strong>  Company All Running Vehicles List</strong></p>
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
                  <th> <span>Vehicle Plate No.</span> </th>
                  <th> <span>Vehicle Name</span> </th>
                  <th> <span>Brand</span> </th>
                  <th> <span>Reg. No</span> </th>
                   <th> <span>Model</span> </th>              
                  <th> <span>Inss Exp.</span> </th>
                  <th> <span>Reg. Exp.</span> </th>
                  <th class="download_link"> <span>Download Link</span> </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vehicles as $vehicle)
                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__info_center"> {{ $vehicle->veh_plate_number }} </td>
                  <td class="td__info"> {{ $vehicle->veh_name }} </td>
                  <td class="td__info"> {{ $vehicle->veh_brand_name }} </td>
                  <td class="td__info"> {{ $vehicle->veh_licence_no }} </td>
                  <td class="td__info"> {{ $vehicle->veh_model_number }} </td>               
                  <td> {{ Carbon\Carbon::parse($vehicle->veh_ins_expire_date)->format('D, d F Y') }} </td>
                  <td> {{ Carbon\Carbon::parse($vehicle->veh_reg_expire_date)->format('D, d F Y') }} </td>
                  @if($vehicle->veh_reg_certificate != null)
                  <td class="td__info_center">  <a target="_blank" class="download_link" href="{{URL::to($vehicle->veh_reg_certificate) }}"  class="btn btn-danger">Download</a> </td>
                   @endif
                 </tr>
                @endforeach
              </tbody>
            </table>
        </section>
        
    <section>
        <br><br>
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
