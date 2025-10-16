# Registro de Origen GPL de `Avatar Steward`

## 1. Resumen

`Avatar Steward` reutiliza el código del plugin `Simple Local Avatars` publicado en WordPress.org para construir una solución ampliada de gestión de avatares. Este documento deja constancia del origen, obligaciones legales y plan de transformación profunda que garantiza un producto derivado legítimo y diferenciado.

## 2. Fuente original

- **Proyecto base:** [Simple Local Avatars](https://wordpress.org/plugins/simple-local-avatars/)
- **Repositorio de referencia:** https://github.com/10up/simple-local-avatars (confirmar versión exacta al importar)
- **Licencia:** GNU General Public License (GPL) versión 2 o posterior
- **Copyright:** 10up (y demás contribuidores listados)

## 3. Obligaciones GPL asumidas

- Mantener `LICENSE.txt` con el texto completo de la GPL y referencias al autor original.
- Preservar avisos de copyright dentro de los encabezados de archivos heredados.
- Declarar explícitamente que `Avatar Steward` es un trabajo derivado de `Simple Local Avatars`.
- Redistribuir el código (gratuito o de pago) únicamente bajo GPL, garantizando los mismos derechos a terceros.
- Documentar cambios significativos y proporcionar acceso al código fuente completo de las versiones distribuidas.

## 4. Componentes heredados y acciones de transformación

| Componente heredado (ref.) | Acción de refactor planificada | Estado |
| --- | --- | --- |
| `simple-local-avatars.php` (bootstrap) | Reescribir carga principal bajo espacio de nombres `AvatarSteward\Core`; eliminar dependencias directas globales. | Pendiente |
| Funciones de subida y asignación (`sla_functions.php`) | Encapsular en clases de servicios (`AvatarSteward\Upload\LocalAvatarService`), añadir validaciones extendidas (privacidad y rendimiento). | Pendiente |
| Hooks de administración (metabox, opciones) | Migrar a controladores modulares con UI renovada y Reactividad mínima (sin dependencias externas). | Pendiente |
| Textos y cadenas reutilizadas | Revisar traducciones, reemplazar branding e incluir referencias a la nueva documentación. | Pendiente |
| Sistema de capacidades | Actualizar prefijos a `avatar_steward_*` y documentar roles/capacidades en `docs/`. | Pendiente |

> **Nota:** Actualiza la columna “Estado” y detalla archivos adicionales a medida que avance el desarrollo.

## 5. Diferenciadores clave planeados

- Generador avanzado de avatares por iniciales con personalización cromática y reglas de contraste.
- Biblioteca local de avatares curados con control de licencias.
- Panel de moderación con auditoría y registro de decisiones.
- Integraciones opcionales con redes sociales para importar avatares bajo consentimiento.
- Pipeline de compliance (CodeCanyon, licencias de assets, pruebas automatizadas, demo reproducible).

## 6. Evidencias y seguimiento

- Registrar en `docs/reports/codecanyon-compliance.md` los hitos de refactor y verificación GPL.
- Mantener histórico de commits que muestren transformaciones estructurales respecto al código original.
- Adjuntar diffs relevantes (antes/después) cuando se sustituyan componentes heredados.
- Documentar en `CHANGELOG.md` las fases clave de reescritura y funcionalidades nuevas.
- Dejar constancia del entorno de ejecución (ej. `docker-compose.dev.yml`, `.env`) y cualquier script de migración utilizado durante las transformaciones.
- **2025-10-16:** Se verificó que `simple-local-avatars/simple-local-avatars.php` e `includes/class-simple-local-avatars.php` mantienen los encabezados GPL originales proporcionados por 10up sin modificaciones.

## 7. Contacto y revisión

- **Responsable legal/técnico:** Desarrollador principal (`Avatar Steward`)
- **Última revisión:** _(completar al finalizar cada iteración)_

Mantener este archivo actualizado es parte del Definition of Done para cada fase relacionada con el refactor del legado GPL.
