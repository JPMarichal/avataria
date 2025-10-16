# Entregables y Criterios de Aceptación por Fase

Este documento centraliza, de forma concisa y accionable, los entregables y criterios de aceptación (Definition of Done) para cada fase del proyecto "Avatar Pro". Sirve como checklist operativo para desarrolladores, QA y Product Owner.

## Fase 1 — Planificación y Diseño (1 semana)

Entregables

- `documentacion/arquitectura.md` (diagramas y clases principales).
- `documentacion/mvp-spec.json` (spec machine-readable con settings y hooks públicos).
- Mockups UI (admin y perfil) en `design/`.
- `docker-compose.dev.yml` (esqueleto reproducible en dev).
- Backlog inicial en `documentacion/backlog-mvp.md` (issues/epics priorizados).
- `docs/licensing.md` inicial (plantilla para licencias) y definición de política de soporte en `docs/soporte.md`.
- Checklist de cumplimiento inicial basada en `09_CodeCanyon_Checklist.md` con responsables asignados.

Criterios de Aceptación

- Artefactos subidos al repo y revisados (PRs) con comentarios del PO resueltos.
- `mvp-spec.json` incluye: features RF-G01..RF-G05, settings (tipo+default), hooks, criterios de aceptación.
- `docker-compose.dev.yml` arranca contenedores básicos en Windows 11 (smoke test).
- Documentación mínima (`README.md`, `CHANGELOG.md`, estructura de `docs/`) creada y referenciada en el repo.
- Plan de licencias y soporte documentado cumpliendo la checklist de CodeCanyon.

Responsables: Product Owner (validación de requisitos), Lead Dev (arquitectura), Designer (mockups).

## Fase 2 — Desarrollo MVP (3 semanas)

Entregables

- Código del plugin (rama `feature/mvp`) con: subida de avatar, reemplazo de `get_avatar()`, integración de perfil, generador de iniciales, avatar por defecto.
- Página de ajustes admin con settings documentados.
- Validación y sanitización de uploads implementada.
- Pruebas: PHPUnit unitarias (generador de iniciales) y tests de integración básicos.
- `README.md` con instalación (Docker) y guía rápida.
- CI básico (lint + tests) en archivo de pipeline.
- `docs/reports/linting/` y `docs/reports/tests/` con resultados adjuntos en PRs.
- Assets preliminares (capturas, guion de video) en `assets/` con licencias registradas en `docs/licensing.md`.
- Registro de cumplimiento del MVP actualizado en `docs/reports/codecanyon-compliance.md`.

Criterios de Aceptación

- Desde el perfil se puede subir una imagen y ésta aparece en front donde se invoca `get_avatar()`.
- No se realizan llamadas a Gravatar en una instalación típica (verificación de red).
- Subidas inválidas son rechazadas con mensaje claro; logs para admins.
- Tests unitarios pasan y cobertura mínima para lógica crítica.
- Overhead medido < 50 ms en prueba de referencia o mitigación documentada.
- Evidencia de linting, tests, demo Docker y documentación disponible para revisor (cumplimiento checklist CodeCanyon) vinculada en el PR final del MVP.

Sign-off: QA (verificación checklist), Product Owner (acepta funcionalidad), Lead Dev (revisión de código).

## Fase 3 — Desarrollo Versión Pro (4 semanas)

Entregables

- Biblioteca de avatares y selector UI.
- Integración social (esqueletos OAuth y flujo de importación).
- Panel de moderación (approve/reject) y registros de auditoría.
- Soporte para múltiples avatares por usuario (UI + backend).
- Controles avanzados: límites por rol, tamaño/dimensiones.
- Paquete listo para CodeCanyon (assets, documentación, video demo checklist).
- `docs/reports/security-scan.md` con resultados de SAST antes del empaquetado.
- `docker-compose.demo.yml` operativo con instrucciones en `docs/demo/README.md`.

Criterios de Aceptación

- Workflows end-to-end (import social, moderación) funcionan en staging.
- Control por roles y restricciones operan como configurado.
- Paquete Pro cumple requisitos de CodeCanyon.
- Licencias actualizadas y verificadas; evidencias de demo y materiales de marketing listos para revisión.

Sign-off: Product Owner, Lead Dev, Responsable Comercial (revisión del paquete).

## Fase 4 — Pruebas y Lanzamiento (2 semanas)

Entregables

- Pruebas de compatibilidad y resultados documentados.
- Paquetes finales (WP.org y CodeCanyon) listos.
- `docker-compose.prod.yml` y playbook de despliegue para Ionos/Ubuntu.
- Documentación final y plan de rollback.
- Materiales de marketing y checklist de publicación.
- Evidencia final de checklist `09_CodeCanyon_Checklist.md` firmada por PO, QA y Responsable Comercial.

Criterios de Aceptación

- Tests de regresión y performance pasan (umbral acordado).
- Despliegue en servidor Ubuntu (Ionos) exitoso en smoke test.
- Paquetes verificados y firmados para publicación.

Sign-off final: Product Owner y Responsable de Operaciones.

## Plantilla rápida de verificación por entregable (usar en PRs)

- [ ] ¿Incluye tests unitarios o justificativo si no aplican?
- [ ] ¿Documentación actualizada (`README`, `CHANGELOG`)?
- [ ] ¿Pipeline CI con lint/tests actualizado?
- [ ] ¿Se ejecutó el smoke test en `docker-compose.dev.yml`?
- [ ] ¿PO/QA han aprobado la entrega?
- [ ] ¿Se adjuntaron evidencias de cumplimiento CodeCanyon pertinentes?

---

Archivo mantenido en el repo y referenciado desde el PRD, Plan de Trabajo y Metodología.

## Preparación para publicación en CodeCanyon

Antes de crear el paquete final para CodeCanyon, completa la checklist en `09_CodeCanyon_Checklist.md`. Esta checklist cubre documentación, packaging, seguridad, licencias y demo reproducible.
