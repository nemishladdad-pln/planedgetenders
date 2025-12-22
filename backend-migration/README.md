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
