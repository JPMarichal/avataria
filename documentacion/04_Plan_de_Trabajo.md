# Plan de Trabajo y Entregables por Fase - "Avatar Steward"

**Duración Total Estimada: 10 Semanas**

---

Este documento integra el cronograma macro y la checklist de entregables para asegurar el cumplimiento continuo de `08_CodeCanyon_Checklist.md` desde el MVP.

## Fase 1: Planificación y Diseño (1 Semana)

- [x] Tarea 1.1: Configurar el entorno de desarrollo local y el repositorio Git.
- [x] Tarea 1.2: Diseñar el mockup de la interfaz de administración (pestaña de opciones, panel de moderación).
- [x] Tarea 1.3: Definir la arquitectura del código (clases, hooks, estructura de archivos).
- [x] Tarea 1.4: Establecer la estructura inicial de documentación (`README.md`, `CHANGELOG.md`, directorio `docs/`, checklist de soporte) alineada con `08_CodeCanyon_Checklist.md`.
- [x] Tarea 1.5: Configurar herramientas de calidad obligatorias (`phpcs.xml`, PHPUnit, ESLint) y documentar su ejecución en el repositorio.
- [x] Tarea 1.6: Especificar la estructura del paquete (`plugin/`, `assets/`, `docs/`, `examples/`) y registrar políticas de licencias y soporte preliminares.
- [x] Tarea 1.7: Preparar `LICENSE.txt`, documentar el origen GPL y planificar el refactor de namespaces/prefijos heredados para evitar conflictos con Simple Local Avatars.

### Entregables

- `documentacion/arquitectura.md` con diagramas y clases principales.
- `documentacion/mvp-spec.json` con settings, hooks públicos y criterios de aceptación iniciales.
- Mockups UI (admin y perfil) en `design/`.
- `docker-compose.dev.yml` funcional en Windows 11.
- Backlog inicial en `documentacion/backlog-mvp.md`.
- `docs/licensing.md` inicial y política de soporte en `docs/soporte.md`.
- Checklist de cumplimiento inicial derivada de `08_CodeCanyon_Checklist.md` con responsables y fechas.
- `docs/legal/origen-gpl.md` con registro de componentes heredados y acciones de refactor.

### Criterios de aceptación

- Todos los artefactos creados y revisados (PRs o revisión propia) sin pendientes.
- `mvp-spec.json` cubre RF-G01..RF-G05 con defaults y hooks.
- `docker-compose.dev.yml` supera smoke test.
- Documentación mínima (`README.md`, `CHANGELOG.md`, estructura `docs/`) accesible en el repo.
- Plan de licencias y soporte documentado conforme a CodeCanyon.
- Registro inicial en `docs/reports/codecanyon-compliance.md` con acciones planificadas.
- `LICENSE.txt` actualizado y evidencias del aislamiento de namespaces frente al plugin original.

- **Responsable principal:** Desarrollador.

## Fase 2: Desarrollo del MVP - Versión Gratuita (3 Semanas)

- [x] Tarea 2.1: Desarrollar la funcionalidad de subida de imagen en el perfil de usuario.
- [x] Tarea 2.2: Implementar la lógica para sobreescribir la función `get_avatar()`.
- [x] Tarea 2.3: Desarrollar el generador de avatares por iniciales.
- [x] Tarea 2.4: Crear la página de opciones básicas del plugin.
- [x] Tarea 2.5: Realizar pruebas internas y depuración.
- [x] Tarea 2.6: Mantener actualizados `README.md`, `CHANGELOG.md` y la documentación de usuario con instalación, configuración y uso del MVP.
- [x] Tarea 2.7: Ejecutar linting (`phpcs`, ESLint) y tests automatizados (PHPUnit) como parte del Definition of Done del MVP.
- [x] Tarea 2.7.1: Implementar modo "bajo ancho de banda" (iniciales/SVG) y documentar métricas de performance.
- [x] Tarea 2.8: Preparar assets preliminares (capturas y guion de video demo) y validar licencias de recursos utilizados.
- [x] Tarea 2.9: Completar la revisión intermedia de `08_CodeCanyon_Checklist.md`, registrando gaps y acciones en `docs/`.
- [x] Tarea 2.10: Documentar en `docs/legal/origen-gpl.md` los módulos refactorizados, diffs relevantes y evidencias de cumplimiento GPL.
- [x] Tarea 2.11: Entregar asistente de migración desde Gravatar/WP User Avatar con pruebas de regresión.

### Entregables

- Código del plugin en rama `feature/mvp` con funcionalidades core.
- Página de ajustes admin con settings documentados.
- Validación y sanitización de uploads implementada.
- Pruebas PHPUnit (generador de iniciales) y tests de integración básicos.
- `README.md` con instalación vía Docker y guía rápida.
- Pipeline de CI con lint/tests configurado.
- Reportes en `docs/reports/linting/` y `docs/reports/tests/` adjuntos a PRs.
- Assets preliminares (capturas, guion de video) en `assets/` con licencias en `docs/licensing.md`.
- Registro de cumplimiento del MVP en `docs/reports/codecanyon-compliance.md`.
- Validación de modo "bajo ancho de banda" documentada con pruebas de performance.
- Guía de migración (Gravatar / plugins legacy) en `docs/migracion/`.

### Criterios de aceptación

- `get_avatar()` muestra el avatar cargado en todos los puntos de uso.
- No se realizan llamadas a Gravatar en escenarios típicos.
- Subidas inválidas son rechazadas con mensajes claros y logging para admin.
- Tests automatizados pasan y cobertura mínima para lógica crítica alcanzada.
- Overhead < 50 ms o mitigación documentada.
- Evidencias de linting, tests, demo Docker y documentación disponibles para revisión (checklist CodeCanyon).

