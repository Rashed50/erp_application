@extends('account::pages.layouts.print')

@section('title', 'Profit and Loss Report')

@section('content')
    <div style="width: 100%; font-family: Arial, sans-serif; color: #333;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; margin: 0;">Profit and Loss Report</h2>
            <small style="color: #555;">{{ \Carbon\Carbon::now()->format('F j, Y') }}</small>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Revenue</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f9f9f9;">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Total Revenue</td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">{{ number_format($totalRevenue, 2) }}</td>
                </tr>
            </table>

            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Expenses</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f9f9f9;">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Total Expenses</td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">{{ number_format($totalExpense, 2) }}</td>
                </tr>
            </table>

            <h3 style="background-color: #f2f2f2; padding: 10px; border-radius: 5px;">Summary</h3>
            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
                <tr style="background-color: {{ $profitOrLoss > 0 ? '#d4edda' : ($profitOrLoss < 0 ? '#f8d7da' : '#fff3cd') }};">
                    <td width="70%" style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">
                        {{ $profitOrLoss > 0 ? 'Profit' : ($profitOrLoss < 0 ? 'Loss' : 'Break-Even') }}
                    </td>
                    <td width="30%" style="border: 1px solid #ddd; text-align: right;">
                        {{ $result }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
