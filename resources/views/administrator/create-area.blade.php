@extends('layouts.index')

@section('kontent')
<div class="container">
    <h4>Kelola Area Parkir</h4>
    <div id="map" style="height: 500px;"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<script>
let map = L.map('map').setView([-3.986, 122.513], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let drawnPolygon = null;
let polygonsLayer = L.layerGroup().addTo(map);

// === Fetch semua area parkir dari DB ===
function loadAreas() {
    polygonsLayer.clearLayers();
    $.get('/api/parking-areas', function(data) {
        data.forEach(area => {
            let polygon = L.polygon(area.polygon_coordinates, {
                color: area.type === 'car' ? 'blue' : 'green'
            }).addTo(polygonsLayer);

            polygon.bindPopup(`<b>${area.name}</b><br>${area.type}<br>
                <button class="btn btn-sm btn-danger" onclick="deleteArea(${area.id})">Hapus</button>`);
        });
    });
}

// === Simpan polygon baru ke server ===
function savePolygon(name, type, coords) {
    $.ajax({
        url: '/api/parking-areas',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ name, type, polygon_coordinates: coords }),
        success: function() {
            loadAreas();
            alert('Area berhasil disimpan!');
        }
    });
}

// === Hapus area ===
function deleteArea(id) {
    $.ajax({
        url: `/api/parking-areas/${id}`,
        type: 'DELETE',
        success: function() {
            loadAreas();
        }
    });
}

// === Event tambah polygon ===
map.on('click', function(e) {
    if (drawnPolygon) {
        map.removeLayer(drawnPolygon);
    }
    drawnPolygon = L.polygon([e.latlng], { color: 'red' }).addTo(map);
});

// Double click untuk menyimpan polygon
map.on('dblclick', function() {
    if (!drawnPolygon) return;

    let coords = drawnPolygon.getLatLngs()[0].map(p => [p.lat, p.lng]);
    let name = prompt("Nama Area Parkir:");
    let type = prompt("Jenis (car/motorcycle):");

    if (name && type) {
        savePolygon(name, type, coords);
        map.removeLayer(drawnPolygon);
        drawnPolygon = null;
    }
});

loadAreas();
</script>
@endsection
