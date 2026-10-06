<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จรับเงิน - {{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 13px; margin: 0; padding: 15px; }
        .receipt { max-width: 300px; margin: auto; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        hr { border: 0.5px dashed #555; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="text-center">
            <h2 style="margin: 0;">POS STORE</h2>
            <p style="margin: 2px 0;">ใบเสร็จรับเงินอย่างย่อ</p>
            <p style="margin: 2px 0;">เลขที่: {{ $order->invoice_no }}</p>
            <p style="margin: 2px 0;">วันที่: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p style="margin: 2px 0;">แคชเชียร์: {{ $order->user->name }}</p>
            @if($order->member)
                <p style="margin: 2px 0;">สมาชิก: {{ $order->member->name }}</p>
            @endif
        </div>
        <hr>
        <table>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }} x{{ $item->quantity }}</td>
                <td class="text-right">฿{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </table>
        <hr>
        <table>
            <tr><td>ยอดรวม:</td><td class="text-right">฿{{ number_format($order->subtotal, 2) }}</td></tr>
            @if($order->discount > 0)
            <tr><td>ส่วนลด:</td><td class="text-right">-฿{{ number_format($order->discount, 2) }}</td></tr>
            @endif
            <tr><td><strong>ยอดสุทธิ:</strong></td><td class="text-right"><strong>฿{{ number_format($order->total_amount, 2) }}</strong></td></tr>
            <tr><td>รับเงิน ({{ $order->payment_method }}):</td><td class="text-right">฿{{ number_format($order->cash_received, 2) }}</td></tr>
            <tr><td>เงินทอน:</td><td class="text-right">฿{{ number_format($order->change_amount, 2) }}</td></tr>
        </table>
        <hr>
        <p class="text-center" style="margin-top: 10px;">ขอบคุณที่ใช้บริการ</p>
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer;">พิมพ์ใบเสร็จ</button>
        </div>
    </div>
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>