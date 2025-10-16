# Checklist Pre-Desarrollo y Go/No-Go

Completa esta lista antes de iniciar cualquier implementación en `src/`.

## 1. Preparación del repositorio
- [x] Verificar que `simple-local-avatars/` está actualizado (último commit de referencia) y documentar la versión en `docs/legal/origen-gpl.md`.
- [x] Confirmar que la rama de trabajo se crea desde `master` (o la rama acordada) y que el historial está limpio (`git status`).
- [x] Dejar claro que el directorio `simple-local-avatars/` es referencia intocable; todo desarrollo debe realizarse en `src/`.
- [x] Asegurar que `LICENSE.txt` contiene la GPL y menciona a `Simple Local Avatars`.

## 2. Entorno local
- [x] Ejecutar `docker-compose.dev.yml` o `wp-env` y validar que WordPress inicia sin errores.
- [x] Instalar dependencias de desarrollo: `composer install` y `npm install` (solo si ya existen los manifiestos).
- [x] Configurar variables de entorno necesarias (`.env`, `.wp-env.json`) y documentarlas en `README.md`.

## 3. Estructura del proyecto
- [x] Confirmar que el código nuevo se ubicará en `src/` bajo namespaces `AvatarSteward\*`.
- [x] Revisar `documentacion/06_Guia_de_Desarrollo.md` para respetar la arquitectura modular y los principios SOLID definidos.
- [x] Asegurar que los directorios `docs/`, `assets/`, `docs/reports/`, `docs/migracion/` existen (crear placeholders vacíos si no).

## 4. Obligaciones GPL y compliance
- [x] Actualizar `docs/legal/origen-gpl.md` con los componentes a refactorizar en la iteración.
- [x] Registrar en `docs/reports/codecanyon-compliance.md` los puntos de la checklist que se revisarán durante el sprint.
- [x] Verificar que los avisos GPL originales permanecen en `simple-local-avatars/simple-local-avatars.php` e `includes/class-simple-local-avatars.php`.

## 5. Instrumentación y QA
- [x] Definir los tests mínimos (PHPUnit/Playwright) para la funcionalidad a desarrollar y anotarlos en el PRD (`documentacion/01_Requerimiento_Producto.md`).
- [x] Configurar scripts de linting y pruebas (`composer lint`, `composer test`, `npm run lint`) en la herramienta CI elegida (ver `composer.json` y `package.json`).
- [x] Preparar plantillas de reportes (`docs/reports/linting/README.md`, `docs/reports/tests/README.md`).

## 6. Alineación con stakeholders
- [x] Validar con el Product Owner las prioridades del sprint según `documentacion/09_Roadmap.md` y `documentacion/04_Plan_de_Trabajo.md`.
- [x] Confirmar mensajes clave en inglés para materiales públicos y confirmar que las traducciones internas seguirán en español.
- [x] Acordar criterios de Definition of Done en `documentacion/11_Definition_of_Done.md`.

## 7. Decisión final
- [ ] ¿Todos los puntos anteriores están marcados como completados?
  - [ ] **Sí** → Proceder con el desarrollo.
  - [ ] **No** → Registrar bloqueos y reprogramar el inicio.
