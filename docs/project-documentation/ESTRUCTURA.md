# Estructura del Plugin Avatar Steward

## 📂 Organización de Archivos

```
avataria/ (repositorio raíz = directorio del plugin)
│
├── avatar-steward.php          ← PUNTO DE ENTRADA ÚNICO DEL PLUGIN
│                                 Define constantes y carga Plugin.php
│
├── assets/                     ← Recursos estáticos del plugin
│   ├── css/
│   │   └── profile-avatar.css  ← Estilos para la UI de perfil
│   ├── js/
│   │   └── profile-avatar.js   ← Lógica de reposicionamiento de sección
│   └── screenshots/            ← Capturas para WordPress.org
│
├── src/                        ← Código fuente PHP
│   ├── index.php               ← Protección de directorio
│   └── AvatarSteward/          ← Namespace principal
│       ├── Plugin.php          ← Clase singleton principal
│       ├── Core/               ← Sistema de avatares
│       ├── Domain/             ← Lógica de negocio
│       ├── Admin/              ← Páginas de administración
│       └── Infrastructure/     ← Servicios transversales
│
├── tests/                      ← Tests automatizados
│   └── phpunit/
│       ├── bootstrap.php       ← Configuración de PHPUnit
│       └── Core/               ← Tests unitarios
│
├── vendor/                     ← Dependencias Composer
│   └── autoload.php
│
├── wp-content/                 ← SOLO para Docker (estructura vacía)
│   ├── plugins/                ← Mapeado a themes/uploads/languages en contenedor
│   ├── themes/
│   ├── uploads/
│   └── languages/
│
├── composer.json               ← Dependencias PHP y scripts
├── phpunit.xml.dist            ← Configuración de tests
├── phpcs.xml                   ← Estándares de código WordPress
└── docker-compose.dev.yml      ← Entorno de desarrollo
```

## 🔧 Flujo de Carga

### 1. WordPress detecta el plugin
WordPress busca archivos con cabecera de plugin en `wp-content/plugins/*/`.

Encuentra: `wp-content/plugins/avatar-steward/avatar-steward.php`

### 2. avatar-steward.php se ejecuta
```php
// Define constantes globales
define('AVATAR_STEWARD_PLUGIN_FILE', __FILE__);
define('AVATAR_STEWARD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AVATAR_STEWARD_PLUGIN_URL', plugin_dir_url(__FILE__));

// Carga Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Carga clase principal
require __DIR__ . '/src/AvatarSteward/Plugin.php';

// Inicia el plugin (singleton)
AvatarSteward\Plugin::instance();
```

### 3. Plugin.php orquesta todo
```php
Plugin::instance()
  └─> boot()
      ├─> init_upload_services()    # Subida de avatares
      ├─> init_avatar_handler()     # Sistema de override
      ├─> init_settings_page()      # Panel de admin
      └─> init_migration_page()     # Herramientas de migración
```

## 🐳 Configuración Docker

### Volúmenes montados:
```yaml
volumes:
  # Todo el repo se monta como plugin
  - ./:/var/www/html/wp-content/plugins/avatar-steward
  
  # Subdirectorios de wp-content mapeados individualmente
  - ./wp-content/themes:/var/www/html/wp-content/themes
  - ./wp-content/uploads:/var/www/html/wp-content/uploads
  - ./wp-content/languages:/var/www/html/wp-content/languages
```

**Ventajas:**
- ✅ Cambios en archivos locales se reflejan inmediatamente en el contenedor
- ✅ No hay conflictos entre volúmenes
- ✅ wp-content/ del host solo contiene carpetas vacías para estructura

## 📍 Rutas Clave

### En desarrollo (Codespaces/Local)
```
AVATAR_STEWARD_PLUGIN_FILE = /workspaces/avataria/avatar-steward.php
AVATAR_STEWARD_PLUGIN_DIR  = /workspaces/avataria/
AVATAR_STEWARD_PLUGIN_URL  = http://localhost:8080/wp-content/plugins/avatar-steward/
```

### En producción (WordPress real)
```
AVATAR_STEWARD_PLUGIN_FILE = /var/www/html/wp-content/plugins/avatar-steward/avatar-steward.php
AVATAR_STEWARD_PLUGIN_DIR  = /var/www/html/wp-content/plugins/avatar-steward/
AVATAR_STEWARD_PLUGIN_URL  = https://example.com/wp-content/plugins/avatar-steward/
```

### Acceso a assets desde PHP
```php
// CSS
$css_url = AVATAR_STEWARD_PLUGIN_URL . 'assets/css/profile-avatar.css';

// JavaScript
$js_url = AVATAR_STEWARD_PLUGIN_URL . 'assets/js/profile-avatar.js';

// Verificar existencia
$css_path = AVATAR_STEWARD_PLUGIN_DIR . 'assets/css/profile-avatar.css';
if (file_exists($css_path)) {
    wp_enqueue_style('avatar-steward-profile', $css_url);
}
```

## ✅ Verificación de Congruencia

### Comando de validación:
```bash
# Verificar que solo existe un avatar-steward.php
find . -name "avatar-steward.php" -type f

# Debe retornar solo: ./avatar-steward.php
```

### Tests de integración:
```bash
# Ejecutar suite completa
composer test

# Debe retornar: OK (219 tests, 428 assertions)
```

### Verificar constantes en WordPress:
```php
// En el dashboard de WordPress, ir a:
// Herramientas > Información del sitio > (buscar Avatar Steward)

// O agregar temporalmente en functions.php:
error_log('Plugin File: ' . AVATAR_STEWARD_PLUGIN_FILE);
error_log('Plugin Dir: ' . AVATAR_STEWARD_PLUGIN_DIR);
error_log('Plugin URL: ' . AVATAR_STEWARD_PLUGIN_URL);
```

## 🚫 Antipatrones Eliminados

### ❌ NO hacer:
```
/src/avatar-steward.php           ← ELIMINADO (duplicado)
/src/assets/                      ← ELIMINADO (duplicado)
/wp-content/plugins/avatar-steward/  ← ELIMINADO (copia obsoleta)
```

### ❌ NO montar volúmenes conflictivos:
```yaml
# MAL - volúmenes sobreescriben entre sí
volumes:
  - ./src:/var/www/html/wp-content/plugins/avatar-steward
  - ./wp-content:/var/www/html/wp-content  # ← Sobreescribe el anterior
```

## 📚 Referencias

- **Código principal:** `src/AvatarSteward/Plugin.php`
- **Configuración Docker:** `docker-compose.dev.yml`
- **Tests:** `tests/phpunit/bootstrap.php`
- **Assets:** `assets/css/` y `assets/js/`
- **Documentación:** `docs/` y `documentacion/`

---

**Última actualización:** 20 de octubre, 2025  
**Versión del plugin:** 0.1.0 (MVP)
