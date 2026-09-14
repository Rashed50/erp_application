<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Item Received Paper</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 70%;
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
            width: 70%;
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
            font-size: 18px;
        }

        .title__part {
            text-align: center;
            font-size: 18px;
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
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 12px;
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

        .td__trade_name {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform: capitalize
        }



        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 12px;
            padding: 5px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: left;
            background-color: #EAEDED;
            color: black;
        }



        .invoice-container {
            width: 100%;
            margin: 20px auto;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }


        .invoice-info {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: white;
        }

        .invoice-details {
            width: 48%;
            font-size: 15px;
            line-height: 1.5;
        }

        .invoice-details strong {
            color: #333;
            font-weight: bold;
        }




    </style>


</head>

<body>
    {{-- <div class="btn-pr">
        <button class="btn btn-success text-center print-button" onclick="allPrint()" id="print"><i class="fa fa-print"></i></button>
    </div> --}}




    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong> Inventory Item Stock Purchase Invoice</strong></p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{ $company->comp_name_en }} <small>{{ $company->comp_name_arb }} </small> </h6>
                <address class="address">
                    {{ $company->comp_address }}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>


        <div class="invoice-container">
            <div class="invoice-info">
                <div class="invoice-details">
                    <p><strong>Store Name:</strong> {{ $inventory_purchase_record->storeName->sub_store_name }}</p>
                    <p><strong>Purchase By:</strong> {{ $inventory_purchase_record->purchaseBy->name }}</p>
                    <p><strong>Purchase From:</strong> {{ $inventory_purchase_record->purchaseFrom->name }}</p>
                    <p><strong>Challan Number:</strong> {{ $inventory_purchase_record->chalan_no }}</p>
                </div>
                <div class="invoice-details">
                    <p><strong>Invoice No:</strong> {{ $inventory_purchase_record->invoice_no }}</p>
                    <p><strong>Date:</strong> {{ $inventory_purchase_record->invoice_date }}</p>
                    <p><strong>Received Date:</strong> {{ $inventory_purchase_record->received_date }}</p>
                </div>
            </div>
        </div>


        {{-- <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th> <span>Store Name</span> </th>
                        <th> <span>Challan Number</span> </th>
                        <th> <span>Purchase By</span> </th>
                        <th> <span>Purchase From</span> </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="td__trade_name"> </td>
                        <td class="td__trade_name"> {{ $inventory_purchase_record->chalan_no }} </td>
                        <td class="td__trade_name">  </td>
                        <td class="td__trade_name">  </td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th> <span>Invoice No</span> </th>
                        <th> <span>Date</span> </th>
                        <th> <span>Received Date</span> </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="td__trade_name">  </td>
                        <td class="td__trade_name" > faasfsfad</td>
                        <td class="td__trade_name"> faasfsfad</td>
                    </tr>
                </tbody>
            </table>
        </section> --}}
        <!-- table part -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th> <span>S.N</span> </th>
                        <th> <span>Category</span> </th>
                        <th> <span>Subcategory</span> </th>
                        <th> <span>Item Name</span> </th>
                        <th> <span>Brand Name</span> </th>
                        <th> <span>Model/Size</span> </th>
                        <!--<th> <span>Unit</span> </th>-->
                        <th> <span>Quantity</span> </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($inventory_item_details as $item)
                    @php
                        $item_unit_name = App\Enums\InventoryItemsUnit::getNameById($item->item_det_unit);
                    @endphp
                        <tr style="text-align: center;">
                            <td class="td__s_n"> {{ $loop->iteration }}</td>
                            <td class="td__trade_name"> {{ $item->itemCatg->icatg_name }} </td>
                            <td class="td__trade_name"> {{ $item->itemSubCatg->iscatg_name }} </td>
                            <td class="td__trade_name"> {{ $item->itemNameCode->item_deta_name . " " .  $item->itemNameCode->item_deta_code}} </td>
                            <td class="td__trade_name"> {{ $item->itemBrandName->item_brand_name }} </td>
                            <td class="td__trade_name"> {{ $item->model_no . ", " . $item->serial_no }} </td>
                            <!--<td class="td__trade_name"> {{ $item_unit_name }} </td>-->
                            <td class="td__trade_name"> {{ $item->quantity }} </td>
                        </tr>
                    @endforeach



                </tbody>
            </table>
        </section>
        <!-- ---------- -->

        <section>
            <br><br>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                    <p>Prepared By<br /><br /> <span style="padding: 10px;">{{ $inventory_purchase_record->createBy->name }}</span></p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>

    <script>
        // function allPrint() {
        //     window.print();
        // };

        // window.addEventListener('DOMContentLoaded', (event) => {
        //     window.print();
        // });
    </script>

</body>

</html>
