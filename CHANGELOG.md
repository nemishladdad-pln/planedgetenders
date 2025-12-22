# Changelog — Planedge Gateway + Frontend Scaffold

All notable changes to this project are recorded here.

## 2025-12-22 — Phase 1 / Phase 2 / Phase 3 work (implemented)

Implemented backend features (gateway)
- Tenders: create/list, category + `subcategory`, budget upload (lumpsum or CSV parsed), re-tender helper
- Signed work orders: contractors can upload signed work orders; persisted link on tender
- Vendors: partial registration (pre-paywall), complete registration (post-paywall), history append and paginated history endpoint
- Buyers: registration requires Admin approval; approval endpoint added
- Subscriptions: create/list/update subscription model (yearly default)
- Invoices: create/list invoices including `registrationDate` and `dueDate`; user invoice listing
- Dashboard: admin endpoints return counts, tender status tallies, monthly tender series, top vendors
- Calendar: combined tender + invoice events with `dueSoon` and `overdue` flags
- Activity log: append and read activities; allow multiple activities per email
- Mobile scaffolding: mobile-friendly endpoints for tenders, dashboard and vendors
- WhatsApp: placeholder send + gateway skeleton to proxy to external provider

Frontend scaffold
- Added `frontend-next/` Next.js + Tailwind scaffold with sample pages:
  - Upcoming tenders list (`/`)
  - Vendor profile (`/vendor/[id]`)
  - `Infographic` and `VendorProfile` components (scaffolds)

Developer utilities
- `docs/openapi.yaml` (basic OpenAPI skeleton)
- `backend-migration/.env.example` extended with upload and WhatsApp variables
- `backend-migration/README.md` updated with guidance and endpoints

## Notes & next steps
- Persisting data: gateway uses an on-disk JSON store for rapid development. Plan a migration to PostgreSQL/MySQL with proper migrations and schema.
- WhatsApp: requires provider credentials to enable actual delivery; webhook handling is a placeholder.
- Frontend: UI visual polish, responsiveness, and paywall gated registration need design and implementation.
- Tests & CI: add unit and integration tests and configure GitHub Actions.

