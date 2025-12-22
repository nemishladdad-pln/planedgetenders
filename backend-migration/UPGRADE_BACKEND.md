# Backend Upgrade Guide — PlanEdge Gateway

This document captures the safe, repeatable steps to perform a full dependency upgrade (including major versions) and a runtime upgrade for the gateway in `backend-migration`.

IMPORTANT: perform these steps on a feature branch and ensure you have a working CI/CD or manual rollback plan.

1) Prepare
- Create a branch:
```cmd
cd backend-migration
git checkout -b backend-upgrade/$(date +%Y%m%d)-audit
```
- Commit any local work and make sure `master`/`main` is clean.

2) Audit current state
- Inspect installed packages and outdated list:
```cmd
cd backend-migration
npm install
npm outdated
```
- Save the output (paste in the PR description). Also inspect `Dockerfile`, `docker-compose.yml`, and `tsconfig.json` for runtime/target settings.

3) Safe upgrades (minor/patch)
- Upgrade non-breaking versions first. From the repo root run:
```cmd
cd backend-migration
npx npm-check-updates -u --target minor
npm install
npm run build
npm run test:run
```
- Fix issues found by TypeScript or runtime failures.

4) Major upgrades (careful)
- Use `npx npm-check-updates -u` to update major versions after reviewing changelogs.
- Apply changes incrementally: update a small set of packages, rebuild, run `npm run build` and `npm run test:run`, and verify functionality.

5) Runtime upgrade (Docker/Node)
- The Dockerfile has been updated to a newer Node image. If you prefer a specific Node LTS, edit `Dockerfile` `FROM` line (e.g., `node:20-bullseye-slim`).
- Rebuild images locally to validate:
```cmd
cd backend-migration
docker-compose build --no-cache
docker-compose up -d
```
- Check logs: `docker-compose logs -f gateway`.

6) CI and other environments
- Update CI runner to use the same Node version.
- Update production Docker registries and deploy to a staging environment first.

7) Rollback plan
- Keep the previous image tag available. If the new image fails, stop the new service and re-deploy the previous image.

8) Notes about TypeScript and Node features
- If you bump Node major (e.g., 18 -> 20), TypeScript `target` can remain `ES2020` initially. After confirming runtime tests pass, you can consider raising `target` to `ES2022` or later to use newer language features.

9) Helpful commands
```cmd
cd backend-migration
git checkout -b backend-upgrade/full-upgrade
npx npm-check-updates -u
npm install
npm run build
npm run test:run
```

10) After successful upgrade
- Update `DEVELOPMENT.md` and `backend-migration/README.md` with new Node and package versions.
- Open a PR, describe changes, list failing tests/issues and remediation steps.

If you want, I can run the audit (`npm outdated`) and prepare a PR with package updates and the Dockerfile change applied in a branch. I will stop and report failing tests for manual review before proceeding with any breaking changes.
