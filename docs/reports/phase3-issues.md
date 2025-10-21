## Phase 3 — Issues created (summary)

Creado el conjunto de issues para las tareas restantes de la Fase 3. Se listan a continuación con dependencias y sugerencias para paralelismo.

### Issues creados

- #68 — Tarea 3.4: Biblioteca de avatares — UI y selector
- #69 — Tarea 3.5: Opciones avanzadas — Restricciones por rol y tamaño
- #70 — Tarea 3.6: Pruebas de integración — Funciones Pro end-to-end
- #71 — Tarea 3.7: Política de soporte — Duración, canales y SLA
- #72 — Tarea 3.8: Documentar licencias de fuentes, iconos e imágenes
- #73 — Tarea 3.9: Pipeline de empaquetado y demo reproducible
- #74 — Tarea 3.10: Auto-borrado de avatares inactivos con notificaciones
- #75 — Tarea 3.11: Módulo de auditoría exportable
- #76 — Tarea 3.12: Sellos de verificación y plantillas sectoriales
- #77 — Tarea 3.13: API de identidad visual (paletas y estilos)

### Dependencias (grafo simplificado)

- 3.1, 3.2, 3.3: completadas (marcadas en `documentacion/04_Plan_de_Trabajo.md`).
- 3.4 (biblioteca): base para 3.12 (sellos) y usada por 3.9 (packaging).
- 3.5 (opciones avanzadas): puede ejecutarse en paralelo, pero requiere activación Pro (3.1) para liberar características.
- 3.6 (tests integración): depende de 3.1, 3.2, 3.3, 3.4 y 3.11 para escenarios completos.
- 3.7 (soporte): independiente (puede realizarse en paralelo).
- 3.8 (licencias): requisito para 3.4 y 3.9 (packaging) — alta prioridad.
- 3.9 (packaging/demo): depende de 3.8 y 3.4 para paquete final.
- 3.10 (auto-borrado): independiente funcionalmente; coordinar con 3.5 si se aplican reglas por rol.
- 3.11 (auditoría): recomendado antes de 3.6 (pruebas) y integrado con 3.2 (moderación).
- 3.12 (sellos/plantillas): depende de 3.4 y 3.8.
- 3.13 (API identidad visual): puede desarrollarse en paralelo; útil para 3.4 y frontend.

### Sugerencias de paralelismo y asignación

- Priorizar 3.8 (licencias) temprano — bloqueador para empaquetado y uso de assets.
- Ejecutar 3.4 (biblioteca), 3.5 (opciones) y 3.13 (API visual) en paralelo: tareas de frontend/back-end separables.
- Implementar 3.11 (auditoría) antes de lanzar 3.6 (tests de integración) para disponer de eventos a testear.
- 3.7 (política de soporte) puede ser trabajada por Product/Comercial en paralelo con desarrollo.
- 3.9 (packaging) y 3.6 (tests) se realizan en la fase final de la iteración.

### Próximos pasos realizados por este commit

- Marcadas 3.1–3.3 como completadas en `documentacion/04_Plan_de_Trabajo.md`.
- Issues 68–77 creados en GitHub (correspondientes a 3.4–3.13).
- Este archivo resumen añadido en `docs/reports/phase3-issues.md`.

---

Firma: Automatización local (acción vía API GitHub) — revisa los issues para asignaciones y prioridades.
