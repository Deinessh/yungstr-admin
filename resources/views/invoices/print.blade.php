<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->invoice_number }} — {{ $brandName }}</title>
    <style>
        :root {
            color: #3A1F12;
        }
        * { box-sizing: border-box; }
        body {
            font-family: Inter, system-ui, -apple-system, sans-serif;
            font-size: 13px;
            color: #3A1F12;
            margin: 0;
            background: #f5f0ea;
        }
        .pre-line { white-space: pre-line; }
        .invoice-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            background: #fff;
            border-bottom: 1px solid #FBD3B0;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .invoice-toolbar__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn-primary {
            background: #F25C2E;
            color: #fff;
        }
        .btn-outline {
            background: #fff;
            color: #3A1F12;
            border-color: #FBD3B0;
        }
        .receipt-sheet {
            max-width: 900px;
            margin: 1.5rem auto 2rem;
            background: #fff;
            border: 1px solid #FBD3B0;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(58, 31, 18, 0.08);
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .header .left, .header .right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .header .right { text-align: right; }
        .title {
            font-size: 24px;
            font-weight: 800;
            color: #F25C2E;
            margin: 0 0 0.25rem;
        }
        .muted { color: #666; margin: 0.15rem 0; }
        .items-table, .totals {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .items-table th, .items-table td {
            border: 1px solid #FBD3B0;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .items-table th { background: #FFF1E0; }
        .totals {
            width: 320px;
            float: right;
            margin-top: 16px;
        }
        .totals td {
            border: none;
            padding: 4px 8px;
        }
        .totals .label { text-align: right; }
        .grand {
            font-size: 16px;
            font-weight: 800;
            color: #F25C2E;
        }
        .item-meta {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
            line-height: 1.4;
        }
        .policy {
            margin-top: 24px;
            padding: 10px 12px;
            background: #FFF8F0;
            border: 1px solid #FBD3B0;
            border-radius: 10px;
            font-size: 11px;
        }
        .footer-note {
            margin-top: 40px;
            font-size: 11px;
            color: #888;
        }
        .clearfix { clear: both; }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .receipt-sheet {
                margin: 0;
                border: none;
                border-radius: 0;
                box-shadow: none;
                max-width: none;
                padding: 0;
            }
            @page {
                margin: 12mm;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-toolbar no-print">
        <div>
            <strong>{{ $brandName }}</strong>
            <span class="muted"> · Invoice {{ $order->invoice_number }}</span>
        </div>
        <div class="invoice-toolbar__actions">
            <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
            <a href="{{ $downloadUrl }}" class="btn btn-outline">Download PDF</a>
            @if($backUrl)
                <a href="{{ $backUrl }}" class="btn btn-outline">Back</a>
            @endif
        </div>
    </div>

    <div class="receipt-sheet">
        @include('invoices.partials.receipt-content')
    </div>

    @if($autoprint ?? false)
    <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
