<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Compra</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0;">
    <h1 style="text-align: center;">Gracias por su compra!</h1>
    <div class="receipt" style="margin: 0 auto; max-width: 600px; padding: 20px;">
        <div class="container" style="background: #fff; box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); padding: 25px;">
            <div class="receipt_header" style="text-align: center; padding-bottom: 40px; border-bottom: 1px dashed #000;">
                <h1 style="font-size: 20px; margin-bottom: 5px; text-transform: uppercase;">Ticket de Compra <br><span></span></h1>
            </div>
            <div class="receipt_body">
                <div class="date_time_con" style="display: flex; justify-content: center; column-gap: 25px;">
                    <div class="date">{{$date}}</div>
                </div>
                <div class="items" style="margin-top: 25px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <th style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">QTY</th>
                            <th style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">ITEM</th>
                            <th style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">AMT</th>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">{{ $product['quantity'] }}</td>
                                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">{{ $product['name'] }}</td>
                                    <td style="border-bottom: 1px solid #ddd; padding: 8px; text-align: left;">${{ $product['price'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="padding: 8px; text-align: left;">Total</td>
                                <td></td>
                                <td style="padding: 8px; text-align: left;">${{$total}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <h3 style="border-top: 1px dashed #000; padding-top: 10px; margin-top: 25px; text-align: center; text-transform: uppercase;">Thank You!</h3>
        </div>
    </div>
</body>
</html>