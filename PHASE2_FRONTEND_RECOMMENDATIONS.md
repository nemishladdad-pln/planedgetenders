Phase 2 Frontend Recommendations for PlanEdge

Purpose: collate UI tasks and component changes the frontend team should implement when Phase 2 backend features are wired.

1) Dashboard & Infographics
- Create `src/components/Infographic.tsx` that accepts metrics props: users, tenders, subscriptions, tenderStatus.
- Create `src/pages/Dashboard.tsx` updated to call new endpoint `/api/tenders/admin/dashboard/summary` and render:
  - KPI cards: total users, total tenders, active subscriptions, pending subscriptions.
  - Small line chart (last 12 months) for tenders per month.
  - Top vendors list.
- Use a lightweight chart library (Chart.js or Recharts). Keep design consistent with existing Tailwind tokens.

2) Vendor Profile & History
- Create `src/pages/VendorProfile.tsx`.
- Calls `/api/tenders/vendors/:id` for profile and `/api/tenders/vendors/:id/history?offset=0&limit=20` for paginated history.
- Render profile summary (name, contact, registration date), and an activity timeline component (infinite scroll / load more).

3) Shortened Vendor Registration (pre-paywall)
- On `Register` flow, split form into:
  - Step A (pre-paywall): minimal fields: `name`, `contact`, `email` and `business type`.
  - Payment screen / paywall.
  - Step B (post-paywall): additional eligibility fields showing only after successful payment.
- Show clear progress indicator (Step 1/3).

4) Upcoming Tenders UI
- Update `TenderList` to call `/api/tenders/upcoming` and surface `dueDate` prominently with conditional coloring:
  - dueSoon (<=7 days): yellow/orange
  - overdue: red
  - future: neutral
- Add timezone-aware date display and relative label (e.g., "in 3 days").

5) Budget Upload Page
- For Admins, `BudgetUploadPage` should offer two options: "Upload CSV/Excel" or "Enter lumpsum".
- When CSV is uploaded, show a preview table of parsed rows and allow mapping of CSV columns to expected fields.

6) Calendar
- Add a calendar view (`/calendar`) using FullCalendar or a simple grid.
- Fetch `/api/tenders/calendar` and style events by type (`tender`, `invoice`) and `dueSoon` flag.

7) Subscriptions / Paywall UI
- Add `Account > Subscriptions` page to show subscription status and next billing date using `/api/tenders/admin/dashboard/summary` for counts and `/api/subscriptions` for user subscriptions.
- Add Renew / Cancel buttons that call `/api/auth/subscribe` or `/api/auth/subscriptions/:id` (PATCH).

8) WhatsApp OTP UX
- On mobile, when requesting OTP, offer "Send to WhatsApp" with clear consent text.
- Provide fallback for SMS/email in case WhatsApp delivery fails.

9) Mobile Considerations
- Ensure API calls have mobile-friendly projections (use existing `/api/auth/mobile/tenders`).
- Build small, reusable components suitable for React Native migration (stateless presentational components).

Design / Accessibility
- Use Tailwind tokens; ensure components follow accessible contrast and have keyboard focus states.
- Keep pages responsive and test at small phone widths (360x780) and tablet.

Developer notes
- Mock backend responses in Storybook for component dev before backend wiring.
- Add unit tests for critical flows: vendor registration, budget upload preview, and subscription renewal.

Suggested first frontend tasks
1. Implement `Infographic` and wire to Dashboard summary endpoint.
2. Implement `VendorProfile` page.
3. Update `Register` flow to split pre/post-paywall steps.

End of recommendations.
