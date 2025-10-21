# Benchmarking de plugins de avatar

Este documento compara Avatar Steward (este repositorio) con tres competidores descargados en `benchmarking/`: Simple Local Avatars (10up), WP User Avatars, y Local Avatars by Nocksoft. La comparación está basada en lectura directa del código fuente y README dentro de cada carpeta del directorio `benchmarking`.

## Resumen ejecutivo

- Avatar Steward: diseño modular (UploadService, AvatarHandler, Initials Generator, Bandwidth Optimizer), soporte de avatars locales en media library, fallback SVG por iniciales y optimización low-bandwidth.
- Simple Local Avatars (10up): solución madura, ampliamente usada; genera tamaños on-demand, soporte multisite con "shared avatars", ratings y opciones de administrador maduras.
- WP User Avatars: plugin popular que permite subir y gestionar avatares; arquitectura de hooks y includes (admin, ajax, rest de facto en archivos). 
- Local Avatars by Nocksoft: ligero, con opción de redirigir Gravatar a local avatar y controles en la pantalla de usuario.

## Tabla comparativa (características clave)

| Característica | Avatar Steward | Simple Local Avatars | WP User Avatars | Local Avatars (Nocksoft) |
|---|---:|---:|---:|---:|
| Subida en perfil | Sí (ProfileFieldsRenderer + UploadHandler) | Sí | Sí | Sí |
| Integration Media Library | Sí (wp_handle_upload, wp_insert_attachment) | Sí | Sí | Parcial (usa media picker) |
| Guarda attachment ID en user meta | Sí (`avatar_steward_avatar`) | Sí (`simple_local_avatar` + sizes) | Sí | Sí |
| Generación dinámica de tamaños | Parcial (usa WP sizes) | Sí (resize y guarda versiones) | Parcial | No |
| Validación de imagen (MIME/size/dim) | Sí (UploadService) | Parcial/Sí | Parcial | Parcial |
| Permisos granulares (quién puede subir) | Parcial (settings page presente) | Sí (caps option) | Sí (capabilities.php incluido) | Parcial |
| Multisite / Shared avatars | Parcial (migration support) | Sí (shared network avatars) | Parcial | No |
| Moderación / ratings | No | Sí (avatar ratings) | No/Parcial | No |
| Migraciones desde otros plugins | Sí (MigrationService) | Sí (migrate WP User Avatar) | Parcial | No |
| REST API / campos REST | Parcial/No explícito | Sí (registers rest fields) | Parcial (ajax endpoints) | No |
| Hooks/filters para desarrolladores | Sí | Sí | Sí | Parcial |
| Fallback: iniciales SVG | Sí | No | No | No |
| Low-bandwidth optimizer | Sí | No | No | No |
| Limpieza de meta al borrar attachment | Sí | Sí (uninstall cleans) | Parcial | No explícito |
| WP-CLI | No evidente | Sí (WP-CLI migrate) | No evidente | No |
| Tests/CI | Sí (tests dir) | Sí (CI en upstream) | Parcial | No |


## Matrices de evidencia (capturas de código/referencias)

- Avatar Steward: ver `src/AvatarSteward/Domain/Uploads/UploadService.php`, `src/AvatarSteward/Core/AvatarHandler.php`, `src/AvatarSteward/Domain/Initials/Generator.php`, `src/AvatarSteward/Domain/LowBandwidth/BandwidthOptimizer.php`.
- Simple Local Avatars: ver `simple-local-avatars/includes/class-simple-local-avatars.php`, `simple-local-avatars/README.md`.
- WP User Avatars: ver `benchmarking/wp-user-avatars/wp-user-avatars.php` y archivos en `wp-user-avatars/wp-user-avatars/` (includes/).
- Local Avatars by Nocksoft: ver `benchmarking/Local-Avatars-by-Nocksoft/local-avatars-by-nocksoft.php` y `php/settings.php`.

## Análisis: quién gana en cada dimensión

- Seguridad / Validación de archivos: Avatar Steward (UploadService con MIME whitelist, límites de tamaño/dimensiones y logging).
- Rendimiento / low-bandwidth: Avatar Steward (BandwidthOptimizer + SVG data URIs).
- Madurez y adopción: Simple Local Avatars (10up) — mayor integraciones, opciones multisite y features (ratings, migraciones, WP-CLI).
- Flexibilidad / extensibilidad para desarrolladores: Empate entre Avatar Steward y Simple Local Avatars (ambos exponen filtros/hooks); WP User Avatars también ofrece hooks.
- Experiencia admin / UI: Simple Local Avatars presenta muchas opciones nativas y network settings; Avatar Steward tiene SettingsPage pero menos opciones visibles aún.

## ¿En qué ganas? (ventajas competitivas de Avatar Steward)
1. Arquitectura moderna y testable (namespaces, strict types, servicios). 
2. Initials SVG fallback que reduce dependencias externas y mejora latencia inicial.
3. Low-bandwidth optimization que puede mejorar la experiencia móvil y reducir consumo de CDN.
4. Validación y logging en UploadService que facilita auditoría y diagnósticos.

## ¿En qué pierdes? (límits respecto a rivales)
1. Madurez / Feature completeness: SLA tiene features probados en producción (shared avatars, ratings, WP-CLI). 
2. Moderación integrada: SLA ofrece ratings; Avatar Steward no tiene panel de moderación ni workflow de aprobación.
3. Generación dinámica y cache de tamaños: SLA implementa resize on-demand; Avatar Steward depende de WP image sizes por defecto.
4. Integración multisite compartida/ajustes de red: falta opción clara de "shared network avatars".

## Recomendaciones tácticas: roadmap para quedar a la cabeza
Prioridad alta (rápido impacto):
- Implementar "Shared network avatars" en settings y lógica para multisite (usar switch_to_blog() donde haga falta).
- Añadir pipeline de moderación mínima: meta `avatar_status` (pending/approved/rejected), UI admin para moderadores y hooks para integrarlo con flujos.
- Exponer endpoints REST seguros para upload/approval/listado de avatares (namespace `avatar-steward/v1`).

Prioridad media:
- Implementar generación dinámica de tamaños (resize on-demand y guardar versión) o integrarse con un servicio de resize en la nube.
- Añadir WP-CLI commands para migración en lote y limpieza.

Prioridad baja:
- Integración opcional con servicios de detección de contenido inapropiado y añadir reglas automáticas para marca/pendiente.
- Documentación pública comparativa y guías de migración completas.

## Conclusión
Avatar Steward está técnicamente bien posicionada para competir en sitios profesionales por su arquitectura, optimizaciones y enfoque en rendimiento y controles. Para alcanzar y superar a soluciones maduras como Simple Local Avatars, la prioridad está en madurar características de producto (multisite compartido, moderación, generación dinámica de tamaños y comandos de migración). Implementando las sugerencias de alta prioridad en 1–2 sprints (según equipo), podrías convertir las ventajas técnicas en ventajas de producto que atraigan a administradores de sitios profesionales.

---

Si quieres, ahora puedo:
- 1) Añadir la matriz en formato CSV o tabla adicional con columnas más técnicas (hooks expuestos, nombres de filtros, rutas de archivo). 
- 2) Implementar la opción `shared avatars` y un endpoint REST básico en código (crear PR). 

Dime qué prefieres que haga a continuación.