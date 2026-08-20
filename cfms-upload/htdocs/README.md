# Customer Feedback Management System

PHP 8.2 + MySQL implementation of the original Express.js/MongoDB backend for Cafe Javas Kampala Road, with a Bootstrap 5 frontend.

## Original Express.js Behavior Preserved

- `POST /api/auth/login`, `POST /api/auth/register`, `GET /api/auth/me`, `GET /api/auth/users`, `PATCH /api/auth/profile`
- `GET /api/feedback/summary`, feedback listing, creation, detail, assign, respond, resolve, escalate, update, delete
- `GET/POST/DELETE /api/contacts`
- `GET /api/notifications/admin`
- Roles from the original backend: `admin`, `staff`
- Feedback enums: categories, `compliment/suggestion/complaint`, `pending/in-progress/resolved/escalated`, `low/medium/high`

## Install

1. Create the database:

```bash
mysql -u root -p < database/migration.sql
mysql -u root -p < database/seed.sql
```

2. Configure environment variables if your database is not local:

```bash
export DB_HOST=127.0.0.1
export DB_DATABASE=customer_feedback_system
export DB_USERNAME=root
export DB_PASSWORD=
```

3. Run locally:

```bash
php -S 127.0.0.1:8080 -t customer-feedback-system
```

4. Login with seeded accounts:

- Admin: `admin@cafejavas.test`
- Staff: `staff@cafejavas.test`
- Password: `password`

Staff accounts for the top customers (password for all: `password`):

- `baguma.jessy@cj.com` — Baguma Jessy Smith
- `namusisi.victoria@cj.com` — Namusisi Victoria Anderson
- `tumugonza.gloria@cj.com` — Tumugonza Gloria
- `hasahya.samalie@cj.com` — Hasahya Samalie Suzan
- `masengere.owen@cj.com` — Masengere Owen
- `kajimu.pretty@cj.com` — Kajimu Pretty

## API Notes

The JSON API mirrors the Express route names. Browser forms use PHP sessions and CSRF tokens. API login returns a bearer token backed by the remember-token column.

## Project Structure

- `controllers/`: request handling and workflow actions
- `models/`: PDO data access
- `views/`: Bootstrap pages and layouts
- `middleware/`: session and role authorization
- `database/`: migration, seed, ER diagram
- `assets/`: CSS, JavaScript, uploads

## Security

The app uses PDO prepared statements, `password_hash()`, `password_verify()`, session timeout, CSRF protection, output escaping, image upload validation, role checks, and audit logging.
