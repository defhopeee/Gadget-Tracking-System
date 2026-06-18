/* Gadget Tracking System - front-end behaviour */
(function () {
    'use strict';

    var statusColors = {
        active: '#16a34a', inactive: '#6b7280', lost: '#d97706',
        stolen: '#dc2626', recovered: '#0891b2'
    };

    function coloredIcon(color) {
        return L.divIcon({
            className: 'pts-pin',
            html: '<span style="background:' + color + '"></span>',
            iconSize: [18, 18], iconAnchor: [9, 9]
        });
    }

    function initMap(el) {
        var points = [];
        try { points = JSON.parse(el.getAttribute('data-points') || '[]'); } catch (e) { points = []; }
        var drawPath = el.getAttribute('data-path') === '1';

        var map = L.map(el.id);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var markers = [];
        points.forEach(function (p) {
            var color = statusColors[p.status] || '#2563eb';
            var marker = L.marker([p.lat, p.lng], { icon: coloredIcon(color) }).addTo(map);
            var html = '<strong>' + (p.label || 'Location') + '</strong>';
            if (p.time) html += '<br><small>' + p.time + '</small>';
            if (p.url) html += '<br><a href="' + p.url + '">Open</a>';
            marker.bindPopup(html);
            markers.push(marker);
        });

        if (drawPath && points.length > 1) {
            L.polyline(points.map(function (p) { return [p.lat, p.lng]; }),
                { color: '#2563eb', weight: 3, opacity: 0.6 }).addTo(map);
        }

        if (points.length === 1) { map.setView([points[0].lat, points[0].lng], 14); }
        else if (points.length > 1) { map.fitBounds(L.featureGroup(markers).getBounds().pad(0.25)); }
        else { map.setView([20, 0], 2); }

        window.ptsMap = map;
    }

    // Fill the manual "Add location" form from this browser's geolocation.
    function fillFromBrowser(btn) {
        if (!navigator.geolocation) { alert('Geolocation is not supported by this browser.'); return; }
        var original = btn.innerHTML;
        btn.disabled = true; btn.textContent = 'Locating...';
        navigator.geolocation.getCurrentPosition(function (pos) {
            var lat = document.getElementById('loc-lat'), lng = document.getElementById('loc-lng');
            if (lat) lat.value = pos.coords.latitude.toFixed(7);
            if (lng) lng.value = pos.coords.longitude.toFixed(7);
            var src = document.getElementById('loc-source'); if (src) src.value = 'browser';
            btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Captured';
        }, function (err) {
            alert('Could not get location: ' + err.message);
            btn.disabled = false; btn.innerHTML = original;
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    // Live tracking: keep posting this device's position while the page is open.
    var watchId = null;
    var liveMarker = null;

    function postLocation(url, token, c) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json',
                'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                latitude: c.latitude, longitude: c.longitude,
                accuracy: c.accuracy ? Math.round(c.accuracy) : null, source: 'browser'
            })
        });
    }

    function startLive(btn) {
        if (!navigator.geolocation) { alert('Geolocation is not supported.'); return; }
        var url = btn.getAttribute('data-url');
        var token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        var status = document.getElementById('live-status');
        btn.classList.remove('btn-outline-success'); btn.classList.add('btn-danger');
        btn.innerHTML = '<i class="bi bi-stop-circle"></i> Stop live tracking';
        if (status) status.textContent = 'Starting...';
        watchId = navigator.geolocation.watchPosition(function (pos) {
            var c = pos.coords;
            postLocation(url, token, c).then(function (r) { return r.json(); }).then(function () {
                if (status) status.textContent = 'Updated ' + new Date().toLocaleTimeString();
                if (window.ptsMap) {
                    var ll = [c.latitude, c.longitude];
                    if (!liveMarker) { liveMarker = L.marker(ll, { icon: coloredIcon('#2563eb') }).addTo(window.ptsMap); }
                    else { liveMarker.setLatLng(ll); }
                    window.ptsMap.setView(ll, Math.max(window.ptsMap.getZoom(), 15));
                }
            }).catch(function () { if (status) status.textContent = 'Send failed (offline?)'; });
        }, function (err) {
            if (status) status.textContent = 'Location error: ' + err.message;
        }, { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
    }

    function stopLive(btn) {
        if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        btn.classList.add('btn-outline-success'); btn.classList.remove('btn-danger');
        btn.innerHTML = '<i class="bi bi-broadcast"></i> Start live tracking';
        var status = document.getElementById('live-status'); if (status) status.textContent = 'Live tracking off.';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#map').forEach(initMap);

        var fillBtn = document.getElementById('use-my-location');
        if (fillBtn) fillBtn.addEventListener('click', function () { fillFromBrowser(fillBtn); });

        var live = document.getElementById('live-toggle');
        if (live) live.addEventListener('click', function () {
            if (watchId === null) startLive(live); else stopLive(live);
        });

        // Shared "delete location" confirmation modal: set the form action.
        document.querySelectorAll('.js-del-loc').forEach(function (b) {
            b.addEventListener('click', function () {
                var form = document.getElementById('delLocForm');
                if (form) form.action = b.getAttribute('data-action');
            });
        });

        // Auto-dismissing toast.
        var t = document.getElementById('flashToast');
        if (t && window.bootstrap) { new bootstrap.Toast(t).show(); }
    });
})();
