<!DOCTYPE html>
<html lang="en">

<head>
<title>Item Received</title>
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
                max-width: 96%;
                margin: 5 auto ;


            }
            @page {
                size: A4 portrait;
                margin: 15mm 5mm 10mm 15mm;
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
            font-size: 12px;
            color: black;
            text-align: center;

        }

        .td__title_name {
            font-size: 12px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }

        .td__total_emp {
            font-size: 12px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform: capitalize
        }


        /* table UI design  */

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 90%;
        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 12px;
            padding: 5px;
        }



        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
            font-weight: bold;
            height: 30px;

        }
    </style>
    <!-- style -->



<body>

    <div class="main__wrap">

        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong>Item Received Details</strong></p>
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
                <table id="employeeinfo">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Store Name</th>
                                        <th>Item Name</th>
                                        <th>Item Code</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Received</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total_quantity = 0;
                                    @endphp
                                    @foreach($records as $item)
                                    @php
                                             $total_quantity += $item->quantity;
                                    @endphp
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td>{{ $item->sub_store_name }}</td>
                                        <td>{{ $item->item_deta_name }}</td>
                                        <td>{{ $item->item_deta_code }}</td>
                                        <td>{{ $item->icatg_name }}</td>
                                        <td>{{ $item->iscatg_name }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->received_date)) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7">Total Quantity</td>
                                        <td>{{ $total_quantity }}</td>
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
