# API Documentation

Base path: `/api`

Authentication uses `Authorization: Bearer <token>` for protected routes. Login and register return the same user shape as the original Express backend:

```json
{"token":"...","user":{"id":"1","email":"admin@cafejavas.test","role":"admin","name":"System Administrator","avatarUrl":null}}
```

## Auth

| Method | Endpoint | Access | Description |
|---|---|---|---|
| POST | `/auth/login` | Public | Login with `email`, `password` |
| POST | `/auth/register` | First user public, later admin | Create user |
| GET | `/auth/me` | Authenticated | Current user |
| GET | `/auth/users?role=staff` | Admin | List users |
| PATCH | `/auth/profile` | Authenticated | Update `name`, `avatar`, `currentPassword`, `newPassword` |

## Feedback

| Method | Endpoint | Access | Description |
|---|---|---|---|
| GET | `/feedback/summary` | Admin | Dashboard totals, categories, monthly/daily series |
| GET | `/feedback` | Public | List feedback with `page`, `limit`, `status`, `category`, `type`, `priority`, `assignedTo`, `branch_id`, `search`, `sort` |
| GET | `/feedback/{id}` | Public | Get feedback |
| POST | `/feedback` | Public | Submit feedback |
| POST | `/feedback/{id}/assign` | Admin | Set `assignedTo`, status becomes `in-progress` |
| POST | `/feedback/{id}/respond` | Admin, Staff | Save `response`, sets `respondedAt` |
| POST | `/feedback/{id}/resolve` | Admin, Staff | Status becomes `resolved`, sets `resolvedAt` |
| POST | `/feedback/{id}/escalate` | Admin, Staff | Status becomes `escalated`, priority becomes `high` |
| PATCH | `/feedback/{id}` | Admin, Staff | Update feedback fields |
| DELETE | `/feedback/{id}` | Admin | Delete feedback |

## Contacts And Notifications

| Method | Endpoint | Access | Description |
|---|---|---|---|
| GET | `/contacts?search=` | Admin | Active contacts |
| POST | `/contacts` | Admin | Create contact; `name` and `phone` required |
| DELETE | `/contacts/{id}` | Admin | Soft-delete contact |
| GET | `/notifications/admin` | Admin | Pending complaint and escalation counts |
