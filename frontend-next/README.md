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
