Frontend (Next.js) scaffold

This folder is a suggested Next.js migration from the existing Vite React app. It provides mobile-friendly pages and components.

Quick start:

```bash
cd frontend-next
npm install
npm run dev
```

Notes:
- The scaffold uses Tailwind CSS. You can adapt components and pages to match the existing design tokens.
- Pages intentionally call the gateway endpoints (`/api/tenders/...`) which the existing backend serves. In production, configure a reverse proxy or set `NEXT_PUBLIC_API_BASE`.

## Development notes
- Base API: pages call the gateway at relative paths (e.g. `/api/tenders/upcoming`). For a separate frontend host set `NEXT_PUBLIC_API_BASE` and update fetcher calls accordingly.
- Important pages and components:
	- `pages/index.tsx` — Upcoming tenders list (calls `/api/tenders/upcoming`)
	- `pages/vendor/[id].tsx` — Vendor profile page (calls `/api/tenders/vendors/:id`)
	- `components/Infographic.tsx` — KPI cards scaffold (wire to `/api/tenders/admin/dashboard/summary`)
	- `components/VendorProfile.tsx` — small vendor profile card
- Paywall gating: backend supports partial vendor registration (`POST /api/tenders/vendors/partial`) and completion; implement multi-step forms with a payment step before showing extended fields.
- CSV/Excel uploads: admin UI should upload files to `/api/tenders/:id/budget` (multipart/form-data with field `budgetFile`) and display parsed preview (`budget.items` is returned).
- Authentication: current gateway uses `x-role` for testing role-protected endpoints. Add a proper auth provider (JWT/Sessions) and inject role claims into API requests.
- Mobile-first: pages are responsive by default, but visual polish and accessibility checks are necessary.

## Recommended next frontend tasks
1. Create a design system file (colors, spacing, font tokens) and apply to pages.
2. Wire `Infographic` to the dashboard summary endpoint and add a small chart for monthly series.
3. Implement multi-step registration with pre-paywall and post-paywall fields.
4. Implement CSV column mapping UI for budget uploads.

## Build & Deploy
- `npm run build` to produce an optimized production build.
- Serve with `npm run start` or build into Docker image; ensure `NEXT_PUBLIC_API_BASE` is set for API calls.
