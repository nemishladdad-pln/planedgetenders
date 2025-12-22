**PlanEdge Feature Roadmap & Implementation Notes**

Purpose: map the requested UI and backend requirements into implementable tasks, priorities, suggested technical approach, and next steps. Use this as the single source of truth while work is split across teams.

1) UI
- Requirement: Frontend (landing pages) must be aesthetically pleasing
  - Priority: High
  - Owner: Frontend designer / React dev
  - Notes: Introduce a simple design system (colors, typography, spacing). Implement a `components/layout` set and update `frontend-react/src/App.tsx` to use the new layout. Consider Tailwind utilities already present in `frontend-react/`.
  - Deliverables: new landing page designs (Figma or static mockups), React components, responsive breakpoints.

- Requirement: Upcoming tenders must be displayed correctly
  - Priority: High
  - Owner: Frontend + Backend
  - Notes: Verify API shape returned by the gateway (see `backend-migration/src/routes/tenders.ts` and `src/services/laravelProxy.ts`). Add client-side components `TenderList`, `TenderCard` (already present) and ensure date/time timezone handling is consistent.
  - Test: end-to-end check with `backend-migration/src/scripts/testEndpoints.ts`.

- Requirement: Planedge infographics
  - Priority: Medium
  - Owner: Frontend + Data owner
  - Notes: Prepare a small set of metrics (registered users, tenders floated, tender status distribution). Add an `Infographic` component and connect it to a new backend stats endpoint.

- Requirement: Reduce registration time
  - Priority: High
  - Owner: Frontend + Product
  - Notes: Shorten initial vendor form; defer optional/long fields post-paywall. Add progressive disclosure in registration forms and connect to existing registration endpoints.

2) Backend
- Requirement: Add subcategory to header tasks (e.g., RCC has 3 categories)
  - Priority: Medium
  - Owner: Backend
  - Notes: Modify data model in Laravel (`public_html/api`) to include `subcategory` for tenders or header tasks. Update gateway proxy routes to pass the new field and frontend to consume it.

- Requirement: Easy budget upload (lumpsum or format change)
  - Priority: High
  - Owner: Backend + Frontend
  - Notes: Add a new upload endpoint that accepts CSV/Excel and a lumpsum JSON payload. Use `multer` in `backend-migration` for uploads and validate on the server. Provide frontend UI to choose upload method.

- Requirement: Mobile application
  - Priority: Medium
  - Owner: Product + Mobile dev
  - Notes: Recommend building a cross-platform app (React Native or Expo) reusing API endpoints. Start by drafting API coverage and auth flows.

- Requirement: 1 Mail ID, multiple activity registrations
  - Priority: Medium
  - Owner: Backend
  - Notes: Allow one email to map to multiple registrations. Update database schema and uniqueness constraints in Laravel. Add admin UI to merge or manage activities.

- Requirement: Yearly subscription module
  - Priority: High
  - Owner: Backend + Billing
  - Notes: Add subscription model, billing events, invoicing. Integrate a payment gateway (Stripe/PayPal) or offline invoice flow. Add feature flags for paywall enforcement.

- Requirement: Vendor Profile development including history
  - Priority: High
  - Owner: Backend + Frontend
  - Notes: Extend vendor model to track historical activity (registrations, awarded works, submitted documents). Expose endpoints for profile and history.

- Requirement: Contractor upload signed work order
  - Priority: Medium
  - Owner: Backend + Frontend
  - Notes: Add an upload endpoint, store signed documents (S3 or disk), and surface status in the dashboard.

- Requirement: Dashboard of works and reports (Admin, Client, Contractor)
  - Priority: High
  - Owner: Full stack
  - Notes: Build role-aware dashboard endpoints and UI. KPIs:
    - Number of registered users
    - Number of tenders floated
    - Status of tenders
  - Implementation: backend aggregated endpoints and frontend Dashboard page(s).

- Requirement: Accounts Profile for Invoices (Registration Date, Due Date)
  - Priority: Medium
  - Owner: Backend + Accounts
  - Notes: Add invoice model fields and admin UI to manage invoices; include CSV export.

- Requirement: Calendar (tender due, works due + conditional formatting)
  - Priority: Medium
  - Owner: Frontend + Backend
  - Notes: Add a calendar component (FullCalendar or lightweight custom) in `frontend-react`. Backend should serve events with type/status for conditional styling.

- Requirement: OTP and updates should go on WhatsApp
  - Priority: Medium
  - Owner: Backend
  - Notes: Use existing `services/whatsapp.ts` as entry; integrate with an official WhatsApp Business API provider or Twilio WhatsApp. Ensure OTP delivery fallback to SMS/email.

- Requirement: Vendor Registration form shortened pre-paywall; more forms post-paywall for eligibility
  - Priority: High
  - Owner: Frontend + Backend
  - Notes: Implement form gating: minimal fields to register and wallet/paywall check; after payment, show extended profile fields.

- Requirement: Buyer registration should require admin approval
  - Priority: High
  - Owner: Backend + Admin UI
  - Notes: Add `approved` flag to buyer accounts and admin endpoint/UI to approve/reject signups. Block buyer actions until approved.

3) Cross-cutting & Operational
- Tests & CI:
  - Add unit tests for gateway routes and Laravel endpoints. Add integration tests that run `backend-migration` build and `test:run` as a sanity check.
  - Add a GitHub Actions workflow to build Node gateway, run Node tests, and run Laravel PHPUnit tests (if CI runner has PHP).

- Metrics & Monitoring:
  - Expose simple metrics endpoints (counts of users, tenders, status) and a basic Prometheus-compatible endpoint or push metrics to a monitoring service.

4) Suggested Implementation Phases (deliver incrementally)
- Phase 0 — Planning (this document + design mocks + API spec)
- Phase 1 — High-impact items: Shorten vendor registration, upcoming tenders correctness, budget upload, buyer approval
- Phase 2 — Billing/subscriptions, vendor profile history, dashboard KPIs
- Phase 3 — Mobile app planning + WhatsApp OTP/integration, calendar and conditional formatting

5) Next immediate actions (pick one to start)
- A) Create `backend-migration/README.md` and `.env.example` (low friction, quick wins)
- B) Frontend: Produce landing page mockups and component skeletons in `frontend-react/src/components`
- C) Backend: Add `approved` flag for buyers and implement admin approval endpoint in Laravel + gateway

6) What I need from you to proceed with development
- Prioritization: which items should we do first?
- Access to any design assets, payment provider credentials, WhatsApp API provider credentials, and deployment targets.
- Any constraints (budget, timeline) for subscription/billing decisions.

---
Use the `todo` list in the repository root or in your project management tool to assign these tasks to engineers and track progress.
