This is a minimal Express + TypeScript API gateway to start incremental backend upgrades.

# Purpose
- Proxy selected Laravel endpoints without touching existing Laravel hosting.
- Provide new endpoints/flows that can be migrated here gradually.

# Quick start (dev)
1. Copy environment variables from your host or create a .env file containing:
   LARAVEL_API_URL=https://your-existing-laravel-host
   PORT=4000

2. Install and run:
   npm install
   npm run dev

3. In another shell: npx ts-node src/scripts/testEndpoints.ts

# Production (build + docker)
- npm run build
- docker build -t planedge-gateway:latest .
- docker run -e LARAVEL_API_URL=https://your-laravel -p 4000:4000 planedge-gateway:latest

# Integration approach
- Start by proxying read-only endpoints (list tenders) so the frontend can switch to the new gateway without changing Laravel.
- Add new endpoints here for heavier features (advanced search, consolidated reports, activity logs).
- Ensure server-side role checks remain enforced in Laravel until full migration is complete.

This service is intentionally additive and will not modify existing hosting or environment settings.

# Run locally:
1. Copy .env from .env.example or use the provided .env and adjust values.
2. npm install
3. npm run dev

# Dev tests:
- In another shell: npx ts-node src/scripts/testEndpoints.ts
- Or build and run: npm run build && npm run test:run

# Docker:
- docker compose up --build
- Data and activity logs persist in ./data

# Notes:
- The gateway now stores tenders/vendors in a local JSON store (DATA_STORE_FILE). Use query param ?proxy=1 to force proxy to existing Laravel backend.
- Use header x-role: Admin|ProjectManager|Vendor|Viewer for role-protected routes.

## Recent changes and development notes
- This gateway now implements a number of new features (see CHANGELOG.md). Key items implemented in this repository:
   - Tenders: category + subcategory, budget upload (lumpsum or CSV/Excel), signed work order upload
   - Vendors: partial registration (pre-paywall), complete registration (post-payment), vendor history
   - Subscriptions: create/list/update subscriptions (basic yearly flow)
   - Invoices: creation and listing, `registrationDate` and `dueDate` fields
   - Dashboard endpoints: user/tender counts, tender status, monthly tender series, top vendors
   - Calendar endpoint: returns tender and invoice events with `dueSoon` and `overdue` flags
   - Mobile endpoints: minimal mobile-friendly endpoints for tenders, dashboard, and vendor profiles
   - WhatsApp: placeholder OTP send and a gateway skeleton available at `/api/whatsapp` (requires provider config)

## Important environment variables (copy to .env)
- `PORT` - server port (default 4000)
- `UPLOAD_DIR` - path to serve/store uploaded files (default `./data/uploads`)
- `DATA_STORE_FILE` - local JSON store path (default `./data/db.json`)
- `ACTIVITY_LOG_FILE` - activity log file (default `./data/activity.log`)
- `LARAVEL_API_URL` - existing Laravel backend URL for proxying
- `WHATSAPP_API_URL`, `WHATSAPP_API_KEY` - (optional) provider endpoint + key for WhatsApp proxying

## Developer guidance
- Data store: currently a simple JSON file. For production, migrate to Postgres/MySQL and implement migrations (suggested next step).
- Authentication: API uses an `x-role` header for role-guarded routes in this gateway (development convenience). Replace with real auth (JWT / OAuth2 / session) in production.
- Uploads: Files are written to `UPLOAD_DIR` and served from `/uploads`. Restrict access and validate content in production.
- WhatsApp: OTPs are sent via `services/whatsapp.ts` which either logs (placeholder) or calls `WHATSAPP_API_URL`. To enable delivery, set provider vars and implement secure webhook handling.
- Tests: basic sanity checks live in repo scripts; add unit/integration tests and GitHub Actions for CI.

## Useful endpoints (examples)
- Health: `GET /health`
- List tenders: `GET /api/tenders`
- Upcoming tenders: `GET /api/tenders/upcoming`
- Budget upload: `POST /api/tenders/:id/budget` (multipart/form-data with `budgetFile` or JSON lumpsum)
- Upload signed workorder: `POST /api/tenders/:id/upload-signed` (Contractor/Admin role)
- Vendor partial registration: `POST /api/tenders/vendors/partial`
- Vendor profile: `GET /api/tenders/vendors/:id`
- Vendor history: `GET /api/tenders/vendors/:id/history?offset=0&limit=20`
- Admin dashboard: `GET /api/tenders/admin/dashboard` and `GET /api/tenders/admin/dashboard/summary` (require `x-role: Admin` header)
- Calendar: `GET /api/tenders/calendar`
- WhatsApp gateway: `POST /api/whatsapp/send` (proxy) and `POST /api/whatsapp/webhook` (provider callbacks)

If you need me to add DB migrations, CI workflows, or wire a real WhatsApp provider, tell me which to prioritize.

# Planedge Gateway - Self-hosting guide (summary)

## Features implemented:
- Local JSON store for tenders, vendors, subscriptions, invoices.
- Tender: category + subcategory, budget upload (lumpsum or file), signed work order upload.
- Vendor: partial registration before paywall, complete after payment, history tracking.
- Mobile endpoints and OTP via WhatsApp placeholder.
- Buyer registration requires admin approval.
- Yearly subscription module.
- Dashboard endpoints: users count, tender count, tender status counts.
- Calendar endpoint with conditional dueSoon flag.
- Invoice endpoints with registration/due date fields.
- File uploads saved under data/uploads.

## Self-host (physical server, no Docker):
1. Copy this folder to your server, e.g. /opt/planedge-gateway
2. Ensure Node 18+ is installed.
3. Create a system user (optional): useradd --system --no-create-home planedge
4. Copy .env (edit values) and ensure paths are writable.
5. Install deps: npm ci --production
6. Build: npm run build
7. Start: node dist/index.js or install systemd unit (see scripts/install_service.sh)

Use the provided install script to automate steps:
sudo ./scripts/install_service.sh /opt/planedge-gateway

## Notes:
- To integrate WhatsApp, set WHATSAPP_API_URL and WHATSAPP_API_KEY.
- This service is additive — you can use ?proxy=1 to forward calls to your existing Laravel backend for gradual migration.
