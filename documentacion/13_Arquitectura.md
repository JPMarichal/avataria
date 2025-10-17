# Arquitectura de Alto Nivel - Avatar Steward

## 1. Objetivos Arquitectónicos
- **Modularidad:** Cada dominio (subidas, iniciales, moderación, integraciones) debe poder evolucionar de forma independiente.
- **Compatibilidad WordPress:** Mantener hooks y convenciones nativas (`actions`, `filters`, roles, opciones) sin romper la experiencia WP.
- **Escalabilidad futura:** La estructura debe permitir incorporar fácilmente las características Pro (biblioteca, integraciones sociales, auditoría).
- **Testabilidad:** Servicios desacoplados, facilitando pruebas unitarias con PHPUnit y pruebas de integración controladas.

## 2. Estructura de Carpetas y Namespaces
| Carpeta | Namespace | Descripción |
| --- | --- | --- |
| `src/` | `AvatarSteward` | Contiene el bootstrap (`avatar-steward.php`) y la raíz de namespaces. |
| `src/Core/` | `AvatarSteward\Core` | Inicialización, contenedor de servicios, registro de hooks. |
| `src/Admin/` | `AvatarSteward\Admin` | Pantallas del panel (settings, moderación) y componentes UI. |
| `src/Domain/Uploads/` | `AvatarSteward\Domain\Uploads` | Servicios para subir, validar y persistir avatares. |
| `src/Domain/Initials/` | `AvatarSteward\Domain\Initials` | Generación de avatares por iniciales y modo bajo ancho de banda. |
| `src/Domain/Library/` | `AvatarSteward\Domain\Library` | Gestión de biblioteca de avatares predefinidos. |
| `src/Domain/Moderation/` | `AvatarSteward\Domain\Moderation` | Lógica para colas, historial y decisiones de moderación. |
| `src/Domain/Integrations/` | `AvatarSteward\Domain\Integrations` | Conectores sociales (Twitter, Facebook) y futuros proveedores. |
| `src/Infrastructure/` | `AvatarSteward\Infrastructure` | Persistencia (repositorios), adaptadores WP, utilidades compartidas. |
| `tests/` | `AvatarSteward\Tests` | Suites PHPUnit (unitarias, integración). |

## 3. Componentes Principales
### 3.1 Bootstrap y Núcleo (`AvatarSteward\Plugin`)
- Singleton que orquesta la carga del plugin.
- Registra el contenedor de servicios y ejecuta `ServiceProvider`s.
- Sincroniza el ciclo de vida con `plugins_loaded` y `init`.

### 3.2 Contenedor de Servicios (`Core\ServiceContainer`)
- Responsable de instanciar y compartir servicios (inyección manual ligera).
- Permite registrar proveedores (`Core\ServiceProviderInterface`).

### 3.3 Proveedores de Servicios
- **`Core\AdminServiceProvider`**: Registra pantallas y assets del admin.
- **`Core\AvatarServiceProvider`**: Registra servicios de subida, iniciales y biblioteca.
- **`Core\ModerationServiceProvider`**: Registra colas, reportes y hooks de moderación.
- **`Core\IntegrationServiceProvider`**: Registra conectores sociales y API externas.

### 3.4 Módulos de Dominio
- **Uploads** (`Domain\Uploads\UploadService`, `UploadValidator`, `StorageRepository`).
- **Initials** (`Domain\Initials\Generator`, `PaletteRegistry`).
- **Moderation** (`Domain\Moderation\Queue`, `DecisionService`, `NotificationService`).
- **Library** (`Domain\Library\Repository`, `AssignmentService`).
- **Roles & Permissions** (`Domain\Access\PolicyEvaluator`).
- **Settings** (`Admin\SettingsPage`, `Admin\SettingsSections`).

## 4. Hooks y Endpoints
- **Carga de avatares:**
  - `add_filter('pre_get_avatar_data', AvatarService::class.'::filterAvatarData')`.
  - `add_action('personal_options_update', UploadController::class.'::handleProfileUpdate')`.
- **Moderación:**
  - `add_action('admin_menu', Admin\MenuRegistrar::class.'::register')`.
  - `register_rest_route('avatarsteward/v1', '/moderation', ...)` para operaciones AJAX/REST.
- **Eventos Custom:**
  - `do_action('avatarsteward/avatar_uploaded', $userId, $avatarId)`.
  - `apply_filters('avatarsteward/default_avatar', $avatar)`.

## 5. Flujos Clave
1. **Actualización de perfil:**
   - WordPress dispara `personal_options_update` → `UploadController` valida, persiste y dispara evento `avatar_uploaded`.
   - `AvatarService` actualiza meta de usuario y cachea resultados.
2. **Renderizado de avatar:**
   - `get_avatar()` llama `pre_get_avatar_data` → `AvatarService` determina origen (upload, iniciales, librería) y devuelve datos normalizados.
3. **Moderación:**
   - Solicitudes desde la UI usan REST (`/moderation`) → `ModerationController` consulta `Queue` y aplica decisiones (`DecisionService`).
   - Notificaciones opcionales mediante `NotificationService` (emails/admin notices).

## 6. Persistencia y Cachedo
- Metadatos de usuario (`user_meta`) para asignaciones de avatar.
- Tablas personalizadas (`wp_avatarsteward_queue`, `wp_avatarsteward_library`) para moderación y biblioteca.
- Uso de transients para cachear resultados de `pre_get_avatar_data` en modo visitante.

## 7. Testing
- **Unitarias:** Servicios `Domain\*` cubiertos con PHPUnit (mocks de adaptadores WP).
- **Integración:** Tests que ejercen hooks relevantes (`pre_get_avatar_data`, REST) en entorno de pruebas.
- **Snapshots:** Opcional para validar estructura de respuestas REST.

## 8. Roadmap Arquitectónico (relación con Fase 1)
- **Tarea 1.3:** Implementar `Core\ServiceContainer`, `Plugin` estable y skeleton de ServiceProviders.
- **Tarea 1.4:** Documentar componentes en `docs/` (diagrama actualizado, README técnico).
- **Tarea 1.5:** Integrar tooling (`phpcs.xml`, `phpunit.xml`, ESLint`) apuntando a la estructura propuesta.
- **Tarea 1.6:** Mapear empaquetado (`plugin/`, `assets/`, `docs/`) reflejando módulos descritos.
- **Tarea 1.7:** Documentar clases heredadas del plugin original y plan de refactor por namespace.

## 9. Próximos Pasos
- Detallar casos de uso en `documentacion/mvp-spec.json` alineados con los servicios definidos.
- Completar diagrama visual (Figma/Lucidchart) para apoyar revisiones con stakeholders.
- Preparar plan de migración desde `Simple_Local_Avatars` hacia los nuevos módulos utilizando adaptadores temporales.
