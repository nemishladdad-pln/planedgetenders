Planedge — Hosting & Local Development Guide

Purpose
This document describes what was implemented, how to host the gateway and frontend locally, and recommended post-deploy workflows. It also explains how to access Admin-level functionality in the current deployment and provides example admin credentials for a fresh local installation (change immediately in production).

1) High-level architecture
- Gateway: `backend-migration` — Express + TypeScript API gateway that consolidates new endpoints and proxies to an existing Laravel backend when needed.
- Data store: simple JSON file store for rapid development (`DATA_STORE_FILE`, default `./data/db.json`).
- Frontend (modern): `frontend-next` — Next.js + Tailwind scaffold (mobile-first pages and components).
- WhatsApp: gateway skeleton at `/api/whatsapp` to proxy sends to a provider when configured.

2) Key implemented features
(See CHANGELOG.md for full list)
- Tenders: category + `subcategory`, budget upload (lumpsum or CSV), signed workorder upload
- Vendors: partial registration (pre-paywall), completion flow (post-paywall), vendor history with pagination
- Subscriptions: create/list/update (yearly default)
- Invoices: create/list, `registrationDate` and `dueDate` fields
- Dashboard endpoints: counts, status tallies, monthly tender series, top vendors
- Calendar: tender/invoice events with `dueSoon` and `overdue` flags
- Mobile scaffolding: mobile endpoints for app integration
- WhatsApp gateway skeleton and OTP send placeholder

3) Important environment variables (.env)
- `PORT` (default 4000)
- `UPLOAD_DIR` (default `./data/uploads`) — directory for uploaded files; served at `/uploads`
- `DATA_STORE_FILE` (default `./data/db.json`) — JSON datastore used by gateway
- `ACTIVITY_LOG_FILE` (default `./data/activity.log`)
- `LARAVEL_API_URL` — existing Laravel installation used for proxying where needed
- `WHATSAPP_API_URL`, `WHATSAPP_API_KEY` — set to enable WhatsApp provider proxying

4) Local development — Gateway
Prerequisites: Node 18+, npm

Commands
```powershell
cd backend-migration
npm install
# build
npm run build
# run dev (ts-node-dev) for rapid development
npm run dev
# or run built output
node dist/index.js
```
Notes
- Uploads directory `UPLOAD_DIR` is created if missing. Uploaded files are served at `http://localhost:4000/uploads/<filename>`.
- Activity logs are appended to `ACTIVITY_LOG_FILE`.
- The gateway stores data in `DATA_STORE_FILE`. Back this up before replacing.

5) Local development — Frontend (Next.js)
Prerequisites: Node 18+, npm

Commands
```powershell
cd frontend-next
npm install
npm run dev
# build for production
npm run build
npm run start
```
Notes
- The scaffold calls gateway endpoints at relative paths (for local dev both run on same host). If hosting frontend separately set `NEXT_PUBLIC_API_BASE` and update API calls.

6) Docker / Production (suggested)
- A simple Dockerfile exists in `backend-migration` and `frontend-next` can be containerized. Use a reverse proxy (NGINX) to route frontend and API.
- Example (gateway):
```bash
# build image
docker build -t planedge-gateway:latest backend-migration
# run
docker run -p 4000:4000 -e PORT=4000 -e DATA_STORE_FILE=/data/db.json -v ./backend-migration/data:/data planedge-gateway:latest
```
- For full-stack deployment use docker-compose (customize env values and volumes) or Kubernetes for scaled deployments.

7) Post-deploy workflows
- Backups
  - Regularly snapshot `DATA_STORE_FILE` and `ACTIVITY_LOG_FILE` (daily). If migrated to Postgres, configure database backups (pg_dump or managed DB backups).
- Logs & monitoring
  - Monitor gateway logs and activity log. Add Prometheus metrics export or integrate with a logging platform (ELK / Datadog).
- Health checks
  - Configure a health check hitting `GET /health` and alert if non-200.
- File storage
  - Consider migrating uploads to a durable object store (S3) and update `UPLOAD_DIR` handling.
- Security
  - Replace the development `x-role` header mechanism with proper authentication (JWT or OAuth2) and enforce TLS for all endpoints.

8) Admin access and credentials (local/dev)
Notes about auth: the gateway currently uses a convenient `x-role` header for role-protected routes (development). There is no built-in secure login flow in the gateway; the header is required to access Admin endpoints. For production you must replace this with a real auth system.

To emulate Admin API access (dev): send requests with header:
- `x-role: Admin`

Example curl (admin dashboard)
```bash
curl -H "x-role: Admin" http://localhost:4000/api/tenders/admin/dashboard
```

Example system admin (OS) account for local hosting (change immediately in production):
- Username: `planedge-admin`
- Password: `ChangeMeNow!2025`

(These are suggested OS user credentials for server-level access — create the Linux system user and set the password; do NOT use these in production unchanged.)

9) How to create a real admin user (recommended)
- Implement an authentication provider (JWT or OAuth) and a user store (Postgres). Create an `Admin` role and seed an admin user with a secure password.
- For quick local setup (development), you can add a user entry to `DATA_STORE_FILE` under `users` array. Example structure:
```json
{
  "id": "u-1",
  "email": "admin@planedge.local",
  "role": "Admin",
  "password": "<hashed-password>"
}
```
Note: The gateway currently does not validate passwords — ensure you add a real auth mechanism before using this in production.

10) Important caveats and next migration items
- Migrate the JSON store to Postgres/MySQL for reliability and concurrent access handling.
- Implement proper authentication and session management.
- Replace `x-role` development header with verified role claims from your auth provider.
- Configure a WhatsApp provider and set `WHATSAPP_API_URL` and `WHATSAPP_API_KEY` to enable OTP delivery.
- Add CI/CD pipelines and automated tests for all endpoints before production deployment.

11) Contacts & support
- If you want, I can implement DB migrations, CI/CD pipelines, or integrate a WhatsApp provider — tell me which to prioritize.

---
Generated: 2025-12-23
*** End Patch