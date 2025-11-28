<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Masuk</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .scan-card {
            border-radius: 15px;
            padding: 35px;
            background: #fff;
            max-width: 600px;
            margin: auto;
            margin-top: 60px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            text-align: center;
        }

        .icon-success { font-size: 80px; color: #28a745; }
        .icon-error { font-size: 80px; color: #dc3545; }
        .detail-box {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">

<div class="scan-card">

    @if($success)

        <i class="fas fa-check-circle icon-success mb-3"></i>
        <h3 class="text-success font-weight-bold">Scan Masuk Berhasil!</h3>

        <div class="detail-box text-left">
            <p><strong>Nama Pemesan:</strong> {{ $reservation->user->name }}</p>
            <p><strong>Kendaraan:</strong> {{ $reservation->vehicle->vehicle_type }}</p>
            <p><strong>Slot Parkir:</strong> {{ $reservation->slot->slot_name }}</p>
            <p><strong>Waktu Masuk:</strong> {{ $parking->start_time }}</p>
        </div>

        @can('isUser')    
            <a href={{ route('index.user') }} class="btn btn-success btn-block mt-4">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        @endcan

    @else

        <i class="fas fa-times-circle icon-error mb-3"></i>
        <h3 class="text-danger font-weight-bold">Scan Gagal!</h3>

        <p class="mt-3">{{ $message }}</p>

        @can('isUser')
        
        <a href="{{ route('index.user') }}" class="btn btn-danger btn-block mt-4">
            <i class="fas fa-redo"></i> Kembali
        </a>
        @endcan

    @endif

</div>

</body>
</html>
