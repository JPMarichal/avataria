# CodeCanyon / Envato — Checklist de Preparación y Requisitos de Calidad

Este documento adapta las directrices de Envato para que el proyecto "Avatar Steward" cumpla los requisitos de calidad necesarios para la revisión y publicación en CodeCanyon.

## 1. Documentación mínima requerida

- `README.md`: Requisitos del sistema (WP, PHP), instalación (incluyendo Docker), configuración y uso básico.
- `CHANGELOG.md`: Histórico de versiones con formato claro.
- `docs/`: Manuales de uso (moderación, import social, licencias), ejemplos y FAQ.
- `assets/`: Capturas y video demo (calidad alta) que muestren los flujos críticos.

## 2. Calidad de código y packaging

- Código legible y sin ofuscación; sigue WordPress Coding Standards.
- Añadir `phpcs.xml` y/o instrucciones para ejecutar `PHP_CodeSniffer`.
- No incluir dependencias dev (ej. `node_modules`) en el paquete final.
- Estructura de paquete ZIP: `plugin/`, `assets/`, `docs/`, `examples/`.

## 3. Seguridad

- Validar y sanear todas las subidas (`wp_check_filetype`, `wp_handle_upload`, nonces, capabilities).
- Evitar ejecución remota de código o inclusión de librerías no verificadas.
- Realizar un chequeo rápido SAST antes de empaquetar.

## 4. Compatibilidad y pruebas

- Declarar compatibilidad: WordPress >= 5.8, PHP >= 7.4.
- Incluir tests unitarios mínimo (PHPUnit) para lógica crítica.
- Proveer instrucciones para levantar demo con `docker-compose` (sin claves privadas).

## 5. Licencias y assets

- Documentar licencias de fuentes, iconos, imágenes y librerías incluidas.
- Si usas terceros, aportar evidencia de permiso/uso o usar alternativas con licencia compatible.

## 6. Soporte y políticas

- Incluir política de soporte (duración, canales, límites).
- Declarar cómo recibir actualizaciones (changelog & versioning).

## 7. Demo y preview

- Proveer demo en vivo o `docker-compose.demo.yml` que el revisor pueda levantar.
- Incluir screenshots que muestren: subida, avatar en front, generador de iniciales y ajustes admin.

## 8. Checklist pre-subida

- [ ] README, CHANGELOG y docs incluidos.
- [ ] Capturas / video demo incluidos en `assets/`.
- [ ] `phpcs.xml` y comandos para linting documentados.
- [ ] Tests unitarios (PHPUnit) y comandos para ejecutar tests documentados.
- [ ] Demo reproducible con Docker (sin keys privadas).
- [ ] Paquete ZIP limpio y probado (sin archivos dev).
- [ ] Licencias de assets documentadas.
- [ ] Política de soporte incluida.

---

Guía oficial y recursos: https://help.author.envato.com/hc/en-us | https://codecanyon.net/licenses
