Frontend (React + TypeScript + Vite + Tailwind)

Quick start:
1. cd frontend-react
2. npm install
3. npm run dev

Default dev server: http://localhost:3000
API proxy: /api -> http://localhost:8000 (Laravel) by default. Adjust BACKEND_URL env when running.

Build:
- npm run build
- Output optionally set to ../public_html/frontend to serve from Laravel public folder.

Designer notes:
- Components in src/components are intentionally minimal and well-typed for designers to iterate.
- Replace Tailwind tokens in tailwind.config.cjs to match brand.
- Add Storybook or Figma tokens next for tighter design handoff.
