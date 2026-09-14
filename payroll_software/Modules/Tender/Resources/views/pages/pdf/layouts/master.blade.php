<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 100px 50px;
        }

        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            text-align: center;
            margin-bottom: 20px;
        }

        footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            text-align: center;
        }

        h1, h2, h3, h4, h5, p, div{
            margin:0;
            padding:0;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .paragraph {
            margin-top: 10px;
            text-align: justify;
        }

        .text-justify{
            text-align: justify;
        }



        /* ============ Margin Start ============ */
        .ms-1 {
            margin-left: 10px;
        }
        
        .ms-2 {
            margin-left: 20px;
        }
        .mt-2 {
            margin-top: 20px;
        }
        .mt-3 {
            margin-top: 30px;
        }
        .my-4 {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        /* ============ Margin End ============ */
        



        /* ============ Padding Start ============ */
        .ps-2 {
            padding-left: 20px;
        }
        .py-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        
        
        .p-3 {
        padding: 1rem;
        }
        /* ============ Padding End ============ */




        /* Text alignment */
        .text-center {
            text-align: center;
        }
        



        /* ============ Table Styles Start ============ */
        /* table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        table th,
        table td {
            padding: 0.75rem;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        table th {
            text-align: inherit;
        } */
        /* ============ Table Styles End ============ */
        





        /* ============ Image styles Start ============ */
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
        /* ============ Image styles Start ============ */

        /* Prevent page break inside a single table row */
        .no-page-break {
            page-break-inside: avoid;
        }
        
        /* Add a page break before the next section if necessary */
        .page-break {
            page-break-before: always;
        }
    </style>

    @yield('style')
</head>

<body>
    <header>
        <table width="100%">
            <tr>
                <td width="50%">
                    <span>Date: {{ $data['date'] }}</span><br>
                    <span>Ref: {{ $data['cr_no'] }}</span>
                </td>
                <td width="50%" style="text-align: right;">
                    <img src="{{ $data['company_logo'] }}" style="width: 250px; height: 70px;" alt="Company Logo">
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table width="100%">
            <tr>
                <td width="80%">
                    <img src="{{ $data['footer_logo'] }}" width="690" height="60" alt="Footer Logo">
                </td>
            </tr>
        </table>
    </footer>

    <!-- ============= Page Content Start =========-->
    <div class="page">
        
        @yield('content')
        
    </div>
    <!-- ============= Page Content End ===========-->
</body>

</html>