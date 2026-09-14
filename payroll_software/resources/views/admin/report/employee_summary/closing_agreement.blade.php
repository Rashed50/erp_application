<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Final Settlement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* GLOBAL FONT & RESET */
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 14px;
            font-family: 'Inter', 'Hind Siliguri', Arial, sans-serif;
            color: #2c3e50;
            line-height: 1.6;
        }

        /* Professional A4 Portrait Wrapper */
        .page {
            background-color: white;
            width: 210mm;
            height: 297mm;
            padding: 15mm 20mm 15mm 20mm;
            margin: 0 auto 25px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e1e8ed;

            /* হালকা ওয়াটারমার্ক */
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='700' height='1000' viewBox='0 0 700 1000'><text fill='rgba(0,0,0,0.025)' font-family='Arial, sans-serif' font-weight='300' font-size='50' x='350' y='500' text-anchor='middle' transform='rotate(-35 350 500)'>ASLOOB BEDAA CO.</text></svg>");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }

        /* হেডার ও বর্ডার অপ্টিমাইজেশন */
        header {
            width: 100%;
            margin-top: -5px;
            padding-bottom: 8px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            border-bottom: 1.2px solid #cbd5e1;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .print__button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }

        /* MAIN CONTENT AREA */
        .content-body {
            width: 100%;
            z-index: 1;
            flex-grow: 1; /* ফুটারকে নিচে পুশ করার জন্য এই অংশটি পুরো খালি জায়গা নেবে */
        }

        .header-title {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 20px 0;
            color: #1a252f;
            font-size: 20px;
            font-weight: bold;
        }

        .bn-title {
            text-align: center;
            font-size: 22px;
            font-family: 'Hind Siliguri', sans-serif;
            font-weight: 700;
            margin: 0 0 20px 0;
        }

        .meta-info {
            margin-bottom: 15px;
        }

        .meta-info p {
            margin: 4px 0;
        }

        .subject-line {
            margin-top: 10px !important;
            margin-bottom: 15px !important;
        }

        .statement-text {
            margin-bottom: 15px;
            text-align: justify;
        }

        .declaration-list {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .declaration-list li {
            margin-bottom: 8px;
            text-align: justify;
            color: #34495e;
        }

        /* 📸 SIGNATURE SECTION FIX (কন্টেন্টের নিচে গ্যাপ রেখে ৩টি লাইন পর পর আসবে) */
        .custom-sig-section {
            margin-top: 35px; /* বডি টেক্সট শেষ হওয়ার পর একটি মার্জিত গ্যাপ */
            text-align: left;
        }

        .custom-sig-section p {
            margin: 22px 0; /* ৩টি লাইন যেন একটার নিচে আরেকটা সঠিক দূরত্বে বসে */
            font-size: 14.5px;
            color: #000;
        }

        /* OFFICIAL FOOTER (সবসময় পেজের একদম নিচে থাকবে) */
        .official-footer {
            width: 100%;
            margin-top: auto; /* ফ্লেক্স লেআউটের কারণে ফুটারটি পেজের সবচেয়ে নিচে ফিক্সড থাকবে */
            z-index: 1;
        }

        .disclaimer {
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
        }

        /* ADVANCED PRINT OPTIMIZATION */
        @media print {
            body {
                background-color: white;
                padding: 0;
                display: block;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            .page {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 15mm 20mm 15mm 20mm;
                box-shadow: none;
                border: none;
                page-break-after: always;
                break-after: page;
                background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='700' height='1000' viewBox='0 0 700 1000'><text fill='rgba(0,0,0,0.025)' font-family='Arial, sans-serif' font-weight='300' font-size='50' x='350' y='500' text-anchor='middle' transform='rotate(-35 350 500)'>ASLOOB BEDAA CO.</text></svg>") !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
         {{-- This is Arabic version of closing agreement --}}
    <div class="page">
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 80%; text-align: left; padding-left: 10px !important;">
                    <div style="font-size: 22px; font-weight: bold; color: #1e293b;">{{ $company->comp_name_en }}</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">{{ $company->comp_address }}</div>
                    <button onclick="window.print()" class="print__button no-print">PRINT</button>
                </td>
                <td style="width: 20%; text-align: right;">
                    <img src="{{ asset('contents/admin/assets/images/logo_new_2.png') }}" alt="Logo" style="height: 78px;">
                </td>
            </tr>
        </table>
    </header>

    <div class="content-body" dir="rtl">
        <!-- Main Title -->
        <h2 class="header-title" style="font-size: 22px; text-align: center; margin-top:20px;">
            مخالصة وإقرار باستلام المستحقات النظامية
        </h2>

        <!-- Employee Details Table -->
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #cbd5e1;">
            <thead>
                <tr style="background-color: #f1f5f9;">
                    <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 600;">الجنسية</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 600;">رقم الإقامة</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 600;">اسم الموظف</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 500;">
                        {{  Str::upper($employee->country->country_name ?? '' ) }}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 500;">
                        {{ Str::upper($employee->akama_no ?? $employee->passfort_no)}}
                    </td>
                    <td style="border: 1px solid #cbd5e1; padding: 8px 12px; text-align: center; font-weight: 500;">
                        {{ Str::upper($employee->employee_name ?? '') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Declaration Text -->
        <p style="text-align: justify; font-size: 15px; line-height: 2; margin-bottom: 15px;">
            بأنني استلمت كافة مستحقاتي النظامية من الأجور والأجور الإضافية وبدلات الإجازة ومكافأة نهاية الخدمة حسب نظام العمل والعمال السعودي من
            <strong>({{ $company->comp_name_en }})</strong> وعنوانها ، منذ بداية عملي لديهم وحتى تاريخ ترك العمل، وأنني بالتوقيع على هذا الإقرار أخلي طرف
             من أية مستحقات تتعلق بي، كما أسقط أية دعاوى أو قضايا أو منازعات لدى أية جهة كانت حاليا أو سابقا، تتعلق بعملي لدى
             وتكون صلتي بالشركة قد انقطعت نهائيا، كما أتعهد بعدم المنازعة أمام أية جهة كانت مستقبلا فيما يتعلق بانتهاء عملي لدى الشركة أو فيما يتعلق بالحصول على رواتبي ومستحقاتي النظامية كما أقر بأنني وقعت على هذا الإقرار بمحض إرادتي واختياري الكاملين دون ضغط أو إكراه من أحد بعد تسوية كافة الخلافات والحصول علي كامل حقوقي لدي الشركة وتلبية جميع طلباتي.
        </p>

        <!-- Closing Statement -->
        <p style="text-align: center; font-size: 15px; font-weight: 800; margin: 40px 0 10px 0; color:black">
            وهذا إقرار وتعهد مني بما ورد أعلاه ،،،
        </p>

        <p style="text-align: center; font-size: 15px; margin: 5px 0 15px 0;">
            المقر بما فيه
        </p>

        <!-- Signature Section -->
        <div class="custom-sig-section" style="margin-top: 40px;">
            <p style="font-size: 15px; margin: 12px 0;">
                <strong>الاسم :</strong> {{  Str::upper($employee->employee_name ?? '') }}
            </p>
            <p style="font-size: 15px; margin: 12px 0;">
                <strong>التوقيع :</strong> _________________________________________
            </p>
            <p style="font-size: 15px; margin: 12px 0;">
                <strong>التاريخ :</strong> {{ date('d/m/Y') }}
            </p>
        </div>
    </div>

    <div class="official-footer">
        <div class="disclaimer">
            هذا تقرير تم إنشاؤه بواسطة الكمبيوتر. لا يلزم التوقيع لمصادقة السجلات الرقمية. مؤتمن {{ $company->comp_name_en }}
        </div>
    </div>
</div>

    {{-- this is English version of closing agreement --}}
    <div class="page">
        <header>
            <table class="header-table">
                <tr>
                    <td style="width: 80%; text-align: left; padding-left: 10px !important;">
                        <div style="font-size: 22px; font-weight: bold; color: #1e293b;">{{ $company->comp_name_en }}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">{{ $company->comp_address }}</div>
                        <button onclick="window.print()" class="print__button no-print">PRINT</button>
                    </td>
                    <td style="width: 20%; text-align: right;">
                        <img src="{{ asset('contents/admin/assets/images/logo_new_2.png') }}" alt="Logo" style="height: 48px;">
                    </td>
                </tr>
            </table>
        </header>

        <div class="content-body">
            <h2 class="header-title">Final Settlement Declaration</h2>

            <div class="meta-info">
                <p><strong>Date:</strong> {{ date('d.m.Y') }}</p>
                <p style="margin-top: 10px;"><strong>To,</strong></p>
                <p style="font-weight: bold; color: #1e293b;">{{ $company->comp_name_en }}</p>
                <p class="subject-line"><strong>Subject: Full & Final Settlement Declaration</strong></p>
            </div>

            <p class="statement-text">
                I, <strong>{{ $employee->employee_name }}</strong>, holding Passport No. <strong>{{ $employee->passfort_no }}</strong>, hereby confirm that I have received all my dues from <strong>{{ $company->comp_name_en }}</strong> before proceeding on vacation to my home country (<strong>{{ $employee->country->country_name }}</strong>).
            </p>

            <p style="margin: 0 0 5px 0;">Details:</p>
            <ul style="list-style-type: disc; margin: 0 0 15px 20px; padding: 0;">
                <li style="margin-bottom: 6px;">Employee Name: <strong>{{ $employee->employee_name }}</strong></li>
                <li style="margin-bottom: 6px;">Employee ID: <strong>{{ $employee->employee_id }}</strong></li>
            </ul>

            <p style="margin: 0 0 10px 0;">I clearly declare and confirm the following:</p>
            <ol class="declaration-list">
                <li>I have received my full and final settlement including salary, overtime, leave salary, and any other benefits due to me.</li>
                <li>I have no pending claims, dues, or financial entitlements against the company.</li>
                <li>I am leaving for my vacation after complete settlement of all my accounts.</li>
                <li>I will not raise any dispute, complaint, or legal claim against the company after returning to my home country.</li>
                <li>This declaration is made by me voluntarily, without any pressure or force.</li>
            </ol>

            <p class="statement-text">
                I fully understand and accept that after signing this document, I have no further rights to claim any amount or benefit from the company.
            </p>

            <div class="custom-sig-section">
                <p>Employee Signature: _________________________________________</p>
                <p>Name: <span style="text-decoration: underline;">{{ $employee->employee_name }}</span></p>
                <p>Date: _________________________________________</p>
            </div>
        </div>

        <div class="official-footer">
            <div class="disclaimer">
                This is a computer-generated report. No signature is required for authentication of digital records. Confidential - {{ $company->comp_name_en }}
            </div>
        </div>
    </div>

     {{-- this is bangla version --}}
    <div class="page">
        <header>
            <table class="header-table">
                <tr>
                    <td style="width: 80%; text-align: left; padding-left: 10px !important;">
                        <div style="font-size: 22px; font-weight: bold; color: #1e293b;">{{ $company->comp_name_en }}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">{{ $company->comp_address }}</div>
                    </td>
                    <td style="width: 20%; text-align: right;">
                        <img src="{{ asset('contents/admin/assets/images/logo_new_2.png') }}" alt="Logo" style="height: 48px;">
                    </td>
                </tr>
            </table>
        </header>

        <div class="content-body">
            <h2 class="bn-title">চূড়ান্ত নিষ্পত্তি ঘোষণা পত্র</h2>

            <div class="meta-info">
                <p><strong>তারিখ:</strong> {{ date('d.m.Y') }}</p>
                <div class="recipient-block">
                    <p><strong>প্রাপক,</strong></p>
                    <p style="font-weight: bold; color: #1e293b;">{{ $company->comp_name_en }}</p>
                </div>
                <p class="subject-line"><strong>বিষয়: সম্পূর্ণ ও চূড়ান্ত হিসাব নিষ্পত্তি ঘোষণা</strong></p>
            </div>

            <p class="statement-text">
                আমি, <strong>{{ $employee->employee_name }}</strong>, পাসপোর্ট নম্বর <strong>{{ $employee->passfort_no }}</strong>, এই মর্মে ঘোষণা করছি যে, আমি <strong>{{ $company->comp_name_en }}</strong> থেকে আমার সকল পাওনা সম্পূর্ণভাবে গ্রহণ করেছি এবং আমার নিজ দেশ (<strong>{{ $employee->country->country_name }}</strong>) ছুটিতে যাওয়ার পূর্বে আমার সমস্ত হিসাব নিষ্পত্তি করা হয়েছে।
            </p>

            <p style="margin: 0 0 5px 0;">বিস্তারিত:</p>
            <ul style="list-style-type: disc; margin: 0 0 15px 20px; padding: 0;">
                <li style="margin-bottom: 6px;">কর্মচারীর নাম: <strong>{{ $employee->employee_name }}</strong></li>
                <li style="margin-bottom: 6px;">কর্মচারী আইডি: <strong>{{ $employee->employee_id }}</strong></li>
            </ul>

            <p style="margin: 0 0 10px 0;">আমি নিম্নলিখিত বিষয়গুলো স্পষ্টভাবে স্বীকার ও ঘোষণা করছি:</p>
            <ol class="declaration-list">
                <li>আমি আমার সম্পূর্ণ বেতন, ওভারটাইম, ছুটির বেতন এবং অন্যান্য সকল প্রাপ্য অর্থ গ্রহণ করেছি।</li>
                <li>কোম্পানির নিকট আমার কোনো প্রকার দাবি, পাওনা বা অভিযোগ অবশিষ্ট নেই।</li>
                <li>আমি সম্পূর্ণ হিসাব নিষ্পত্তি করেই নিজ দেশে ({{ $employee->country->country_name }}) যাচ্ছি।</li>
                <li>দেশে যাওয়ার পর আমি কোম্পানির বিরুদ্ধে কোনো প্রকার অভিযোগ, দাবি বা আইনগত পদক্ষেপ গ্রহণ করব না।</li>
                <li>আমি এই ঘোষণাপত্রটি স্বেচ্ছায় এবং কোনো প্রকার চাপ বা জোরজবরদস্তি ছাড়াই প্রদান করছি।</li>
            </ol>

            <p class="statement-text">
                আমি সম্পূর্ণভাবে বুঝে এবং সম্মত হয়ে এই ঘোষণাপত্রে স্বাক্ষর করছি যে, ভবিষ্যতে কোম্পানির নিকট আমার কোনো দাবি থাকবে না।
            </p>

            <div class="custom-sig-section">
                <p>কর্মচারীর স্বাক্ষর: _________________________________________</p>
                <p>নাম: <span style="text-decoration: underline;">{{ $employee->employee_name }}</span></p>
                <p>পাসপোর্ট নম্বর: _________________________________________</p>
            </div>
        </div>

        <div class="official-footer">
            <div class="disclaimer">
                This is a computer-generated report. No signature is required for authentication of digital records. Confidential - {{ $company->comp_name_en }}
            </div>
        </div>
    </div>

</body>
</html>
