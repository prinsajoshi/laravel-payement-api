<!DOCTYPE html>
<html>

<head>
    <title>Order Confirmation</title>
</head>

<body>
    <h1>Thank you for your order!</h1>
    @if($order)
    <p>Order Number: {{ $order->order_number ?? 'N/A' }}</p>
    <p>Total Amount: {{ $order->total_amount ?? 'N/A' }}</p>
    @endif


    <!-- You can include more details about the order here -->
</body>

</html>