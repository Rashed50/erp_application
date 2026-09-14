<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tender PDF</title>
    <style>
        @page {
            margin: 100px 25px;
            /* Adjust top and bottom margin to fit header and footer */
        }

        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }
    </style>
</head>

<body>
    <header>
        <table width="100%">
            <tr>
                <td width="50%">
                    <span>Date: {{ $data['header']['date'] }}</span><br>
                    <span>Ref: {{ $data['header']['ref'] }}</span>
                </td>
                <td width="50%" style="text-align: right;">
                    <img src="{{ $data['header']['company_logo'] }}" style="width: 250px; height: 90px;"
                        alt="Company Logo">
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table width="100%">
            <tr>
                <td width="80%">
                    <img src="{{ $data['footer']['footer_logo'] }}" width="730" height="60" alt="Footer Logo">
                </td>
            </tr>
        </table>
    </footer>

    @foreach ($data['content'] as $page)
    <div class="page">
        <h1>{{ $page['title'] }}</h1>
        <p>{{ $page['body'] }}</p>
    </div>
    @endforeach
</body>

</html>