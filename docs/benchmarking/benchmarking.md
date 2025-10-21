# Benchmarking completo: plugins de avatar locales

Este documento compara Avatar Steward (este repositorio) con todos los plugins clonados en `benchmarking/`. La comparación se ha realizado leyendo los archivos principales (`README`, `main plugin file`, y archivos includes) en cada carpeta.

Competidores incluidos (carpetas en `benchmarking/`):

- `simple-local-avatars/` (10up)
- `wp-user-avatars/` (WP User Avatars)
- `Local-Avatars-by-Nocksoft/`
- `leira-letter-avatar/` (Leira Letter Avatar)
- `user-profile-picture/` (User Profile Picture / Metronet)


## Resumen rápido de cada competidor

- Simple Local Avatars (10up): plugin maduro, genera tamaños on-demand, opciones multisite (shared avatars), ratings, migraciones, WP-CLI, buena documentación.
- WP User Avatars: plugin popular con arquitectura basada en includes (admin, ajax, capabilities), soporte media library y hooks; versión en benchmarking incluye `includes/` con admin y ajax.
- Local Avatars by Nocksoft: plugin ligero, redirección de Gravatar a local avatar, configuración simple y media picker admin.
- Leira Letter Avatar: genera letter avatars (iniciales) para usuarios; arquitectura moderna con includes y activator/deactivator classes.
- User Profile Picture (Metronet/Cozmoslabs): plugin completo con admin UI, AJAX, REST support, bloque Gutenberg optional, opciones para image sizes y overrides.


## Matriz comparativa - características observadas (basada en código)

La tabla resume las características observadas en el código fuente de cada plugin comparado con Avatar Steward (este repo).

| Característica | Avatar Steward | Simple Local Avatars | WP User Avatars | Local Avatars (Nocksoft) | Leira Letter Avatar | User Profile Picture |
|---|---:|---:|---:|---:|---:|---:|
| Subida en perfil | Sí | Sí | Sí | Sí | No (genera iniciales) | Sí |
| Integration Media Library | Sí | Sí | Sí | Sí (media picker) | Parcial (uses WP APIs) | Sí |
| Guarda attachment ID en user meta | Sí | Sí | Sí | Sí | No (letter avatar generated) | Sí |
| Generación dinámica de tamaños | Parcial (usa WP sizes) | Sí (dynamic resize and save) | Parcial | No | No | Sí/Configurable |
| Validación de imagen (MIME/size/dim) | Sí | Parcial | Parcial | Parcial | N/A | Parcial |
| Permisos (quién puede subir) | Parcial (settings page) | Sí (caps option) | Sí (capabilities included) | Parcial | N/A | Sí (admin options) |
| Multisite / Shared avatars | Parcial | Sí (shared) | Parcial | No | No | Parcial |
| Moderación / ratings | No | Sí (ratings) | No | No | No | No |
| Migraciones desde otros plugins | Sí (MigrationService) | Sí | Parcial | No | No | Parcial |
| REST API / endpoints | Parcial/No explícito | Sí (rest fields) | Parcial (ajax) | No | No | Sí (rest_api_register present) |
| Hooks/filters extensibles | Sí | Sí | Sí | Parcial | Sí | Sí |
| Fallback a iniciales (SVG) | Sí (Initials Generator) | No | No | No | Sí (letter avatars) | No |
| Low-bandwidth optimizer (SVG fallback) | Sí | No | No | No | No | No |
| Limpieza meta al borrar attachment | Sí | Sí (uninstall clears) | Parcial | No | N/A | Sí/Parcial |
| WP-CLI | No evidente | Sí (migrate command) | No evidente | No | No | No |
| Tests/CI | Sí (tests dir) | Sí (CI upstream) | Parcial | No | Parcial | Parcial |
| Internacionalización (i18n) | Sí | Sí | Sí | Sí | Sí | Sí |


## Observaciones por plugin (evidencias rápidas)

- Avatar Steward: ver `src/AvatarSteward/Domain/Uploads/UploadService.php`, `src/AvatarSteward/Core/AvatarHandler.php`, `src/AvatarSteward/Domain/Initials/Generator.php`.
- Simple Local Avatars: ver `simple-local-avatars/includes/class-simple-local-avatars.php`, `README.md`.
- WP User Avatars: ver `benchmarking/wp-user-avatars/wp-user-avatars.php` y `includes/`.
- Local Avatars by Nocksoft: ver `local-avatars-by-nocksoft.php`, `php/settings.php`.
- Leira Letter Avatar: ver `leira-letter-avatar/leira-letter-avatar.php` y `includes/` (plugin genera letter avatars in-site).
- User Profile Picture: ver `user-profile-picture/metronet-profile-picture.php` y métodos para REST, AJAX y admin.


## Análisis competitivo global

- Fuerzas de Avatar Steward: arquitectura modular, validación robusta, fallback SVG por iniciales y optimizaciones para bajo ancho de banda; excelente punto de partida técnico para entornos profesionales.
- Debilidades frente a competidores maduros: falta de algunas opciones productivas (shared avatars multisite, rating/moderation workflow, generación dinámica de tamaños plenamente equivalente a SLA), y menor historial de adopción comparado con SLA.

### Recomendaciones de priorización (no ejecutar aún)
1. Shared avatars multisite (alta prioridad funcional)
2. Moderation workflow básico (avatar_status meta + admin UI)
3. Web API (REST) para integraciones y moderación remota
4. Resize on-demand y cache de versiones de imagen
5. WP-CLI commands para migración y limpieza


## Conclusión
Este benchmarking cubre todos los repos en `benchmarking/` y sintetiza evidencias extraídas del código. Avatar Steward parte con ventajas técnicas claras; para liderar en producto debe añadir las funcionalidades de gobernanza y multisite que los administradores esperan.

---

Si quieres, puedo:
- Exportar la tabla a CSV y añadirla al repo (`docs/benchmarking/matrix.csv`).
- Generar una matriz con detalles técnicos por hook/filtro/nombres de funciones (más trabajo de extracción).
- Preparar las tareas (issues o PR) para implementar la priorización sugerida.

Dime cuál prefieres.