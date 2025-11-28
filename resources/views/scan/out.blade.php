<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan Out</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background: #f5f7fa;
            padding-top: 50px;
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }
        .icon-success {
            font-size: 70px;
            color: #28a745;
        }
        .icon-error {
            font-size: 70px;
            color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- ERROR MODE -->
            @if(isset($error))
                <div class="card text-center p-4">
                    <i class="fas fa-times-circle icon-error mb-3"></i>

                    <h3 class="mb-2 text-danger font-weight-bold">Scan Gagal</h3>
                    <p class="text-muted mb-4">{{ $error }}</p>

                    <hr>

                    @can('isUser')
                        <a href="{{ route('index.user') }}" class="btn btn-danger btn-block">
                            <i class="fas fa-redo mr-1"></i> Coba Lagi
                        </a>
                        
                    @endcan

                </div>

            <!-- SUCCESS MODE -->
            @elseif(isset($data))
                <div class="card text-center p-4">
                    <i class="fas fa-check-circle icon-success mb-3"></i>

                    <h3 class="mb-2 text-success font-weight-bold">Scan Out Berhasil!</h3>
                    <p class="text-muted mb-4">Terima kasih telah menggunakan layanan parkir kami.</p>

                    <hr>

                    <div class="text-left px-3">

                        <p><strong>Nomor Slot:</strong> 
                            {{ $data['slot_name'] ?? '-' }}
                        </p>

                        <p><strong>Mulai Parkir:</strong><br>
                            {{ \Carbon\Carbon::parse($data['start_time'])->translatedFormat('l, d F Y H:i') }}
                        </p>

                        <p><strong>Selesai Parkir:</strong><br>
                            {{ \Carbon\Carbon::parse($data['end_time'])->translatedFormat('l, d F Y H:i') }}
                        </p>

                        <p><strong>Total Durasi:</strong><br>
                            <span class="badge badge-info p-2" style="font-size: 14px;">
                                {{ $data['duration_display'] }}
                            </span>
                        </p>

                        <p><strong>Total Biaya:</strong><br>
                            <span class="badge badge-success p-2" style="font-size: 18px;">
                                Rp {{ number_format($data['total_fee'], 0, ',', '.') }}
                            </span>
                        </p>

                    </div>

                     @can('isUser')
                        <a href="{{ route('index.user') }}" class="btn btn-info btn-block">
                            <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                        </a>
                    @endcan

                    <hr>
                </div>

            @endif

        </div>
    </div>
</div>

</body>
</html>
