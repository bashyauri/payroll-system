<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pay Slip</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details,
        .salary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .details td,
        .salary th,
        .salary td {
            border: 1px solid #000;
            padding: 6px;
        }

        .salary th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>

    <div class="header">Pay Slip</div>

    <table class="details">
        <tr>
            <td><strong>Name:</strong> {{ $employee->user->name }}</td>
            <td><strong>Month:</strong> {{ $salary->month }}</td>
        </tr>
        <tr>
            <td><strong>Employee ID:</strong> {{ $employee->staff_id ?? '-' }}</td>
            <td><strong>Year:</strong> {{ $salary->year }}</td>
        </tr>
    </table>

    <table class="salary">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Base Salary</td>
                <td>{{ number_format($salary->base_salary, 2) }}</td>
            </tr>
            <tr>
                <td>Total Allowances</td>
                <td>{{ number_format($salary->total_allowances, 2) }}</td>
            </tr>
            <tr>
                <td>Total Deductions</td>
                <td>{{ number_format($salary->total_deductions, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Gross Pay</strong></td>
                <td><strong>{{ number_format($salary->gross_pay, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Net Pay</strong></td>
                <td><strong>{{ number_format($salary->net_pay, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px;">Generated on: {{ now()->format('d M Y, h:i A') }}</p>

</body>

</html>