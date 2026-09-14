<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll @yield('title') </title>
</head>

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
        font-size: 8pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1pt solid #e9e9e9;
        padding: 5px;
    }

    th {
        background-color: #f0f0f0;
    }

    .header {
        margin-bottom: 20px;
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

    .text-end {
        text-align: right;
    }

    .text-nowrap {
        white-space: nowrap;
    }

    .text-justify {
        text-align: justify;
    }

    .text-bold {
        font-weight: bold;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-capitalize {
        text-transform: capitalize;
    }

    .text-middle {
        vertical-align: middle;
    }

    .text-center {
        text-align: center;
    }

    .row {
        display: flex;
        width: 100%;
        margin-right: -5px;
        margin-left: -5px;
    }

    .col-12 {
        max-width: 100%;
        width: 100%;
        position: relative;
        padding-right: 5px;
        padding-left: 5px;
    }


    .col-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
        position: relative;
        width: 100%;
        padding-right: 5px;
        padding-left: 5px;
    }

    .col-6 {
        flex: 0 0 50%;
        max-width: 50%;
        position: relative;
        width: 100%;
        padding-right: 5px;
        padding-left: 5px;
    }

    .col-auto {
        flex: 0 0 auto;
        width: auto;
        max-width: 100%;
        position: relative;
        padding-right: 5px;
        padding-left: 5px;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .py-2 {
        padding-top: .5rem;
        padding-bottom: .5rem;
    }

    .px-2 {
        padding-left: .5rem;
        padding-right: .5rem;
    }

    .pt-2 {
        padding-top: .5rem;
    }

    .pb-1 {
        padding-bottom: .25rem;
    }

    .p-0 {
        padding: 0;
    }

    .m-0 {
        margin: 0;
    }

    .bg-secondary {
        background-color: #f0f0f0 !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .border {
        border: 1px solid #e9e9e9 !important;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .justify-content-around {
        justify-content: space-around;
    }


    .inline-block-container {
        width: 100%;
    }

    .inline-block-item {
        display: inline-block;
        width: 50%;
        border: 1px solid black;
        padding: 5px;
    }
</style>


<body>

<header class="header">
    <img class="logo" src="" alt="Logo">
    <div class="company">
        <p><b>ASLOOB BEDAA</b></p>
        <p>A Construction Company of Excellence</p>
    </div>
</header>

@yield('content')
</body>
</html>



{{-- 



@extends('tender::pages.layouts.print')

@section('title', 'Tender Documents')

@section('content')
    
@endsection --}}
