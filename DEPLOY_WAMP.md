# Deploying on WAMP (Windows)

This guide deploys the Cafe Javas Feedback Management system on WAMP
(Apache + MySQL/MariaDB + PHP) for Windows.

## Requirements

- WAMP with PHP **8.0 or newer** (8.2/8.3 recommended)
- MySQL **or** MariaDB (either works; both ship with WAMP)
- PHP extensions used by the app (all enabled by default in WAMP):
  `mysqli`, `curl`, `fileinfo`, `json`, `mbstring`

## Important: how the app must be hosted

All views use absolute URLs (`/assets/...`, `/login`, `/feedback/...`), so the
project **must be served from the domain root**. It will NOT work from
`http://localhost/customer-feedback-system/` — the router and asset links
would break.

## Step 1 — Copy the project (no subfolder)

Put the project **directly inside WAMP's web directory** — its contents become
the root of `http://localhost/`. Copy all files/folders (`index.php`,
`.htaccess`, `assets/`, `config/`, `controllers/`, `database/`, etc.) into:

```
C:\wamp64\www
```

Tip: delete WAMP's default `index.php` first if it exists, so the app's
`index.php` is used. With this layout no VirtualHost is needed — WAMP's default
`localhost` vhost already points at `C:\wamp64\www`, and the app is served from
`http://localhost/`.

If you prefer a separate domain (e.g. `http://cfms.test`), use the
VirtualHost option in Step 4 instead.

## Step 2 — Create the database

Open **phpMyAdmin** (`http://localhost/phpmyadmin`), log in as `root`, then:

1. Go to the **Import** tab.
2. Import `database/migration.sql` (creates the `cfms` database and tables).
3. Import `database/seed.sql` (adds roles, branches, staff accounts, and
   sample feedback).

You can also import both from the MySQL CLI:

```
mysql -u root -p < database/migration.sql
mysql -u root -p < database/seed.sql
```

WAMP's MySQL/MariaDB default login is user `root` with an **empty password**.
If you set a password, remember it for Step 3.

## Step 3 — Configure the environment

1. In the project folder, copy `.env.wamp` to `.env`:

```
copy .env.wamp .env
```

2. Open `.env` and confirm the database settings match your WAMP MySQL:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cfms
DB_USERNAME=root
DB_PASSWORD=
```

3. Optional: copy your SMS / Groq keys from the old `.env` into the new one.
   Leave them empty to keep those integrations disabled (`SMS_ENABLED=false`
   keeps the app from calling external APIs).

## Step 4 — VirtualHost (optional, for a custom domain)

If you copied the project straight into `C:\wamp64\www` you can **skip this
step** — `http://localhost/` already works.

For a separate domain, copy the snippet from `wamp/httpd-vhosts.conf.example`
into `C:\wamp64\bin\apache\apacheX.Y.Z\conf\extra\httpd-vhosts.conf`, then add
this line to `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1  cfms.test
```

Alternatively, use the WAMP tray icon menu to add the virtual host graphically.

Afterwards restart all WAMP services (left-click the tray icon -> Restart All).

## Step 5 — Verify

- Open `http://localhost/` (or `http://cfms.test` if you set up Step 4) — you
  should see the Cafe Javas landing page.
- If you see "Database connection failed", double-check Step 3's credentials.
- Uploads are written to `assets/uploads/avatars` (auto-created). If avatar
  uploads fail, make sure the folder is writable.

## Login

| Role  | Email                   | Password   |
|-------|-------------------------|------------|
| Admin | `admin@cafejavas.test`  | `password` |
| Staff | `staff@cafejavas.test`  | `password` |

Additional seeded staff accounts (all with password `password`):

- `baguma.jessy@cj.com`
- `namusisi.victoria@cj.com`
- `tumugonza.gloria@cj.com`
- `hasahya.samalie@cj.com`
- `masengere.owen@cj.com`
- `kajimu.pretty@cj.com`

## Troubleshooting

- **500 error on every page** → PHP fatal during bootstrap; check the Apache
  error log (`C:\wamp64\logs\`). If it mentions a missing database, run
  Step 2 again.
- **Landing page shows but links/404** → the app is not being served from the
  domain root; you probably left it in a subfolder like
  `C:\wamp64\www\customer-feedback-system`. Move the files directly into
  `C:\wamp64\www` (Step 1).
- **mod_rewrite disabled** → `.htaccess` silently fails. In WAMP it is on by
  default; confirm `LoadModule rewrite_module` is not commented in
  `httpd.conf`.
