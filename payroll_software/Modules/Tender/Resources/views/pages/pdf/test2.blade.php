<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tender PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header,
        .footer {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.8em;
        }

        .content {
            margin: 0;
            padding: 0;
        }

        .paragraph {
            margin-top: 2px;
            margin-bottom: 0;
            text-align: justify;
        }

        .ms-1 {
            margin-left: 10px !important;
        }

        .ms-2 {
            margin-left: 20px !important;
        }

        .text-justify {
            text-align: justify !important;
        }

        .ps-2 {
            padding-left: 20px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <table width="100%">
            <tr>
                <td width="50%">
                    <span>Date: 07th Sep-2024</span><br>
                    <span>Ref: ABC-AVR-AG-CMU-008</span>
                </td>
                <td width="50%" style="text-align: right;">
                    <img src="{{ $data['company_logo'] }}" style="width: 250px; height: 90px;" alt="Company Logo">
                </td>
            </tr>
        </table>
    </div>

    <!-- Content -->
    <div class="content">
        <h3 style="margin-bottom: 0;">{{ $data['company_representative_name'] }}</h3>
        <span style="margin-top: 2px;">P.O Box # {{$data['post_box']}}</span><br>
        <span>{{$data['city']}}</span><br>
        <span>Tel: {{$data['phone_number']}}, Fax: {{$data['fax_number']}}</span><br>
        <span>CR. {{$data['cr_no']}}</span><br>
        <span>{{$data['country']}}</span><br>


        <h3 style="margin-bottom: 0;">Attention: {{ $data['company_representative_name'] }}</h3>
        <p style="margin-top: 2px;">{{ $data['company_representative_designation'] }}</p>


        <h3 style="margin-bottom: 0;">Ref Project: {{ $data['ref_project'] }}</h3>
        <p style="margin-top: 2px; margin-left: 40px;">Letter of Intent: {{ $data['letter_of_intent'] }}</p>


        <p style="margin-bottom: 0;">Dear Sir,</p>
        <p class="paragraph">
            We are pleased to advise that, we are prepared to enter into a formal subcontract Agreement with yourselves
            for the provision of Super Structure Installation as described above for {{$data['ref_project']}}.
            This letter constitutes the letter of intent of ASLOOB BEDAA CONTRACTING CO. to enter into such a contract
            with <strong>{{$data['company_representative_name']}}</strong> on a Re-measurable basis constituting, ASLOOB
            BEDAA CONTRACTING CO. Standard Terms and conditions, Bills of Quantities, AFC Drawings, Specifications, Time
            Schedule for a total sum of
        </p>

        <!-- Setting explicit margin values to fully control the spacing around these headings -->
        <h3 style="margin: 5px 0 0 0;">SAR 608000.00</h3>
        <h3 style="margin: 2px 0;">
            (Six hundred and eight thousand Saudi Riyal only, excluding VAT.)
        </h3>
        <p class="paragraph">
            The terms and conditions of the formal agreement shall be as below:
            The payment terms are as set out below; -
        </p>
        <span class="ms-2">
            <strong>1)</strong>
            Advance payment –0%.
        </span><br>

        <span class="ms-2">
            <strong>2)</strong>
            Retention – 0%
        </span><br>

        <span class="ms-2">
            <strong>3)</strong>
            <span>PAYMENT TERMS:</span><br>
            <div class="ps-2">
                3.1) Hundred percent (100%) of the actual executed quantities will be payable after thirty (30) days of
                submitting
                Consultant / Client (Aecom / Nesma) approved executed quantities.
            </div>
        </span><br>
        <span class="ms-2">
            <div class="ps-2 text-justify">
                <strong>4)</strong>
                All works shall be in full compliance with the project specifications, AFC drawings, standard codes and
                practices.
                Co-ordination and interfacing with the MEP, Structural, Architectural subcontractor and any other
                subcontractors whose
                works are adjacent or against or impact the installation of CMU BLOCK & PLASTER Works. Installation of
                CMU BLOCK &
                PLASTER Works system for Shuttering, scaffolding, rebar Cutting-Bending, Rebar fixing, Concrete Pouring
                to complete the
                job,
            </div>
        </span><br>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table width="100%">
            <tr>
                <td width="80%">
                    <img src="{{ $data['footer_logo'] }}" width="690" height="60" alt="Footer Logo">
                </td>
            </tr>
        </table>
    </div>

</body>

</html>