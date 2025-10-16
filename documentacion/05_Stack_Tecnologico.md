# Stack Tecnológico, Dependencias y Requerimientos - "Avatar Steward"

* **Proyecto:** Avatar Steward (Provisional)
* **Versión:** 1.0
* **Fecha:** 14 de Octubre de 2025

---

## 1. Stack Tecnológico 💻

Este documento define el conjunto de tecnologías y herramientas que se utilizarán para el desarrollo, construcción y despliegue del plugin "Avatar Steward".

### **1.1. Backend**
* **Lenguaje Principal:** **PHP >= 7.4**. Se utilizará la sintaxis moderna de PHP, incluyendo tipado estricto (`declare(strict_types=1);`), clases anónimas y funciones de flecha donde sea apropiado.
* **Base de Datos:** **MySQL >= 5.7** / **MariaDB >= 10.3**. Se interactuará exclusivamente a través de la API de WordPress (`$wpdb`) para garantizar la máxima compatibilidad.
* **Servidor Web:** Se garantizará la compatibilidad con **Apache** (con `mod_rewrite`) y **Nginx**.

### **1.2. Frontend**
* **JavaScript:** **Vanilla JavaScript (ES6+)**. Para mantener el plugin ligero y sin dependencias innecesarias, no se usarán frameworks como React o Vue en el frontend del plugin. Todo el código JS será modular y seguirá las mejores prácticas modernas.
* **CSS:** **SASS (SCSS)**. Se utilizará SASS para escribir CSS de forma modular, anidada y mantenible. Se compilará a CSS plano durante el proceso de construcción.
* **HTML:** Se utilizará la API de WordPress para generar todo el HTML, asegurando el uso de funciones de escape (`esc_html`, `esc_attr`, `esc_url`) para prevenir vulnerabilidades XSS.

### **1.3. Entorno de Desarrollo y Herramientas**
* **Gestor de Dependencias PHP:** **Composer**. Se usará para gestionar el autoloading (PSR-4) y cualquier posible librería de backend (aunque se intentará mantener al mínimo).
* **Gestor de Paquetes JS:** **NPM** (Node Package Manager). Se usará para gestionar las dependencias de desarrollo como SASS, linters y herramientas de construcción.
* **Empaquetador/Bundler:** **Webpack**. Se utilizará para compilar, minificar y empaquetar los archivos SASS y JavaScript en archivos optimizados para producción.
* **Control de Versiones:** **Git**. El proyecto se gestionará en un repositorio Git (ej. en GitHub o GitLab).
* **Entorno Local:** Se recomienda el uso de **Docker** (`wp-env`) o herramientas similares como LocalWP para replicar el entorno de producción de forma consistente.
* **Integración Continua:** A partir del MVP, toda rama debe ejecutar `composer lint`, `composer test`, `npm run lint` y cualquier script documentado en `08_CodeCanyon_Checklist.md` para garantizar el cumplimiento continuo.

