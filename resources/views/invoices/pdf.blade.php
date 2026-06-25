<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #3A1F12; }
        .header { display: table; width: 100%; margin-bottom: 24px; }
        .header .left, .header .right { display: table-cell; vertical-align: top; width: 50%; }
        .header .right { text-align: right; }
        .title { font-size: 22px; font-weight: bold; color: #F25C2E; margin: 0; }
        .muted { color: #666; }
        .pre-line { white-space: pre-line; }
        .items-table, .totals { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .items-table th, .items-table td { border: 1px solid #FBD3B0; padding: 8px; text-align: left; vertical-align: top; }
        .items-table th { background: #FFF1E0; }
        .totals { width: 320px; float: right; }
        .totals td { border: none; padding: 4px 8px; }
        .totals .label { text-align: right; }
        .grand { font-size: 16px; font-weight: bold; color: #F25C2E; }
        .item-meta { font-size: 10px; color: #666; margin-top: 4px; line-height: 1.4; }
        .policy { margin-top: 24px; padding: 10px; background: #FFF8F0; border: 1px solid #FBD3B0; font-size: 10px; }
        .footer-note { margin-top: 40px; font-size: 10px; color: #888; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    @include('invoices.partials.receipt-content')
</body>
</html>
