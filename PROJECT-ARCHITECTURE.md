# Cafe Javas — Customer Feedback Management Dashboard

## Project Overview

A **PHP 8.4** (no framework) web app with two sides:

- **Public website** — landing page, menus, about, feedback submission & ticket tracking
- **Admin dashboard** — manage feedback, users, contacts, analytics, reports, settings

---

## Request Lifecycle

```
Browser → .htaccess/router.php → index.php (front controller)
  → bootstrap.php (env, config, DB, autoload, session)
  → Router::dispatch() matches URL → Controller method → Model → View
```

### Key bootstrap files

| File | Purpose |
|------|---------|
| `index.php` | Front controller — all 65 routes defined here |
| `includes/bootstrap.php` | Loads `.env`, config, mysqli database, autoloader, session |
| `includes/Router.php` | Custom URL matcher with `{id}` placeholders (regex `[0-9]+`) |
| `router.php` | PHP built-in server router for `php -S` (dev mode) |

---

## Architecture: MVC-like

| Layer | Directory | Example |
|-------|-----------|---------|
| **Controller** | `controllers/` (13 files) | `FeedbackController::assign()` |
| **Model** | `models/` (8 files) | `Feedback::summary()` |
| **View** | `views/` (29 files) | `views/dashboard/index.php` |
| **Layout** | `views/layouts/` (3) | `app.php` (dashboard), `public.php` (website), `auth.php` (login) |
| **Partial** | `views/partials/` (3) | `sidebar.php`, `topbar.php`, `toasts.php` |

### View rendering

Views are rendered via:

```php
View::render('dashboard/index', ['title' => 'Dashboard', 'summary' => $data], 'app');
```

This extracts `$data` into scope, captures the output buffer, then wraps it with the chosen layout (e.g. `views/layouts/app.php` inserts it into `<main><?= $content ?></main>`).

---

## Database

**16 tables** in MariaDB (`customer_feedback_system`) on port **3307**.

### Core tables

| Table | Purpose |
|-------|---------|
| `feedback` | Main feedback records (673 seeded) |
| `users` | Admin & staff accounts |
| `roles` | Admin, Staff, Manager, Customer |
| `permissions` | 7 permission flags |
| `role_permissions` | Many-to-many join |
| `contacts` | Customer contact list |
| `responses` | Staff replies to feedback |
| `audit_logs` | Every action logged with IP & user agent |
| `settings` | Key-value configuration |
| `counters` | Ticket number sequence |
| `sessions` | PHP session storage |
| `password_resets` | Password reset tokens |
| `notifications` | Admin notifications |
| `attachments` | File uploads |
| `feedback_categories` | Lookup table |
| `feedback_status` | Lookup table |

### Seed credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@cafejavas.test` | `password` |
| Staff | `staff@cafejavas.test` | `password` |

The seed data (673 feedback records spanning Jan–Jul 2026, Ugandan context) is in `database/seed.sql`.

---

## Authentication

Two auth modes:

1. **Session-based** (browser) — `Auth::require()` redirects to `/login` if not authenticated
2. **Bearer token** (API) — `Auth::apiUser()` resolves from `remember_token` column

Roles: **admin** (full access) vs **staff** (limited to manage/respond/view reports).

### Auth middleware

```php
Auth::require();                // redirect if not logged in
Auth::requireRole('admin');     // 403 if wrong role
Auth::requireRole('admin', 'staff');
```

---

## Key Features

- **Feedback lifecycle**: Submit → Pending → In Progress → Resolved / Escalated
- **CSRF protection** on all web forms (token in session)
- **Auto SMS reply** via Groq AI — generates contextual response + sends via Comms/EgoSMS
- **Audit trail** — every login, create, update, delete, assign, respond logged
- **Analytics** — 12 chart types (monthly trend, categories, ratings, hours, weekdays, cumulative, response time)
- **Pagination** with filter/sort on feedback list
- **Reports** with CSV & PDF export
- **REST API** — full CRUD for feedback, contacts, auth

---

## Routes

