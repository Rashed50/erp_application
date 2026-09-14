<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Received from Bank</title>
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

            .download_link {
                visibility: hidden;
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
            font-size: 13px;
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
            text-align: center;
            background: #B3E6FA;
            padding-top: 5px;
            padding-bottom: 5px;
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


        /* table UI design  */

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
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
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong> Cash Received Report </strong></p>
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

        {{-- {{ dd($items->toArray()) }} --}}


        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                    <tr >
                        <th> <span>S.N</span> </th>
                        <th> <span>Receipt No.</span> </th>
                        <th> <span>Bank</span> </th>
                        <th> <span>Date</span> </th>
                        <th> <span>Inserted By</span> </th>
                        <th>Inserted At</th>
                        <th> <span>Remarks</span> </th>
                        <th> <span>Amount</span> </th>
                    </tr>

                </thead>
                <tbody>
                    @php $totalAmount = 0; @endphp
                    @foreach ($items as $index => $item)
                    <tr style="text-align: center;">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['receipt_number'] }}</td>
                        <td>{{ $item['bank_name'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['ReceivedDate'])->format('d/m/Y') }}</td>
                        <td>{{ $item['created_by_name'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                        <td>{{ $item['Remarks'] }}</td>
                        <td>{{ number_format($item['Amount'], 2) }}</td>
                    </tr>
                    @php $totalAmount += $item['Amount']; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="text-align: center; font-weight: bold;">
                        <td colspan="7">Total Amount</td>
                        <td>{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </section>

        <section>
            <br><br>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
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
