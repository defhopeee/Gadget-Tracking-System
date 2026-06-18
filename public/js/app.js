/* Gadget Tracking System — front-end behaviour */
(function () {
    'use strict';

    const statusColors = {
        active: '#16a34a',
        inactive: '#6b7280',
        lost: '#d97706',
        stolen: '#dc2626',
        recovered: '#0891b2',
    };

    function coloredIcon(color) {
        return L.divIcon({
            className: 'pts-pin',
            html: '<span style="background:' + color + '"></span>',
            iconSize: [18, 18],
            iconAnchor: [9, 9],
        });
    }

    function initMap(el) {
        let points = [];
        try {
            points = JSON.parse(el.getAttribute('data-points') || '[]');
        } catch (e) {
            points = [];
        }
        const drawPath = el.getAttribute('data-path') === '1';

        const map = L.map(el.id);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const markers = [];
        points.forEach(function (p) {
            const color = statusColors[p.status] || '#2563eb';
            const marker = L.marker([p.lat, p.lng], { icon: coloredIcon(color) }).addTo(map);
            let html = '<strong>' + (p.label || 'Location') + '</strong>';
            if (p.time) html += '<br><small>' + p.time + '</small>';
            if (p.url) html += '<br><a href="' + p.url + '">Open</a>';
            marker.bindPopup(html);
            markers.push(marker);
        });

        if (drawPath && points.length > 1) {
            L.polyline(points.map(function (p) { return [p.lat, p.lng]; }),
                { color: '#2563eb', weight: 3, opacity: 0.6 }).addTo(map);
        }

        if (points.length === 1) {
            map.setView([points[0].lat, points[0].lng], 14);
        } else if (points.length > 1) {
            map.fitBounds(L.featureGroup(markers).getBounds().pad(0.25));
        } else {
            map.setView([20, 0], 2);
        }
    }

    function initGeolocation() {
        const btn = document.getElementById('use-my-location');
        if (!btn) return;
        btn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by this browser.');
                return;
            }
            btn.disabled = true;
            btn.textContent = 'Locating…';
            navigator.geolocation.getCurrentPosition(function (pos) {
                document.getElementById('loc-lat').value = pos.coords.latitude.toFixed(7);
                document.getElementById('loc-lng').value = pos.coords.longitude.toFixed(7);
                if (pos.coords.accuracy) {
                    document.getElementById('loc-acc').value = Math.round(pos.coords.accuracy);
                }
                document.getElementById('loc-source').value = 'browser';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg"></i> Location captured';
            }, function (err) {
                alert('Could not get location: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-crosshair"></i> Use this browser\'s location';
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#map').forEach(initMap);
        initGeolocation();
    });
})();