### Public web routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/` | Landing page |
| GET | `/our-menus` | Menu listing |
| GET | `/about` | About page |
| GET | `/feedback/submit` | Public feedback form |
| POST | `/feedback/submit` | Submit feedback |
| GET | `/feedback/track` | Track ticket status |

### Auth routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/login` | Login form |
| POST | `/login` | Login action |
| GET | `/register` | Registration form |
| POST | `/register` | Register action |
| POST | `/logout` | Logout |
| GET | `/profile` | View/edit profile |
| POST | `/profile` | Update profile |

### Admin web routes (auth required)

| Method | Path | Role |
|--------|------|------|
| GET | `/dashboard` | Any |
| GET | `/inbox` | Any |
| GET | `/feedback` | Any |
| GET | `/feedback/{id}` | Any |
| POST | `/feedback` | Any |
| POST | `/feedback/{id}/respond` | Admin/Staff |
| POST | `/feedback/{id}/status` | Admin/Staff |
| POST | `/feedback/{id}/assign` | Admin |
| POST | `/feedback/{id}/delete` | Admin |
| GET | `/customers` | Any |
| GET | `/analytics` | Any |
| GET | `/activity` | Any |
| GET | `/reports` | Any |
| GET | `/users` | Admin |
| POST | `/users` | Admin |
| POST | `/users/{id}` | Admin |
| POST | `/users/{id}/delete` | Admin |
| GET | `/contacts` | Admin |
| POST | `/contacts` | Admin |
| POST | `/contacts/{id}/delete` | Admin |
| GET | `/settings` | Admin |
| POST | `/settings` | Admin |
| GET | `/system/audit` | Admin |
| GET | `/system/integrations` | Admin |
| GET | `/system/maintenance` | Admin |
| GET | `/system/templates` | Admin |

### API routes

| Method | Path | Auth |
|--------|------|------|
| POST | `/api/auth/login` | None |
| POST | `/api/auth/register` | None (first) / Admin |
| GET | `/api/auth/me` | Bearer |
| GET | `/api/auth/users` | Admin |
| PATCH | `/api/auth/profile` | Admin/Staff |
| GET | `/api/feedback/summary` | Admin |
| GET | `/api/feedback` | Public |
| GET | `/api/feedback/{id}` | Public |
| POST | `/api/feedback` | Public |
| POST | `/api/feedback/{id}/assign` | Admin |
| POST | `/api/feedback/{id}/respond` | Admin/Staff |
| POST | `/api/feedback/{id}/resolve` | Admin/Staff |
| POST | `/api/feedback/{id}/escalate` | Admin/Staff |
| PATCH | `/api/feedback/{id}` | Admin/Staff |
| DELETE | `/api/feedback/{id}` | Admin |
| GET | `/api/contacts` | Admin |
| POST | `/api/contacts` | Admin |
| DELETE | `/api/contacts/{id}` | Admin |
| GET | `/api/notifications/admin` | Admin |

---

## Controllers (13)

| Controller | Key methods |
|------------|-------------|
| `AuthController` | login/logout/register, profile CRUD, API auth (token-based) |
| `DashboardController` | index — renders admin or staff dashboard with summary stats |
| `FeedbackController` | Full CRUD + public form + track + assign/respond/status + API (all CRUD + summary) |
| `InboxController` | index — lists pending feedback |
| `CustomerController` | index — aggregated customer list from feedback table |
| `AnalyticsController` | index — full analytics with 12 chart datasets (admin & staff) |
| `ActivityController` | index — audit log timeline |
| `UserController` | index/store/update/delete — user management |
| `ContactController` | index/store/delete + API list/create/delete |
| `ReportController` | index (filtered list), export CSV, export PDF (print) |
| `SettingController` | index/save — system settings |
| `SystemController` | audit, integrations, maintenance, templates CRUD |
| `NotificationController` | apiAdmin — pending complaints & escalations counts |

---

## Models (8)

All models extend `BaseModel` which provides a `mysqli` connection and wrapper methods.

### BaseModel wrapper methods

