@extends('account::pages.layouts.print')

@section('title', 'Balance Sheet Report')

@section('content')
    <div style="width: 100%; font-family: Arial, sans-serif; color: #333;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; margin: 0;">Balance Sheet Report</h2>
            <small style="color: #555;">{{ \Carbon\Carbon::now()->format('F j, Y') }}</small>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Assets</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f9f9f9;">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Total Assets</td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">{{ number_format($totalAssets, 2) }}</td>
                </tr>
            </table>

            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Liabilities</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f9f9f9;">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Total Liabilities</td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">{{ number_format($totalLiabilities, 2) }}</td>
                </tr>
            </table>

            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Owner's Equity</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f9f9f9;">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Total Owner's Equity</td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">{{ number_format($totalEquity, 2) }}</td>
                </tr>
            </table>

            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Balance Check</h3>
            <p style="font-size: 16px; padding: 10px; border: 1px solid #ddd; background-color: {{ $isBalanced ? '#d4edda' : '#f8d7da' }}; color: {{ $isBalanced ? '#155724' : '#721c24' }};">
                {{ $isBalanced ? 'The Balance Sheet is Balanced' : 'The Balance Sheet is Not Balanced' }}
            </p>
        </div>
    </div>
@endsection
