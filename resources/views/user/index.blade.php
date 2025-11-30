@extends('layouts.index')


@section('kontent')

<style>
    .card-modern {
        border-radius: 12px;
        overflow: hidden;
    }
    .section-title {
        font-weight: 700;
        font-size: 18px;
        letter-spacing: .5px;
    }
    .icon-title {
        font-size: 20px;
        margin-right: 6px;
    }
    .form-label {
        font-weight: 600;
    }
    #btn-qr-area .btn {
        border-radius: 10px;
        font-weight: 600;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>


 <div class="page-breadcrumb">
            <div class="row">
                <div class="col-7 align-self-center">
                    <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Selamat Datang {{ Auth::user()->name ?? 'Guest' }}</h3>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 p-0">
                                <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                                <li class="breadcrumb-item text-muted active" aria-current="page">Reservasi Parkir</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="col-5 align-self-center">
                    {{-- <div class="customize-input float-right">
                        <button class="btn btn-outline-cyan btn-rounded">
                            <h5 id="date" class="pt-2"></h5>
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>

<div class="container mt-4">

    

    <div class="row">

        {{-- ============================
             FORM RESERVASI
        ============================= --}}
       

        <div class="col-md-5">

            <div class="card shadow card-modern mb-3">
                <div class="card-header bg-info text-white d-flex align-items-center">
                    <i class="fas fa-parking icon-title"></i>
                    <span class="section-title">Buat Reservasi Parkir</span>
                </div>

                <div class="card-body">

                    <form id="form-reservasi">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Pilih Kendaraan</label>
                            <select name="vehicle_id" class="form-control" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->brand }} - {{ $vehicle->plate_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Slot Parkir</label>
                            <select name="slot_id" id="slot-select" class="form-control" required>
                                <option value="">-- Pilih Slot --</option>
                            </select>
                        </div>

                        <button type="submit" id="btn-submit" class="btn btn-primary btn-block py-2">
                            <i class="fas fa-ticket-alt mr-2"></i>Buat Reservasi
                        </button>
                    </form>

                </div>
            </div>

            {{-- QR BUTTON --}}
            {{-- <div id="btn-qr-area" class="text-center"></div> --}}
                <div id="btn-qr-area" class="mt-3 mb-3 text-center"></div>

        </div>

        {{-- ============================
             HISTORY
        ============================= --}}
        <div class="col-md-7">

            <div class="card shadow card-modern">
                <div class="card-header bg-warning text-white d-flex align-items-center">
                    <i class="fas fa-history icon-title"></i>
                    <span class="section-title">Riwayat Parkir</span>
                </div>

               


                <div class="card-body">
                     <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filter-bulan" class="form-control">
                                <option value="">-- Semua Bulan --</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                   <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="historyTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Slot</th>
                                    <th>Status</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                </tr>
                            </thead>
                            <tbody id="history-body"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>


{{-- ============================
     MODAL QR
============================= --}}
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content card-modern">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-qrcode mr-2"></i>QR Code Reservasi</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">

                <img id="qr-image" src="" class="img-fluid mb-2" width="220">

                <p class="text-muted">Scan QR di pintu masuk untuk Scan In / Scan Out</p>

                <h4 id="countdown" class="text-danger font-weight-bold mt-3"></h4>

                <div id="expiredAlert" class="alert alert-danger mt-3" style="display:none;">
                    QR Code telah kadaluarsa!
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-block" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>





{{-- AJAX SCRIPT --}}
<script>
let table;
let countdownInterval;
let lastQR = null;

$(document).ready(function(){
    table = $('#historyTable').DataTable();
    loadHistory();
    setInterval(loadHistory, 3000); // auto refresh
});

$("#filter-bulan").on("change", function () {
    loadHistory();
});


function beautifyDate(dateString){
    if(!dateString) return "-";
    const d = new Date(dateString);
    return d.toLocaleString("id-ID", {
        weekday: "long",
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}



function statusBadge(status){
    let color = "secondary"; // default

    if(status === "booked") color = "warning";
    if(status === "active" || status === "ongoing") color = "success";
    if(status === "completed") color = "info";
    if(status === "expired") color = "danger";

    return `<span class="badge badge-pill badge-${color}">${status.toUpperCase()}</span>`;
}

function loadHistory(){
    let bulan = $("#filter-bulan").val();

    $.get("/user/reservasi/history", function(res){

        table.clear();
        $("#btn-qr-area").html("");
        $("#expiredAlert").hide();

        // FILTER BULAN
        if (bulan !== "") {
            res = res.filter(row => {
                if (!row.start_time) return false;
                let month = new Date(row.start_time).getMonth() + 1;
                month = month.toString().padStart(2, '0');
                return month === bulan;
            });
        }

        if(res.length === 0){
            table.row.add(["-", "-", "-", "-", "-",
                "<span class='text-muted'>Tidak ada data pada bulan ini.</span>"
            ]).draw();
            return;
        }

        // Ambil reservasi terbaru
        let latest = res.sort((a,b) => new Date(b.created_at) - new Date(a.created_at))[0];
        let status = (latest.status || "").toLowerCase();

        // // RESET COUNTDOWN
        // clearInterval(countdownInterval);
        // $("#countdown").text("");

        if(status !== "booked"){
            clearInterval(countdownInterval);
            $("#countdown").text("");
        }

        // ===================================
        // STATUS BOOKED
        // ===================================
        if(status === "booked"){

            lastQR = {
                qrcode_token: latest.qrcode_token,
                expired_at: latest.expired_at
            };

            $("#expiredAlert")
                .removeClass("alert-danger alert-success alert-info")
                .addClass("alert-warning")
                .text("Anda memiliki reservasi BOOKED. Silakan melakukan Scan IN di lokasi parkir.")
                .show();

            $("#btn-qr-area").html(`
                <button onclick="openQR('in')" class="btn btn-warning btn-block">
                    <i class="fas fa-sign-in-alt mr-2"></i> Tampilkan QR Scan In
                </button>
            `);

            let expiredAt = new Date(latest.expired_at.replace(" ","T")).getTime();
            startCountdown(expiredAt);
        }

        // ===================================
        // STATUS ACTIVE / ONGOING
        // ===================================
        else if(status === "active" || status === "ongoing"){

            lastQR = {
                qrcode_token: latest.qrcode_token,
                qrcode_out: latest.qrcode_out,
                expired_at: latest.expired_at
            };

            $("#expiredAlert")
                .removeClass("alert-warning")
                .addClass("alert-success")
                .text("Silahkan Scan OUT ketika akan keluar dari area parkir.")
                .show();

            $("#btn-qr-area").html(`
                <a href="/scan-user/${latest.qrcode_token}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-info-circle mr-2"></i> Lihat Status Parkir
                </a>

                <button onclick="openQR('out')" class="btn btn-danger btn-block">
                    <i class="fas fa-sign-out-alt mr-2"></i> Tampilkan QR Scan OUT
                </button>
            `);

            
        }

        // ===================================
        // STATUS COMPLETED
        // ===================================
        else if(status === "completed"){

            lastQR = {
                qrcode_token: latest.qrcode_token,
                qrcode_out: latest.qrcode_out
            };

            $("#btn-qr-area").html(`
                <a href="/scanout-user/${latest.qrcode_out}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-info-circle mr-2"></i> Lihat Status Pembayaran
                </a>
            `);

            $("#expiredAlert")
                .removeClass("alert-danger")
                .addClass("alert-info")
                .text("Anda sudah Scan OUT. Parkir selesai.")
                .show();
        }

        res.forEach((r,i) => {
            table.row.add([
                i+1,
                r.slot?.slot_name ?? r.slot_id,
                statusBadge(r.status),
                beautifyDate(r.start_time ?? "-"),
                beautifyDate(r.end_time ?? "-")
            ]);
        });

        table.draw();
    });
}

function openQR(type){
    if(!lastQR){
        alert("Tidak ada reservasi aktif");
        return;
    }

    let endpoint = "";
    let token = "";

    if(type === "in"){
        endpoint = "scan-user/" + lastQR.qrcode_token;
        token = lastQR.qrcode_token;
    } else if(type === "out"){
        endpoint = "scanout-user/" + lastQR.qrcode_out;
        token = lastQR.qrcode_out;
    }

    let qrScanUrl = `https://smartparkingft.com/${endpoint}?token=${token}`;

    let qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(qrScanUrl)}`;

    $("#qr-image").attr("src", qrImgUrl);
    $("#expiredAlert").hide();

    if(lastQR.expired_at){
        let expiredAt = new Date(lastQR.expired_at.replace(" ", "T")).getTime();
        startCountdown(expiredAt);
    }

    $("#qrModal").modal("show");
}

// COUNTDOWN QR
function startCountdown(expiredTimestamp){

    clearInterval(countdownInterval);

    countdownInterval = setInterval(() => {

        let now = new Date().getTime();
        let diff = expiredTimestamp - now;

        if(diff <= 0){
            clearInterval(countdownInterval);
            $("#countdown").text("00:00");
            $("#expiredAlert").show();
            return;
        }

        let minutes = Math.floor(diff / (1000 * 60));
        let seconds = Math.floor((diff % (1000 * 60)) / 1000);

        $("#countdown").text(
            (minutes < 10 ? "0"+minutes : minutes) + ":" +
            (seconds < 10 ? "0"+seconds : seconds)
        );

    }, 1000);
}

// ===============================
// FILTER SLOT BERDASARKAN KENDARAAN
// ===============================
$("select[name='vehicle_id']").on("change", function () {
    let vehicleId = $(this).val();

    if(!vehicleId){
        $("select[name='slot_id']").html('<option value="">-- Pilih Slot --</option>');
        return;
    }

    $.get("/user/slot-by-vehicle/" + vehicleId, function(res){
        
        let html = '<option value="">-- Pilih Slot --</option>';

        if(res.length === 0){
            html += '<option value="">Tidak ada slot tersedia</option>';
        } else {
            res.forEach(s => {
                html += `
                    <option value="${s.id}">
                        Slot #${s.slot_name} (${s.status}) (${s.type})
                    </option>
                `;
            });
        }

        $("select[name='slot_id']").html(html);
    });

});



$("#form-reservasi").submit(function(e){
    e.preventDefault();

    $("#btn-submit").prop("disabled", true).text("Memproses...");

    $.ajax({
        url: "/user/reservasi/store-user",
        method: "POST",
        data: $(this).serialize(),

        success: function(res){
            $("#btn-submit").prop("disabled", false).text("Buat Reservasi");

            if(!res.success){
                alert(res.message);
                return;
            }

            // QR IN
            let qrScanUrl = `https://smartparkingft.com/scan-user/${res.qrcode_token}`;
            let qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrScanUrl)}`;
            $("#qr-image").attr("src", qrImgUrl);

            // Perbaikan expiredAt → ambil dari res.data
            let expiredAt = new Date(res.data.expired_at.replace(" ", "T")).getTime();
            startCountdown(expiredAt);

            // SIMPAN SEMUA TOKEN → IN & OUT
            lastQR = {
                qrcode_token: res.qrcode_token,
                qrcode_out: res.qrcode_out,  
                expired_at: res.data.expired_at
            };

            console.log("TOKEN IN :", lastQR.qrcode_token);
            console.log("TOKEN OUT:", lastQR.qrcode_out);

            $("#qrModal").modal("show");
            loadHistory();
        }


    });
});



   
</script>

@endsection
