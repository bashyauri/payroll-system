<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Slip</title>
    <style>
        :root {
            --text-color: #1f2937;
            --bg-color: #ffffff;
            --border-color: #d1d5db;
            --header-bg: #f9fafb;
            --accent-color: #3b82f6;
            --secondary-text: #6b7280;
            --table-header-bg: #f3f4f6;
            --table-row-alt: #f9fafb;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --text-color: #e5e7eb;
                --bg-color: #111827;
                --border-color: #374151;
                --header-bg: #1f2937;
                --accent-color: #60a5fa;
                --secondary-text: #9ca3af;
                --table-header-bg: #1f2937;
                --table-row-alt: #18202f;
            }
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            padding: 20px;
            margin: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 25px;
            background-color: var(--bg-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent-color);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: var(--accent-color);
        }

        .header p {
            margin: 0;
            color: var(--secondary-text);
            font-size: 14px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            color: var(--secondary-text);
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .details-table td {
            padding: 10px;
            border: 1px solid var(--border-color);
        }

        .details-table tr td:first-child {
            font-weight: 600;
            background-color: var(--table-header-bg);
            width: 30%;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .salary-table th,
        .salary-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid var(--border-color);
        }

        .salary-table th {
            background-color: var(--table-header-bg);
            font-weight: 600;
        }

        .salary-table tr:nth-child(even) {
            background-color: var(--table-row-alt);
        }

        .total-row {
            font-weight: 700;
            background-color: var(--table-header-bg) !important;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: var(--secondary-text);
            font-size: 13px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            width: 45%;
            border-top: 1px solid var(--border-color);
            padding-top: 25px;
            text-align: center;
            font-size: 13px;
            color: var(--secondary-text);
        }

        @media print {
            body {
                padding: 0;
            }

            .container {
                border: none;
                box-shadow: none;
                padding: 15px;
            }
        }

        @media (max-width: 640px) {
            .details-table tr td:first-child {
                width: 40%;
            }

            .signature-area {
                flex-direction: column;
            }

            .signature-line {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>PAY SLIP</h1>
            <p>{{ date('F Y', strtotime($salary->year . '-' . $salary->month . '-01')) }}</p>
        </div>

        <div class="company-info">
            <p><strong>Company Name:</strong> {{config('app.name')}}</p>
            <p><strong>Address:</strong> Waziri Umaru Federal Polytechnic</p>
        </div>

        <table class="details-table">
            <tr>
                <td>Employee Name</td>
                <td>{{ $salary->employee->user->name }}</td>
            </tr>
            <tr>
                <td>Employee ID</td>
                <td>{{ $salary->employee->staff_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>Department</td>
                <td>{{ $salary->employee->department->name ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <td>Position</td>
                <td>{{ $salary->employee->position->title ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <td>Pay Period</td>
                <td>{{ $salary->month }} {{ $salary->year }}</td>
            </tr>
            <tr>
                <td>Pay Date</td>
                <td>{{ now()->format('d M Y') }}</td>
            </tr>
        </table>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Earnings</th>
                    <th>Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td>{{ number_format($salary->base_salary, 2) }}</td>
                </tr>
                @if($salary->total_allowances > 0)
                    <tr>
                        <td>Allowances</td>
                        <td>{{ number_format($salary->total_allowances, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Gross Pay</td>
                    <td>{{ number_format($salary->gross_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Deductions</th>
                    <th>Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                @if($salary->total_deductions > 0)
                    <tr>
                        <td>Deductions</td>
                        <td>{{ number_format($salary->total_deductions, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td><strong>Net Pay</strong></td>
                    <td><strong>{{ number_format($salary->net_pay, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="signature-area">
            <div class="signature-line">
                <p>Authorized Signature</p>
            </div>
            <div class="signature-line">
                <p>Employee Signature</p>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated document and does not require a physical signature.</p>
            <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <script>
        // Listen for dark mode preference changes
        const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        function updateColorScheme(e) {
            // This will automatically update due to CSS media queries
            console.log('Color scheme updated to:', e.matches ? 'dark' : 'light');
        }

        // Add listener
        darkModeMediaQuery.addListener(updateColorScheme);
    </script>
</body>

</html>