| Method | Description |
|--------|-------------|
| `query($sql, $params)` | Executes a prepared statement, returns mysqli_result or true |
| `fetch($sql, $params)` | Returns single row as associative array |
| `fetchAll($sql, $params)` | Returns all rows as array of assoc arrays |
| `fetchColumn($sql, $params)` | Returns single scalar value |
| `insert($sql, $params)` | Executes INSERT, returns inserted ID |
| `execute($sql, $params)` | Executes UPDATE/DELETE, returns affected rows |

### Models

| Model | Key methods |
|-------|-------------|
| `Feedback` | `paginate()`, `find()`, `create()`, `update()`, `delete()`, `summary()` (30+ metrics), `staffSummary()`, `staffMonthly()`, `customers()` |
| `User` | `findByEmail()`, `findById()`, `create()`, `update()`, `delete()`, `serialize()` |
| `Contact` | `list()`, `find()`, `create()`, `deactivate()` |
| `Counter` | `next()` — atomic ticket number generation |
| `Setting` | `all()`, `save()` |
| `AuditLog` | `record()`, `latest()`, `forUser()` |
| `ResponseTemplate` | `all()`, `categories()`, `create()`, `update()`, `delete()` |

---

## Helpers (6)

All are static utility classes.

| Helper | Key methods |
|--------|-------------|
| `Security` | `e()` (HTML escape), `csrfToken()`, `verifyCsrf()`, `enforceSessionTimeout()` |
| `View` | `render(view, data, layout)` |
| `Flash` | `success()`, `error()`, `all()` |
| `Response` | `json()`, `redirect()` |
| `GroqAi` | `enabled()`, `generateSmsReply()` — calls Groq API with fallback models |
| `Sms` | `enabled()`, `send()`, `normalizePhone()` — Comms/EgoSMS gateway |

---

## Middleware (1)

`Auth` — used as static utility within controllers (not a middleware pipeline):

- `user()`, `id()`, `check()`, `require()`, `requireRole()`
- `login()`, `logout()`, `attemptRememberLogin()` (remember-me cookie)
- `apiUser()` (resolves Bearer token)

---

## View Structure

### Layouts (3)

| Layout | Used for | Key content |
|--------|----------|-------------|
| `layouts/app.php` | Dashboard pages | Bootstrap 5 + app.css, sidebar, topbar, content, Chart.js, toasts |
| `layouts/public.php` | Public website | Google Fonts (Rubik, Pacifico), preloader, nav bar, footer, back-to-top |
| `layouts/auth.php` | Login/Register | Split-screen: brand panel (45%) + form panel (55%) |

### Partials (3)

| Partial | Content |
|---------|---------|
| `partials/sidebar.php` | Brand, nav sections (Main/Insights/Management/System), user profile, logout |
| `partials/topbar.php` | Page title, public form button, user avatar, logout |
| `partials/toasts.php` | Bootstrap toast container for flash messages |

### Page views (29)

| Directory | Files |
|-----------|-------|
| `views/` | `landing.php`, `our-menus.php`, `about.php` |
| `views/auth/` | `login.php`, `register.php`, `profile.php` |
| `views/dashboard/` | `index.php`, `staff.php` |
| `views/feedback/` | `public_form.php`, `track.php`, `index.php`, `show.php`, `table.php` |
| `views/inbox/` | `index.php` |
| `views/customers/` | `index.php` |
| `views/analytics/` | `index.php`, `staff.php` |
| `views/activity/` | `index.php` |
| `views/users/` | `index.php` |
| `views/contacts/` | `index.php` |
| `views/reports/` | `index.php` |
| `views/settings/` | `index.php` |
| `views/system/` | `audit.php`, `integrations.php`, `maintenance.php`, `templates.php` |
| `views/errors/` | `403.php`, `404.php`, `setup.php` |

---

## Key Code Patterns

### Controller dispatch

```php
// index.php
$router->get("/feedback/{id}", [FeedbackController::class, "show"]);
```

### Model query (mysqli prepared statements)

```php
$count = $this->fetchColumn('SELECT COUNT(*) FROM feedback WHERE status = ?', ['pending']);
$rows  = $this->fetchAll('SELECT * FROM feedback WHERE status = ? ORDER BY created_at DESC', [$status]);
$row   = $this->fetch('SELECT * FROM users WHERE id = ?', [$id]);
$newId = $this->insert('INSERT INTO feedback (subject) VALUES (?)', [$subject]);
$affected = $this->execute('UPDATE feedback SET status = ? WHERE id = ?', ['resolved', $id]);
```

