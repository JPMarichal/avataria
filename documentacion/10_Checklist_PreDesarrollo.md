# Checklist Pre-Desarrollo y Go/No-Go

Completa esta lista antes de iniciar cualquier implementación en `src/`.

## 1. Preparación del repositorio
- [x] Verificar que `simple-local-avatars/` está actualizado (último commit de referencia) y documentar la versión en `docs/legal/origen-gpl.md`.
- [x] Confirmar que la rama de trabajo se crea desde `master` (o la rama acordada) y que el historial está limpio (`git status`).
- [x] Dejar claro que el directorio `simple-local-avatars/` es referencia intocable; todo desarrollo debe realizarse en `src/`.
- [x] Asegurar que `LICENSE.txt` contiene la GPL y menciona a `Simple Local Avatars`.

## 2. Entorno local
- [ ] Ejecutar `docker-compose.dev.yml` o `wp-env` y validar que WordPress inicia sin errores.
- [ ] Instalar dependencias de desarrollo: `composer install` y `npm install` (solo si ya existen los manifiestos).
- [ ] Configurar variables de entorno necesarias (`.env`, `.wp-env.json`) y documentarlas en `README.md`.

## 3. Estructura del proyecto
- [ ] Confirmar que el código nuevo se ubicará en `src/` bajo namespaces `AvatarSteward\*`.
- [ ] Revisar `documentacion/06_Guia_de_Desarrollo.md` para respetar la arquitectura modular y los principios SOLID definidos.
- [ ] Asegurar que los directorios `docs/`, `assets/`, `docs/reports/`, `docs/migracion/` existen (crear placeholders vacíos si no).

## 4. Obligaciones GPL y compliance
- [ ] Actualizar `docs/legal/origen-gpl.md` con los componentes a refactorizar en la iteración.
- [ ] Registrar en `docs/reports/codecanyon-compliance.md` los puntos de la checklist que se revisarán durante el sprint.
- [ ] Verificar que los avisos de copyright originales permanecen en los archivos heredados.

## 5. Instrumentación y QA
- [ ] Definir los tests mínimos (PHPUnit/Playwright) para la funcionalidad a desarrollar y anotarlos en el PRD (`documentacion/01_Requerimiento_Producto.md`).
- [ ] Configurar scripts de linting y pruebas (`composer lint`, `composer test`, `npm run lint`) en la herramienta CI elegida.
- [ ] Preparar plantillas de reportes (`docs/reports/linting/README.md`, `docs/reports/tests/README.md`) si aún no existen.

## 6. Alineación con stakeholders
- [ ] Validar con el Product Owner las prioridades del sprint según `documentacion/09_Roadmap.md` y `documentacion/04_Plan_de_Trabajo.md`.
- [ ] Confirmar mensajes clave en inglés para materiales públicos y confirmar que las traducciones internas seguirán en español.
- [ ] Acordar criterios de Definition of Done específicos para la iteración y documentarlos en la tarea correspondiente (issue/Jira/Trello).

## 7. Decisión final
- [ ] ¿Todos los puntos anteriores están marcados como completados?
  - [ ] **Sí** → Proceder con el desarrollo.
  - [ ] **No** → Registrar bloqueos y reprogramar el inicio.
