@extends('layouts.index')

@section('kontent')

<style>
#map {
    width: 100%;
    height: 500px;
    z-index: 1;
}
</style>

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Maps & Slot Area</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                            <li class="breadcrumb-item text-muted active" aria-current="page">Maps & Slot Area</li>
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

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6">
                    <h4 class="card-title mb-2">Maps</h4>
                </div>
                <div class="col-sm-6 d-flex justify-content-end">
                    <div class="d-flex">
                        <button class="btn btn-success btn-rounded" data-toggle="modal" data-target="#modalAddArea">Tambah Data Area</button>
                        <button class="btn btn-primary btn-rounded ml-2" data-toggle="modal" data-target="#modalAddSlot">Tambah Data Slot</button>
                    </div>
                </div>
            </div>
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- ===================================================
     MODAL TAMBAH AREA
=================================================== -->
<div class="modal fade" id="modalAddArea" tabindex="-1" aria-labelledby="modalAddAreaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalAddAreaLabel">Tambah Data Area Parkir</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="alert alert-warning d-flex align-items-start" role="alert">
             <i class="fas fa-info-circle fa-lg mr-2"></i>
            <div>
                Mohon diperhatikan bahwa area berwarna <b>biru muda pada peta</b> merupakan area parkir yang sudah ada.<br>
                Silakan gunakan tool polygon untuk menandai area parkir baru yang ingin ditambahkan.<br>
                Terima kasih.
            </div>
        </div>

        <form id="formAddArea">
          <div class="mb-3">
            <label for="areaName">Nama Area</label>
            <input type="text" id="areaName" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="areaGate">Gate</label>
            <select id="areaGate" name="gate" class="form-control">
              <option>-- Pilih Gate --</option>
              <option value="utama">Utama</option>
              <option value="sekunder">Sekunder</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="areaType">Jenis Area</label>
            <select id="areaType" name="type" class="form-control" required>
              <option value="">-- Pilih Jenis --</option>
              <option value="car">Mobil</option>
              <option value="motorcycle">Motor</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Gambar Polygon di Peta</label>
            <div id="mapAddArea" style="height: 400px; border: 1px solid #ccc; border-radius: 10px;"></div>
            <input type="hidden" id="polygonCoordinates" name="polygon_coordinates">
            <small class="text-muted">Gunakan tool polygon untuk menandai area parkir.</small>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="btnSaveArea">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- ===================================================
     MODAL TAMBAH SLOT
