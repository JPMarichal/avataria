# Definition of Done - Avatar Steward

## 1. General Criteria
- **Build**: `docker compose -f docker-compose.dev.yml up -d` ejecuta sin errores y el entorno queda accesible en `http://localhost:8080`.
- **Linting**: `composer lint` y `npm run lint` (cuando exista tooling JS) se ejecutan sin fallos.
- **Tests**: `composer test` (PHPUnit) pasa con cobertura mínima acordada para servicios críticos.
- **Documentación**: Actualizar PRD, plan de trabajo, marketing y README cuando la funcionalidad lo requiera.
- **Compliance**: Registrar evidencias en `docs/reports/codecanyon-compliance.md`, mantener `docs/legal/origen-gpl.md` y licencias actualizadas.

## 2. Código / Arquitectura
- Código nuevo reside en `src/` bajo namespace `AvatarSteward\` siguiendo SOLID, KISS, DRY.
- Hooks, servicios y clases cuentan con pruebas unitarias y documentación en línea cuando aplique.
- No se introducen dependencias de producción; solo `require-dev` gestionado con Composer/NPM.

## 3. QA Manual
- Validación funcional en WordPress 6.8.3 (contenedor) cubriendo flujos: instalación, subida de avatares, modo low-bandwidth, migración desde Gravatar.
- Verificación visual básica (UI en inglés) y confirmación de que no se realizan llamadas a Gravatar.

## 4. Entregables
- Reportes actualizados en `docs/reports/linting/` y `docs/reports/tests/`.
- Changelog y documentación de usuario ajustados si cambian características.
- Evidencias para CodeCanyon (capturas, checklist) revisadas antes de merge a `master` o rama release.
