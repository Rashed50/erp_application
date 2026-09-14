<!-- purchase_report_pdf.blade.php -->
<html>
<head>
    <style>
        @page {
            margin: .5in;
            size: A4;
            counter-increment: page;
        }

        @page :first {
            margin-top: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            color: #333;
            font-size: 8pt;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            /* font-size: 12px; */
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .header {
            margin-bottom: 40px;
            display: flex;
        }

        .header .logo {
            padding-top: 2rem;
            height: .6in;
        }

        .header .company {
            padding-top: 2rem;
            width: 100%;
        }

        .header .company p {
            margin: 0 0 6pt;
            font-size: 10pt;
            text-align: center;
        }

        .total {
            font-weight: bold;
            color: #d9534f;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <header class="header">
        <img class="logo" src="" alt="Logo">
        <div class="company">
            <p><b>ASLOOB BEDAA</b></p>
            <p>A Construction Company of Excellence</p>
        </div>
    </header>
    <h2>Purchase Report</h2>
    <table cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice Number</th>
                <th>Supplier Name</th>
                <th>Issue Date</th>
                <th>Purchase Date</th>
                <th>Net Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $index => $purchase)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->supplier->isupp_name ?? 'N/A' }}</td>
                    <td>{{ $purchase->issue_date }}</td>
                    <td>{{ $purchase->purchase_date }}</td>
                    <td class="total">{{ number_format($purchase->net_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generated on: {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