=================================================== -->
<div class="modal fade" id="modalAddSlot" tabindex="-1" aria-labelledby="modalAddSlotLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalAddSlotLabel">Tambah Data Slot Parkir</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="alert alert-warning d-flex align-items-start" role="alert">
            <i class="fas fa-info-circle fa-lg mr-2 mt-1"></i>
            <div>
                Pastikan Anda memilih area parkir yang benar.<br>
                Slot akan ditempatkan <b>di dalam area yang sudah ada di peta</b>.<br>
                Klik pada peta yang berwarna biru untuk menandai posisi slot parkir baru.
                <p class="text-danger"><b> Untuk daerah yang berwarna kuning sudah memiliki slot parkir</b></p>
            </div>
        </div>

        <form id="formAddSlot">
          <div class="mb-3">
            <label for="slotName">Nama Slot</label>
            <input type="text" id="slotName" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="slotArea">Pilih Area</label>
            <select id="slotArea" name="area_id" class="form-control" required>
              <option value="">-- Pilih Area --</option>
              @foreach($areas ?? [] as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label>Pilih Lokasi Slot di Peta</label>
            <div id="mapAddSlot" style="height: 400px; border: 1px solid #ccc; border-radius: 10px;"></div>
            <input type="hidden" id="slotLat" name="latitude">
            <input type="hidden" id="slotLng" name="longitude">
            <small class="text-muted">Klik di peta untuk menentukan posisi slot parkir.</small>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSaveSlot">Simpan</button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {

    // =====================================================
    // PETA UTAMA
    // =====================================================
    var map = L.map('map', {
        center: [-3.983076988724448, 122.51968872932548],
        zoom: 19
    });

    var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains: ['mt0','mt1','mt2','mt3'],
        attribution: '© Google',
        maxZoom: 22
    }).addTo(map);

    setTimeout(() => {
        map.invalidateSize();
    }, 500);

    var drawnAreas = new L.FeatureGroup().addTo(map);
    var drawnSlots = new L.FeatureGroup().addTo(map);

    // =====================================================
    // LOAD AREA
    // =====================================================
    function loadParkingAreas() {
        $.get("{{ route('parking-areas.index') }}", function(data) {
            drawnAreas.clearLayers();

            data.forEach(function(area) {
                if (area.polygon_coordinates) {
                    try {
                        let coords = JSON.parse(area.polygon_coordinates);

                        if (Array.isArray(coords)) {
                            L.polygon(coords, {
                                color: area.type === 'car' ? 'green' : 'orange',
                                fillOpacity: 0.4
                            })
                            .bindPopup(`
                                <b>${area.name}</b><br>
                                Jenis: ${area.type}<br>
                                <button class="btn btn-sm btn-danger mt-2 btn-delete-area" data-id="${area.id}">Hapus</button>
                            `)
                            .addTo(drawnAreas);
                        }

                    } catch (e) {
                        console.error('Invalid polygon data for', area.name);
                    }
                }
            });
        });
    }

    // =====================================================
    // LOAD SLOT
    // =====================================================
    function loadParkingSlots() {
        $.get("{{ route('parking-slots.index') }}", function(data) {
            drawnSlots.clearLayers();

            data.forEach(function(slot) {
                if (slot.latitude && slot.longitude) {
                    L.circleMarker([slot.latitude, slot.longitude], {
                        radius: 2,
                        color: '#007bff',
                        fillColor: '#007bff',
                        fillOpacity: 0.8
                    })
                    .bindPopup(`
                        <b>${slot.slot_name}</b><br>
                        Area: ${slot.area?.name || 'Tidak diketahui'}<br>
                        <badge class="badge badge-success">${slot.status}</badge><br>
                        <button class="btn btn-sm btn-danger mt-2 btn-delete-slot" data-id="${slot.id}">Hapus</button>
                    `)
                    .addTo(drawnSlots);
                }
            });
        });
    }

    loadParkingAreas();
    loadParkingSlots();

    setInterval(() => {
        loadParkingAreas();
        loadParkingSlots();
    }, 5000);



    function loadExistingAreasToModalMap(map, layerGroup) {
        $.ajax({
            url: "/parking-areas/list", // pastikan endpoint ini mengembalikan semua area
            type: "GET",
            success: function (areas) {
                layerGroup.clearLayers();

                areas.forEach(a => {
                    if (!a.polygon_coordinates) return;

                    let coords = JSON.parse(a.polygon_coordinates);

                    let polygon = L.polygon(coords, {
                        color: "#007bff",
                        fillColor: "#007bff",
                        fillOpacity: 0.25,
                        weight: 2
                    });

                    polygon.bindPopup(`<b>${a.name}</b><br>Gate: ${a.gate}<br>Type: ${a.type}`);

                    polygon.addTo(layerGroup);
                });
            },
            error: function (xhr) {
                console.error("Gagal load area ke modal:", xhr.responseText);
            }
        });
    }


   

    // =====================================================
    // MODAL AREA
    // =====================================================
    var mapAddArea;
    var drawnItemsModal = new L.FeatureGroup();
    var existingAreaLayer = new L.FeatureGroup(); // <-- layer untuk area lama

    $('#modalAddArea').on('shown.bs.modal', function () {

        if (!mapAddArea) {
            mapAddArea = L.map('mapAddArea', {
                center: [-3.983076988724448, 122.51968872932548],
                zoom: 18
            });

            L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                subdomains: ['mt0','mt1','mt2','mt3'],
                maxZoom: 22
            }).addTo(mapAddArea);

            // Tambah layer existing area (tidak boleh diedit)
            existingAreaLayer.addTo(mapAddArea);

            // Tambah layer untuk polygon baru user
            drawnItemsModal.addTo(mapAddArea);

            var drawControl = new L.Control.Draw({
                edit: { featureGroup: drawnItemsModal },
                draw: {
                    polygon: true,
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false
                }
            });

            mapAddArea.addControl(drawControl);

            // Event user menggambar polygon baru
            mapAddArea.on(L.Draw.Event.CREATED, function (event) {
                drawnItemsModal.clearLayers();
                var layer = event.layer;
                drawnItemsModal.addLayer(layer);

                var coordinates = layer.getLatLngs()[0].map(p => [p.lat, p.lng]);
                $('#polygonCoordinates').val(JSON.stringify(coordinates));
            });
        }

        // === Load area lama setiap kali modal dibuka ===
        loadExistingAreasToModalMap(mapAddArea, existingAreaLayer);

        setTimeout(() => { mapAddArea.invalidateSize(); }, 300);
    });


    // SAVE AREA ============================================
    $('#btnSaveArea').click(function () {

        var formData = {
            name: $('#areaName').val(),
            gate: $('#areaGate').val(),
            type: $('#areaType').val(),
            polygon_coordinates: JSON.parse($('#polygonCoordinates').val() || '[]'),
            _token: '{{ csrf_token() }}'
        };

        if (!formData.name || !formData.gate || !formData.type || formData.polygon_coordinates.length === 0) {
            SwalGlobal.warning("Mohon lengkapi semua field & gambar polygon.");
            return;
        }

        $.ajax({
            url: "{{ route('parking-areas.store') }}",
            type: "POST",
            data: formData,
            success: function () {
                $('#modalAddArea').modal('hide');
                SwalGlobal.success("Area berhasil disimpan!");
                loadParkingAreas();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                SwalGlobal.error("Gagal menyimpan area.");
            }
        });
    });

    
    // var mapAddSlot, slotMarkers = [];
    // var existingAreaLayersSlot = []; // Simpan polygon area

    // $('#modalAddSlot').on('shown.bs.modal', function () {

    //     if (!mapAddSlot) {

    //         mapAddSlot = L.map('mapAddSlot', {
    //             center: [-3.983076988724448, 122.51968872932548],
    //             zoom: 18
    //         });

    //         L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    //             subdomains: ['mt0','mt1','mt2','mt3'],
    //             maxZoom: 22
    //         }).addTo(mapAddSlot);

    //         // Klik untuk menambah marker slot
    //         mapAddSlot.on('click', function(e) {
    //             let marker = L.marker(e.latlng).addTo(mapAddSlot);
    //             slotMarkers.push(e.latlng);
    //         });
    //     }

    //     // === LOAD POLYGON AREA KE DALAM MODAL ===
    //     $.ajax({
    //         url: "/parking-areas/list", // endpoint untuk ambil data area
    //         type: "GET",
    //         success: function(res) {

    //             // Hapus layer lama agar tidak double jika buka modal 2x
    //             existingAreaLayersSlot.forEach(layer => mapAddSlot.removeLayer(layer));
    //             existingAreaLayersSlot = [];

    //             res.forEach(area => {
    //                 if (area.polygon_coordinates) {

    //                     let polygon = L.polygon(JSON.parse(area.polygon_coordinates), {
    //                         color: "yellow",
    //                         fillColor: "orange",
    //                         weight: 2,
    //                         fillOpacity: 0.2
    //                     }).addTo(mapAddSlot);

    //                     existingAreaLayersSlot.push(polygon);
    //                 }
    //             });
    //         }
    //     });

    //     // Refresh map ukuran
    //     setTimeout(() => { mapAddSlot.invalidateSize(); }, 300);
    // });


    // // =====================================================
    // // SAVE MULTIPLE SLOT
    // // =====================================================
    // $('#btnSaveSlot').click(function () {

    //     var formData = {
    //         area_id: $('#slotArea').val(),
    //         name_prefix: $('#slotName').val(),
    //         slots: slotMarkers.map((p, i) => ({
    //             latitude: p.lat,
    //             longitude: p.lng,
    //             slot_name: `${$('#slotName').val()}-${i+1}`
    //         })),
    //         _token: '{{ csrf_token() }}'
    //     };

    //     if (!formData.area_id || formData.slots.length === 0) {
    //         SwalGlobal.warning("Pilih area & tambahkan minimal 1 titik slot.");
    //         return;
    //     }

    //     $.ajax({
    //         url: "{{ route('parking-slots.store-multiple') }}",
    //         type: "POST",
    //         data: JSON.stringify(formData),
    //         contentType: "application/json",
    //         success: function () {
    //             $('#modalAddSlot').modal('hide');
    //             SwalGlobal.success("Semua slot berhasil disimpan!");
    //             slotMarkers = [];
    //             loadParkingSlots();
    //         },
    //         error: function (xhr) {
    //             console.error(xhr.responseText);
    //             SwalGlobal.error("Gagal menyimpan slot.");
    //         }
    //     });
    // });


    var mapAddSlot, slotMarkers = [];
    var existingAreaLayersSlot = []; 

    $('#modalAddSlot').on('shown.bs.modal', function () {

        if (!mapAddSlot) {

            mapAddSlot = L.map('mapAddSlot', {
                center: [-3.983076988724448, 122.51968872932548],
                zoom: 18
            });

            L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                subdomains: ['mt0','mt1','mt2','mt3'],
                maxZoom: 22
            }).addTo(mapAddSlot);

            // === ADD MARKER BARU BERWARNA BIRU ===
            mapAddSlot.on('click', function(e) {

                let blueIcon = L.divIcon({
                    html: '<i class="fas fa-map-marker-alt" style="color: blue; font-size: 32px;"></i>',
                    iconAnchor: [16, 32],
                    className: ''
                });

                L.marker(e.latlng, { icon: blueIcon }).addTo(mapAddSlot);

                slotMarkers.push(e.latlng);
            });
        }

        // === LOAD AREA POLYGON DARI BACKEND ===
        $.ajax({
            url: "/api/parking-areas",
            type: "GET",
            success: function(res) {

                // Bersihkan layer lama
                existingAreaLayersSlot.forEach(layer => mapAddSlot.removeLayer(layer));
                existingAreaLayersSlot = [];

                res.forEach(area => {
                    if (area.polygon_coordinates) {

                        let hasSlots = area.total_slots > 0;

                        let polygon = L.polygon(area.polygon_coordinates, {
                            color: hasSlots ? "yellow" : "blue",
                            fillColor: hasSlots ? "orange" : "lightblue",
                            weight: 2,
                            fillOpacity: 0.25
                        }).addTo(mapAddSlot);

                        existingAreaLayersSlot.push(polygon);
                    }
                });

            }
        });

        setTimeout(() => { mapAddSlot.invalidateSize(); }, 300);
    });


    // =====================================================
    // SAVE MULTIPLE SLOT
    // =====================================================
    $('#btnSaveSlot').click(function () {

        var formData = {
            area_id: $('#slotArea').val(),
            name_prefix: $('#slotName').val(),
            slots: slotMarkers.map((p, i) => ({
                latitude: p.lat,
                longitude: p.lng,
                slot_name: `${$('#slotName').val()}-${i+1}`
            })),
            _token: '{{ csrf_token() }}'
        };

        if (!formData.area_id || formData.slots.length === 0) {
            SwalGlobal.warning("Pilih area & tambahkan minimal 1 titik slot.");
            return;
        }

        $.ajax({
            url: "{{ route('parking-slots.store-multiple') }}",
            type: "POST",
            data: JSON.stringify(formData),
            contentType: "application/json",
            success: function () {
                $('#modalAddSlot').modal('hide');
                SwalGlobal.success("Semua slot berhasil disimpan!");
                slotMarkers = [];
                loadParkingSlots();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                SwalGlobal.error("Gagal menyimpan slot.");
            }
        });
    });

});
</script>


@endsection
