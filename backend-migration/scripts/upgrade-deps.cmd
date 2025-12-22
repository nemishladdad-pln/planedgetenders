@echo off
REM Upgrade Node dependencies using npm-check-updates (Windows wrapper)
cd /d %~dp0\..
echo Running npm-check-updates (will update package.json)...
npx npm-check-updates -u
if %ERRORLEVEL% neq 0 (
  echo npm-check-updates failed
  exit /b %ERRORLEVEL%
)
echo Installing updated dependencies...
npm install
echo Building project...
npm run build
echo Running sanity script (test:run)...
npm run test:run
echo Done. Review output for errors.
