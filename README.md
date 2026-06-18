# Phone Tracking System

A web application for keeping track of a fleet of phones / mobile devices —
their details, status (active, lost, stolen, recovered) and **last known
location on a live map**. Built with **Laravel 12**, Blade, Bootstrap 5 and
Leaflet/OpenStreetMap.

> This is an **asset / "find-my-device" style tracker**: devices (or their
> owners) report their own position to the server using a per-device API key.
> Every account only ever sees the phones it has registered.

---

## Features

- 🔐 **Authentication** – register / login / logout (Laravel session auth).
- 📱 **Device registry** – CRUD for phones: label, brand, model, IMEI, number,
  owner and status.
- 🗺️ **Live map** – dashboard shows the latest position of every device;
  each device page shows its full location trail (Leaflet + OpenStreetMap, no
  API key needed).
- 📍 **Location logging** – add positions manually, capture them from the
  browser's geolocation, or have the device check in automatically.
- 🔑 **Device API key** – each phone gets a unique key it uses to POST its
  location to the reporting endpoint.
- 📊 **Dashboard** – status counts and recent check-ins at a glance.

## Tech stack

| Layer    | Choice                                   |
|----------|------------------------------------------|
| Backend  | PHP 8.2, Laravel 12                      |
| Database | SQLite (default) — swap to MySQL easily  |
| Frontend | Blade, Bootstrap 5, vanilla JS           |
| Maps     | Leaflet + OpenStreetMap tiles            |

CSS/JS are loaded from a CDN, so **no Node/npm build step is required**.

---

## Getting started

### Requirements
- PHP 8.2+ with `pdo_sqlite` (or `pdo_mysql`), `mbstring`, `openssl`
- [Composer](https://getcomposer.org/)

### Install

```bash
git clone https://github.com/defhopeee/Phone-Tracking-System.git
cd Phone-Tracking-System

composer install
cp .env.example .env
php artisan key:generate

# Create the database schema and demo data
php artisan migrate --seed

php artisan serve
```

Open <http://127.0.0.1:8000>.

A demo account is seeded:

```
email:    demo@example.com
password: password
```

…or click **Create one** to register your own account.

### Running under XAMPP

This project lives in `htdocs`, so you can also browse it through Apache. Point
your virtual host / browser at the **`public/`** directory (Laravel's web root),
e.g. <http://localhost/Projects/Phone%20Tracking%20System/public>.

---

## Using MySQL instead of SQLite

Create a database and update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phone_tracking
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`.

---

## Device check-in API

Devices report their position to a single endpoint, authenticating with the
per-phone API key shown on each device's page.

```
POST /api/report-location
Header:  X-Api-Key: <the phone's api key>   (or form field api_key)
Body:    latitude, longitude, [accuracy], [battery], [label]
```

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/report-location \
  -H "X-Api-Key: YOUR_DEVICE_KEY" \
  -d "latitude=51.5074" \
  -d "longitude=-0.1278" \
  -d "battery=82"
```

Responses:

| Status | Meaning                          |
|--------|----------------------------------|
| 201    | Location recorded                |
| 401    | No API key supplied              |
| 403    | Unknown / invalid API key        |
| 422    | Validation failed (bad coords)   |

---

## Project layout

```
app/Http/Controllers/      Auth, Dashboard, Phone, Location, Api/LocationReport
app/Models/                User, Phone, LocationLog
database/migrations/       phones, location_logs (+ Laravel defaults)
database/seeders/          demo user, sample phones & trails
resources/views/           Blade templates (layouts, auth, phones, dashboard)
public/css, public/js      app.css, app.js (map + geolocation)
routes/web.php             all routes incl. the device API endpoint
```

## Security notes

- Each user can only view and manage their own phones (ownership is enforced in
  every controller action).
- API keys are unique per device and can be **regenerated** from the device page
  if one is leaked.
- Use this only to track devices **you own or are authorised to track**.

## License

MIT — see below. Use responsibly and only on devices you own or are authorised
to track.