### View rendering

```php
// Controller
View::render('dashboard/index', [
    'title' => 'Dashboard',
    'summary' => $feedback->summary(),
], 'app');
```

### Auth check

```php
Auth::require();                    // redirect to /login if not logged in
Auth::requireRole('admin');         // 403 if wrong role
```

### CSRF protection

```php
// In form:
<input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">

// In controller:
Security::verifyCsrf();
```

### API response

```php
Response::json(['data' => $items, 'total' => $total], 200);
```

---

## CSS Architecture

### Two root variable blocks

| Block | Scope | Purpose |
|-------|-------|---------|
| `:root` (line 1) | Dashboard / System UI | `--brand`, `--side`, `--ink`, `--line`, `--muted` for sidebar, topbar, panels, stats |
| `:root` (Cafe Javas Theme) | Public website / Auth | `--gold`, `--gold-dark`, `--bg-dark`, `--bg-cream`, `--text-dark`, `--font-heading`, `--font-body` |

### Color palette (Cafe Javas)

| Color | Hex | Usage |
|-------|-----|-------|
| Sage green | `#a6b49a` | Primary accent (buttons, links, highlights) |
| Dark green | `#547043` | `--gold-dark` — hover states, gradients |
| Light sage | `#c9cfc2` | Light accent |
| Navy | `#152f4e` | Dark backgrounds, headings |
| Dark gray | `#383838` | Body text |
| Medium gray | `#989898` | Muted text |
| Warm cream | `#faf8f6` | Form panel background |

### Auth login page

Split-screen layout:
- **Left (45%)** — Navy gradient with subtle sage decorative circles, branded header, food image with glass badges, stats
- **Right (55%)** — Cream background with 4px sage gradient top bar, centered form card
- Form: uppercase labels, segmented role toggle, 2px inputs with sage focus ring, gradient submit button with shadow/lift effect

### Sidebar

Fixed 264px left column with `flex-direction:column`:
- `.sidebar-top` (`flex:1;min-height:0;overflow-y:auto`) — brand, nav sections, divider
- `.sidebar-profile` (`flex-shrink:0`) — user info pinned to bottom

---

## Configuration

| File | Purpose |
|------|---------|
| `.env` | Environment variables (DB creds, SMS keys, Groq API key, DB_PORT=3307) |
| `config/config.php` | Returns config array with `app`, `db`, `sms`, `ai` sections |
| `config/database.php` | `mysqli_connect()` via `db()` helper — plain mysqli, no PDO |

---

## Running the App

```bash
# Development server (port 8080)
php -S localhost:8080 -t /path/to/project router.php
```

### Database setup

```bash
mysql -h 127.0.0.1 -P 3307 -u root < database/migration.sql
mysql -h 127.0.0.1 -P 3307 -u root < database/seed.sql
```

---

## File Inventory

```
.htaccess                          Apache rewrite rules
router.php                         PHP built-in server router
index.php                          Front controller + all route definitions

includes/
  bootstrap.php                    App bootstrap (env, config, DB, autoload, session)
  Router.php                       Custom URL router

config/
  config.php                       App configuration array
  database.php                     mysqli connection via db() helper

controllers/ (13)                  Application controllers
models/ (8)                        Data models (extend BaseModel with mysqli wrappers)
helpers/ (6)                       Static utility classes
middleware/ (1)                    Auth middleware

views/
  layouts/ (3)                     Page wrappers (app, public, auth)
  partials/ (3)                    Reusable components (sidebar, topbar, toasts)
  (29 view files)                  Page templates

database/
  migration.sql                    Schema (16 tables)
  seed.sql                         673 records + users + roles + permissions

assets/
  css/app.css                      All styles (2244+ lines)
  js/app.js                        JavaScript
  uploads/restaurant/              Images (logo, burgers, salad, etc.)
  uploads/avatars/                 User avatar uploads
```