### **1.4. Calidad y Compliance**
* Las herramientas de calidad (`phpcs.xml`, `phpunit.xml.dist`, configuración de ESLint/SASS`) se mantienen versionadas desde el MVP y se documentan en `README.md`.
* El empaquetado reproducible (`plugin/`, `assets/`, `docs/`, `examples/`) se valida en cada release candidate. No se incluyen dependencias de desarrollo ni archivos temporales.
* Los pipelines de CI generan reportes en `docs/reports/` (lint, tests, cobertura) que sirven como evidencia para la checklist de CodeCanyon.
* Las capturas y material demo se guardan en `assets/` con metadatos de licencias en `docs/licensing.md`.

## 4. Entorno de Desarrollo y Producción 🐳

### **4.1. Desarrollo**
* **Plataforma:** Máquina con **Windows 11**.
* **Contenedores:** El desarrollo se realizará utilizando **Docker** para crear entornos de contenedores consistentes. Se utilizará `wp-env` o configuraciones personalizadas de Docker Compose para simular el entorno de WordPress localmente, incluyendo PHP, MySQL/MariaDB y Nginx/Apache. Esto asegura compatibilidad y facilita la colaboración.
- **Repositorio heredado:** El código original (`simple-local-avatars/`) se mantiene como submódulo/git clone de referencia. El nuevo desarrollo vivirá en `src/` siguiendo organización PSR-4 y namespaces `AvatarSteward\*`.
- **Reescritura por módulos:** Servicios clave (uploads, generación automática, moderación, integraciones sociales) se implementarán como clases independientes, reemplazando la estructura monolítica detectada en `includes/class-simple-local-avatars.php`.
- **Compatibilidad legacy:** Se mantendrán wrappers temporales para hooks críticos (`pre_get_avatar_data`, REST fields, comandos WP-CLI) hasta completar la migración.
- **Stack Docker oficial:** El archivo `docker-compose.dev.yml` más `.env` definen un entorno con `wordpress:6.8.3-php8.1`, `mysql:8.0` y `phpmyadmin:5`. Para iniciar, ejecutar en la raíz del proyecto:

```pwsh
docker compose -f docker-compose.dev.yml up -d
```

  - WordPress queda disponible en `http://localhost:${WORDPRESS_PORT}` (por defecto `8080`).
  - phpMyAdmin queda en `http://localhost:${PHPMYADMIN_PORT}` (por defecto `8081`).
  - Credenciales por defecto (`.env`): usuario `avatar_user`, password `avatar_pass123`, base `avatar_steward`, root `root_pass123`. El prefijo de tablas es `avs_`.
  - Volúmenes: `./src` se monta como plugin activo `avatar-steward`. El plugin original **no** se monta automáticamente; se mantiene solo como referencia en el repositorio. Datos persistentes se guardan en volúmenes `wordpress_data` y `db_data`.
  - Para detener el entorno: `docker compose -f docker-compose.dev.yml down`.

### **4.2. Producción**
* **Plataforma:** Servidor VPS en **Ionos**, con **Ubuntu**.
* **Despliegue:** El despliegue se realizará a través de contenedores utilizando **Docker**. Se configurará un entorno de producción con contenedores para WordPress, PHP-FPM, MySQL/MariaDB y Nginx, orquestados con Docker Compose. Se implementarán prácticas de seguridad como actualizaciones regulares, firewalls y monitoreo para mantener la estabilidad y el rendimiento.

## 2. Dependencias del Proyecto 🔗

El objetivo es tener **cero dependencias de producción** en el plugin final para garantizar la máxima compatibilidad y ligereza.

* **Dependencias de PHP (vía Composer):** Ninguna en el producto final. Se puede usar `PHP_CodeSniffer` con los estándares de codificación de WordPress como dependencia de desarrollo.
* **Dependencias de JavaScript (vía NPM):** Ninguna en el producto final. Todas las dependencias (ej. `sass`, `webpack`, `eslint`) serán `devDependencies` y se documentará el comando de build en el `README.md` para el revisor de CodeCanyon.

## 3. Requerimientos del Sistema 📋

* **WordPress:** **Versión >= 5.8**. Esto asegura la disponibilidad de APIs modernas como las del `theme.json` y un editor de bloques estable.
* **PHP:** **Versión >= 7.4**. Se requiere por el uso de sintaxis moderna y mejoras de rendimiento. Se recomendará PHP 8.0+.
* **Extensiones PHP Requeridas:** `gd` o `imagick` para el procesamiento de imágenes (generación de avatares, redimensionamiento).
* **Docker:** Archivos `docker-compose.dev.yml` y `docker-compose.demo.yml` deben iniciar sin intervención manual como criterio de aceptación previo al envío a CodeCanyon.

---

## Documentos Relacionados

Para una comprensión completa del proyecto, consulta los siguientes documentos:

- [Documento de Requerimientos del Producto](01_Documento_Requerimientos_Producto.md): Define los requerimientos técnicos.
- [Estrategia de Negocio](02_Estrategia_de_Negocio.md): Detalla el modelo de negocio.
- [Estrategia de Marketing](03_Estrategia_de_Marketing.md): Describe la estrategia de lanzamiento.
- [Plan de Trabajo](04_Plan_de_Trabajo.md): Incluye el cronograma de desarrollo.
- [Guía de Desarrollo](06_Guia_de_Desarrollo.md): Define principios de desarrollo.
- [Metodología de Desarrollo](07_Metodologia_de_Desarrollo.md): Cubre el flujo de trabajo y pruebas.
- [CodeCanyon Checklist](08_CodeCanyon_Checklist.md): Detalla los requisitos de cumplimiento continuo para Envato.