- **Responsable principal:** Desarrollador.

## Fase 3: Desarrollo de la Versión Pro (4 Semanas)

- [ ] Tarea 3.1: Implementar el sistema de licenciamiento y activación de la versión Pro.
- [ ] Tarea 3.2: Desarrollar el panel de moderación de avatares.
- [ ] Tarea 3.3: Integrar las APIs sociales para la importación de avatares.
- [ ] Tarea 3.4: Desarrollar la funcionalidad de la biblioteca de avatares.
- [ ] Tarea 3.5: Añadir las opciones avanzadas (restricciones por rol, tamaño, etc.).
- [ ] Tarea 3.6: Realizar pruebas de integración de todas las funciones Pro.
- [ ] Tarea 3.7: Consolidar la política de soporte (duración, canales, SLA) y documentarla en `docs/`.
- [ ] Tarea 3.8: Documentar licencias de fuentes, iconos, imágenes y librerías incluidas en el paquete Pro.
- [ ] Tarea 3.9: Preparar el pipeline de empaquetado (`zip` limpio sin dependencias dev) y validar demo reproducible (`docker-compose.demo.yml`).
- [ ] Tarea 3.10: Implementar auto-borrado de avatares inactivos con notificaciones configurables.
- [ ] Tarea 3.11: Desarrollar módulo de auditoría exportable (logs de acceso/modificación).
- [ ] Tarea 3.12: Configurar sellos de verificación y plantillas sectoriales para la biblioteca.
- [ ] Tarea 3.13: Añadir API de identidad visual (paletas, estilos) y documentarla en `docs/api/`.

### Entregables

- Biblioteca de avatares y selector UI funcional.
- Integraciones sociales (esqueletos OAuth y flujo de importación).
- Panel de moderación con registros de auditoría.
- Soporte para múltiples avatares por usuario.
- Controles avanzados por rol y restricciones de tamaño.
- Paquete Pro listo para CodeCanyon (assets, documentación, video demo).
- `docs/reports/security-scan.md` con resultados de SAST.
- `docker-compose.demo.yml` operativo con instrucciones en `docs/demo/README.md`.

### Criterios de aceptación

- Workflows end-to-end (import social, moderación) operativos en staging.
- Controles por rol y restricciones aplican según configuración.
- Paquete Pro cumple la checklist CodeCanyon.
- Licencias actualizadas y verificadas; evidencias de demo y materiales de marketing listos para revisión.
- SAST sin hallazgos críticos abiertos.

- **Responsable principal:** Desarrollador (auto revisión) con checklist firmada.

## Fase 4: Pruebas y Lanzamiento (2 Semanas)

- [ ] Tarea 4.1: Ejecutar pruebas de compatibilidad (temas y plugins clave) y performance documentando resultados en `docs/qa/`.
- [ ] Tarea 4.2: Completar la checklist final de `08_CodeCanyon_Checklist.md` y adjuntar evidencia en `docs/reports/`.
- [ ] Tarea 4.3: Generar paquetes finales (`plugin.zip`, `docs/`, `assets/`) y verificar ausencia de dependencias de desarrollo.
- [ ] Tarea 4.4: Validar demo reproducible (Docker) y entregar instrucciones de acceso al revisor.
- [ ] Tarea 4.5: Revisar licencias, soporte y changelog definitivo con Product Owner y Responsable Comercial.
- [ ] Tarea 4.6: Ejecutar el plan de marketing de lanzamiento.
- [ ] Tarea 4.7: Actualizar `docs/legal/origen-gpl.md` y `CHANGELOG.md` con el resumen final de transformaciones respecto a Simple Local Avatars.

## Requisitos de Publicación

Antes de preparar el paquete para CodeCanyon, revisa `08_CodeCanyon_Checklist.md` y asegura que todos los puntos de la checklist pre-subida están completados en cada entrega incremental.

### Entregables

- Resultados de pruebas de compatibilidad y performance documentados.
- Paquetes finales (WP.org y CodeCanyon) listos y verificados.
- `docker-compose.prod.yml` y playbook de despliegue para Ionos/Ubuntu.
- Documentación final, plan de rollback y materiales de marketing.
- Evidencia final firmada en `docs/reports/codecanyon-compliance.md`.
- `docs/legal/origen-gpl.md` actualizado con el estado final del refactor y enlaces a commits clave.

### Criterios de aceptación

- Tests de regresión y performance dentro del umbral acordado.
- Smoke test exitoso en servidor Ubuntu (Ionos).
- Paquetes finales sin dependencias dev ni archivos residuales.
- Demo reproducible validada con instrucciones claras para revisor.
- Documentación, licencias y soporte listos para publicarse.

- **Responsable principal:** Desarrollador.

---

## Documentos Relacionados

Para una comprensión completa del proyecto, consulta los siguientes documentos:

- [Documento de Requerimientos del Producto](01_Documento_Requerimientos_Producto.md): Define los requerimientos funcionales.
- [Estrategia de Negocio](02_Estrategia_de_Negocio.md): Detalla el modelo de negocio.
- [Estrategia de Marketing](03_Estrategia_de_Marketing.md): Describe las fases de marketing.
- [Stack Tecnológico](05_Stack_Tecnologico.md): Especifica las tecnologías y entornos.
- [Guía de Desarrollo](06_Guia_de_Desarrollo.md): Define principios de desarrollo.
- [Metodología de Desarrollo](07_Metodologia_de_Desarrollo.md): Cubre el flujo de trabajo Agile y Git.
- [CodeCanyon Checklist](08_CodeCanyon_Checklist.md): Requisitos de calidad y packaging para la publicación en CodeCanyon.