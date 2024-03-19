<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
    <body>
        <form action="{{ route('payment.process') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <div class="form-group">
            <label for="name">Nama:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $order->name }}" readonly>
        </div>
        <div class="form-group">
            <label for="address">Alamat:</label>
            <textarea name="address" id="address" class="form-control" readonly>{{ $order->address }}</textarea>
        </div>
        <div class="form-group">
            <label for="phone">Telepon:</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $order->phone }}" readonly>
        </div>
        <div class="form-group">
            <label for="qty">Jumlah:</label>
            <input type="number" name="qty" id="qty" class="form-control" value="{{ $order->qty }}" readonly>
        </div>
        <div class="form-group">
            <label for="total_price">Total Harga:</label>
            <input type="text" name="total_price" id="total_price" class="form-control" value="{{ $order->total_price }}" readonly>
        </div>
        <div class="form-group">
            <label for="payment_method">Metode Pembayaran:</label>
            <select name="payment_method" id="payment_method" class="form-control">
                <option value="credit_card">Kartu Kredit</option>
                <option value="bank_transfer">Transfer Bank</option>
                <!-- Tambahkan opsi pembayaran lain sesuai kebutuhan -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
    </form>    
</body>
</html>