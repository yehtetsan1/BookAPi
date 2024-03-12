<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @foreach ($orderDetails as $orderDetail)
        <h2>Orderdetials Id{{$orderDetail->id}}</h2>
        <p>BookName{{$orderDetail->book->title}}</p>
        <p>OrderAmount{{$orderDetail->book->amount}}</p>
    @endforeach
</body>
</html>
