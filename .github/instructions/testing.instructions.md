# Testing Guidelines for Avatar Steward

1. **Automated tests first**
   - Add or update PHPUnit tests for every PHP service added in `src/`.
   - Create integration tests for WordPress hooks (`pre_get_avatar_data`, REST endpoints) when behavior changes.
2. **Test locations**
   - Place unit tests under `tests/phpunit/` mirroring the namespace structure.
   - Store integration/e2e test plans in `docs/reports/tests/` and update the README in that directory with execution steps.
3. **Commands to run**
   - `composer lint` – WordPress Coding Standards (phpcs).
   - `composer test` – PHPUnit suite with coverage.
   - `npm run lint` – ESLint/SASS linting (configure as per `documentacion/05_Stack_Tecnologico.md`).
4. **CI expectations**
   - Ensure GitHub Actions (or chosen CI) runs the commands above on every pull request.
   - Failing checks must be fixed before merge. Do not skip tests unless a ticket documents the justification.
5. **Reporting**
   - Export lint/test results to `docs/reports/linting/` and `docs/reports/tests/` as part of the Definition of Done (see `documentacion/04_Plan_de_Trabajo.md`).
   - Log security scans (SAST/DAST) when applicable in `docs/reports/security-scan.md`.
6. **Manual QA**
   - Follow the QA scenarios outlined in `documentacion/04_Plan_de_Trabajo.md` (migración, modo low-bandwidth, auditoría).
   - Record manual findings in `docs/qa/` with date, tester, environment and status.
