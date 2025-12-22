**Project Overview**
- **Purpose:**: This repository contains the PlanEdge codebase: a TypeScript-based gateway (in `backend-migration`) that proxies and coordinates requests to a Laravel API (in `public_html/api`) and a React frontend (in `frontend-react`).
- **Primary components:**: `backend-migration` (Node/TypeScript gateway), `frontend-react` (Vite + React UI), `public_html/api` (Laravel API), plus assorted scripts and deployment artifacts in `scripts/`.

**Repository Structure (high level)**
- **`backend-migration/`**: Node TypeScript gateway service. Key files:
  - `src/index.ts` — app entry.
  - `src/routes/` — Express routes (`auth.ts`, `tenders.ts`).
  - `src/middleware/roleMiddleware.ts` — role-based access control.
  - `src/services/` — integrations (Laravel proxy, WhatsApp bridge, activity store, etc.).
  - `docker-compose.yml`, `Dockerfile` — container definitions for the gateway.
  - `planedge-gateway.service` — systemd service file (packaged for Linux deployments).
- **`frontend-react/`**: Vite + React SPA. Key files: `src/`, `package.json`, `vite.config.ts`.
- **`public_html/api/`**: Laravel application (PHP). Unit and feature tests live under `tests/` (e.g., `tests/Feature/GatewayProxyTest.php`).
- **`scripts/` and root-level files**: helper scripts, install/deploy helpers.

**Critical Areas — read before editing**
- **Gateway entry (`src/index.ts`) and route handlers:**: Changing server configuration, CORS, or request/response mapping will affect all clients. Validate with `testEndpoints` after changes.
- **`src/services/laravelProxy.ts` and `public_html/api` integration:**: This code translates and forwards requests to Laravel. Mistakes here can silently break business logic or authentication flows.
- **Authentication (`routes/auth.ts`) and role middleware:**: Review `roleMiddleware.ts` when altering roles or access checks; tests and manual verification are required.
- **File upload handling (`multer`) and `src/services/store.ts` or `activityStore.ts`:**: These touch storage and security. Ensure uploads are validated and that storage credentials/paths are safe.
- **Third-party integrations (WhatsApp, email):**: See `services/whatsapp.ts` and `nodemailer` usage. Secrets live in environment configuration; do not commit them.
- **Production artifacts (`planedge-gateway.service`, Dockerfile, docker-compose.yml`):**: Review and test rebuilds when changing port, environment variable names, or paths.

**Infrastructure & Deployment Notes**
- **Containers:**: `backend-migration/docker-compose.yml` and `Dockerfile` build the gateway. Use `docker-compose up --build` to build and run locally in a container.
- **Systemd service:**: `backend-migration/planedge-gateway.service` is included for Linux deployments — it’s not used on Windows development machines.
- **Environment variables:**: The gateway uses `dotenv`. Place runtime variables in `.env` (local dev) and in your container or host service environment in production.
- **Static frontend:**: Built frontend assets may be served from `public_html/admin` (prebuilt static files). The React source in `frontend-react/` is where edits belong.

**How to run and test locally**
- **Backend gateway (dev)**:
```bat
cd backend-migration
npm install
npm run dev
```
  - `dev` uses `ts-node-dev` (fast reload). Use `npm run build` then `npm start` for a production-like run.

- **Backend gateway (Docker)**:
```bat
cd backend-migration
docker-compose up --build -d
```

- **Frontend (dev)**:
```bat
cd frontend-react
npm install
npm run dev
```

- **Run gateway integration test script (sanity)**:
```bat
cd backend-migration
npm run build
npm run test:run
```
  - `test:run` executes the built `dist/scripts/testEndpoints.js` script. It’s a quick sanity check against configured endpoints.

**Editing guidance & safe-change checklist**
Before editing core files, follow this checklist:
- **1) Understand ownership:** Identify the files you will change and any dependent modules (search `src/services`, `src/routes`, `public_html/api`).
- **2) Run tests locally:** For Node code, run the gateway in dev mode and use `npm run test:run` after building. For Laravel, run PHPUnit inside `public_html/api`.
- **3) Validate env vars:** Add new config keys to `.env.example` and document them. Update `docker-compose.yml` and `planedge-gateway.service` if variable names change.
- **4) Avoid secret leakage:** Never commit secrets. Use `ENV` in container configs and secret management in CI.
- **5) Update static/public builds:** If API responses change, ensure the frontend still expects the same shape. Rebuild `frontend-react` if needed.
- **6) Run integration sanity checks:** Use `backend-migration/src/scripts/testEndpoints.ts` or `dist/scripts/testEndpoints.js` after building.
- **7) Version & changelog:** Bump `backend-migration/package.json` version when releasing gateway changes and note breaking changes.

**PR Checklist**
- **Run unit/integration tests.**
- **Build both gateway and frontend.**
- **Smoke test major flows:** auth, tenders, file upload, notifications.
- **Update `DEVELOPMENT.md` or top-level `README.md`** if you introduce new infra, env vars, or deploy steps.

**Troubleshooting & common fixes**
- **Gateway crashes on startup:** Missing or malformed `.env` keys — check logs and ensure required env vars are set.
- **Requests failing intermittently:** Check `laravelProxy` timeouts and network connectivity to `public_html/api`.
- **File upload errors:** Confirm `multer` setup, disk permissions, and configured storage paths.

**Where to look for more context**
- **Gateway service definitions:** `backend-migration/Dockerfile`, `backend-migration/docker-compose.yml`, `backend-migration/planedge-gateway.service`.
- **Gateway scripts and helpers:** `backend-migration/src/scripts/` (including `testEndpoints.ts`).
- **Laravel tests:** `tests/Feature/GatewayProxyTest.php` under `public_html/api/tests/Feature`.

**Contacts / Owners**
- **Gateway:** Look for the most recent committers in `backend-migration` git history.
- **Frontend:** Check `frontend-react` committers.

If you want, I can also:
- Add a short `README` inside `backend-migration/` with gateway-specific quickstart.
- Add a runnable `Makefile` or a Windows `scripts/dev.cmd` wrapper that centralizes common commands.

---
File created by automation: keep this file updated when infra or flows change.
