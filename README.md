# Gadget Tracking System

A web application for keeping track of a fleet of **gadgets** (phones,
tablets, laptops, smartwatches, GPS trackers, cameras and more), including
their details, status (active, lost, stolen, recovered) and **last known
location on a live map**. Built with **Laravel 12**, Blade, Bootstrap 5 and
Leaflet/OpenStreetMap.

> This is an asset / "find my device" style tracker: devices (or their
> owners) report their own position to the server using a per gadget API key.

## Features

- **Opens straight to the dashboard** (no login for now).
- **Gadget registry**: add any gadget with a type (phone, tablet, laptop,
  smartwatch, GPS tracker, camera, other), brand, model, IMEI, number, owner
  and status.
- **Live map**: the dashboard shows the latest position of every gadget; each
  gadget page shows its full location trail (Leaflet + OpenStreetMap, no API
  key needed).
- **Location logging**: add positions manually, capture them from the
  browser's geolocation, or have the device check in automatically.
- **Device API key**: each gadget has a unique key it uses to POST its
  location to the reporting endpoint.
- **Dashboard**: status counts and recent check-ins at a glance.

## Tech stack

| Layer    | Choice                                  |
|----------|-----------------------------------------|
| Backend  | PHP 8.2, Laravel 12                     |
| Database | SQLite (default), MySQL ready           |
| Frontend | Blade, Bootstrap 5, vanilla JS          |
| Maps     | Leaflet + OpenStreetMap tiles           |

CSS/JS load from a CDN, so **no Node/npm build step is required**.

## Getting started

### Requirements

- PHP 8.2+ with `pdo_sqlite` (or `pdo_mysql`), `mbstring`, `openssl`
- [Composer](https://getcomposer.org/)

### Install

```bash
git clone https://github.com/defhopeee/Gadget-Tracking-System.git
cd Gadget-Tracking-System

composer install
cp .env.example .env
php artisan key:generate

# Create the database schema and demo data
php artisan migrate --seed

php artisan serve
```

Open <http://127.0.0.1:8000>. The app loads directly on the dashboard with the
seeded sample gadgets; no login is required.

### Running under XAMPP (no `php artisan serve` needed)

The repo ships a root `.htaccess` and `index.php`, so once the folder is in
`htdocs` and **Apache is running**, the app is served automatically. Just open:

<http://localhost/Projects/Gadget%20Tracking%20System/>

No directory listing, no `/public` in the URL, nothing to start manually.
(Requires Apache's `mod_rewrite`, enabled by default in XAMPP.)

## Time zone

The app is configured for UTC+3 (`config/app.php` uses `Etc/GMT-3`) so the
timeline matches local time. Change that value to your own zone if needed.

## Using MySQL instead of SQLite

Create a database and update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gadget_tracking
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`.

## Device check in API

Devices report their position to a single endpoint, authenticating with the
per gadget API key shown on each gadget's page.

```text
POST /api/reportlocation
Header:  X-Api-Key: <the gadget's api key>   (or form field api_key)
Body:    latitude, longitude, [accuracy], [battery], [label]
```

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/reportlocation \
  -H "X-Api-Key: YOUR_DEVICE_KEY" \
  -d "latitude=51.5074" \
  -d "longitude=-0.1278" \
  -d "battery=82"
```

Responses:

| Status | Meaning                        |
|--------|--------------------------------|
| 201    | Location recorded              |
| 401    | No API key supplied            |
| 403    | Unknown or invalid API key     |
| 422    | Validation failed (bad coords) |

## Project layout

```text
app/Http/Controllers/      Dashboard, Phone, Location, Api/LocationReport
app/Models/                User, Phone, LocationLog
database/migrations/       phones, location_logs (+ Laravel defaults)
database/seeders/          empty by default (no demo data)
resources/views/           Blade templates (layouts, gadgets, dashboard)
public/css, public/js      app.css, app.js (map + geolocation)
routes/web.php             all routes incl. the device API endpoint
```

## Notes

- Ships with **no demo data**; the database starts empty.
- API keys are unique per gadget and can be regenerated from the gadget page if
  one is leaked.
- Use this only to track devices you own or are authorised to track.

## Security status

This is a demo/portfolio project, not a production service. What is in place:

- Output is escaped (Blade auto-escaping, and map popups are escaped in JS) to
  prevent stored XSS.
- All web forms use CSRF tokens; Eloquent uses prepared statements (no SQL
  injection); mass assignment is limited to validated fields.
- The device API endpoint authenticates with a random per-gadget key and is
  rate-limited.
- No secrets are committed: `.env` and the SQLite database are git-ignored.

Before any real-world use you MUST:

- **Add authentication / multi-tenant access control.** As shipped there is no
  login: anyone who can reach the site sees and edits every gadget. This is
  intentional for the demo only.
- Set `APP_DEBUG=false` and a strong `APP_KEY` in production, serve over HTTPS,
  and add a privacy policy plus lawful consent for collecting location data.

## License

MIT. Use responsibly and only on devices you own or are authorised to track.
