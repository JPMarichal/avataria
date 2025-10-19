# Instrucciones de Instalación y Prueba - Avatar Steward

## ⚠️ IMPORTANTE: Estructura del Plugin

El plugin ahora tiene **DOS archivos principales** para compatibilidad:

1. **`avatar-steward.php`** (en la raíz) - Archivo principal para WordPress
2. **`src/avatar-steward.php`** - Archivo heredado (ahora es un proxy)

## 📁 Instalación Correcta

### Opción 1: Plugin Completo (Recomendado)
```bash
# En tu directorio wp-content/plugins/
git clone [tu-repo] avatar-steward
# o copia toda la carpeta avataria como avatar-steward
```

### Opción 2: Verificar Estructura
Tu directorio de plugin debe verse así:
```
wp-content/plugins/avatar-steward/
├── avatar-steward.php          ← Archivo principal
├── assets/
│   ├── css/profile-avatar.css  ← Estilos
│   └── js/profile-avatar.js    ← JavaScript
├── src/
│   └── AvatarSteward/
└── ...
```

## 🔧 Pasos de Instalación

1. **Desactivar el plugin** (si ya estaba activo)
2. **Eliminar cache** del navegador (Ctrl+Shift+R o ventana incógnito)
3. **Activar el plugin** desde WordPress admin
4. **Verificar que se activa** desde el archivo principal `avatar-steward.php`

## 🧪 Cómo Probar

### 1. Verificar Activación
- Ve a Plugins → Plugins Instalados
- Busca "Avatar Steward"
- Debe mostrar "Plugin activado" 

### 2. Probar la Sección de Avatar
- Ve a **Usuarios → Tu Perfil** (o **Users → Your Profile**)
- **Busca la sección "Avatar"** que debería aparecer:
  - ✅ Después de "Acerca de ti" / "About Yourself"
  - ✅ Antes de "Gestión de la cuenta" / "Account Management"
  - ✅ Con fondo gris claro y bordes redondeados

### 3. Verificar en Consola del Navegador
Abre las **Herramientas de Desarrollador** (F12) y ve a la **Consola**:

```javascript
// Deberías ver estos mensajes:
Avatar Steward: Found avatar section, attempting to reposition...
Avatar Steward: Avatar section repositioned after first form table
```

### 4. Verificar Assets Cargados
En la pestaña **Network/Red** de las herramientas de desarrollador:
- ✅ `profile-avatar.css` debe cargarse
- ✅ `profile-avatar.js` debe cargarse

## 🐛 Debug / Solución de Problemas

### Si la sección no aparece:

1. **Verifica el archivo principal**:
   ```php
   // Añade esto temporalmente a functions.php
   add_action('wp_loaded', function() {
       if (class_exists('AvatarSteward\\Plugin')) {
           error_log('Avatar Steward: Plugin loaded correctly');
       } else {
           error_log('Avatar Steward: Plugin class not found');
       }
   });
   ```

2. **Verifica hooks**:
   ```php
   // Añade esto temporalmente a functions.php
   add_action('show_user_profile', function() {
       error_log('Hook show_user_profile fired');
   }, 1);
   ```

3. **Verifica JavaScript en consola**:
   ```javascript
   // Ejecuta en consola del navegador
   console.log('Avatar section:', document.getElementById('avatar-steward-section'));
   console.log('Profile form:', document.querySelector('.user-edit-php, .profile-php'));
   ```

### Si los estilos no se aplican:

1. **Verifica en código fuente** que aparezcan estas líneas:
   ```html
   <link rel="stylesheet" href=".../assets/css/profile-avatar.css">
   <script src=".../assets/js/profile-avatar.js"></script>
   ```

2. **Fuerza reload** con Ctrl+Shift+R

3. **Verifica cache del plugin** - algunos plugins de cache impiden cargar nuevos assets

## 📋 Checklist de Verificación

- [ ] Plugin activado desde `avatar-steward.php` (raíz)
- [ ] Cache del navegador limpiado
- [ ] En página Usuarios → Perfil
- [ ] Sección "Avatar" visible
- [ ] Sección con fondo gris claro y bordes redondeados
- [ ] Sección posicionada después de "Acerca de ti"
- [ ] JavaScript ejecutándose (ver consola)
- [ ] CSS cargado (verificar en Network tab)

## 🆘 Si Sigue Sin Funcionar

1. **Copia el archivo de debug** `debug-avatar-steward.php` a tu tema
2. **Incluye en functions.php**:
   ```php
   require_once get_template_directory() . '/debug-avatar-steward.php';
   ```
3. **Ve a la consola del navegador** en la página de perfil
4. **Revisa el error_log de WordPress** para mensajes de Avatar Steward

---

## 🎯 Resultado Esperado

Al final deberías ver:
- ✅ Sección "Avatar" inmediatamente después de "Acerca de ti"  
- ✅ Fondo gris claro (#f3f4f6) con bordes redondeados
- ✅ Campo de subida de archivo funcional
- ✅ Mensajes en consola confirmando reposicionamiento

**Si sigues teniendo problemas, compárteme:**
1. La URL exacta donde probaste
2. Los mensajes de error en la consola
3. Los logs de error_log de WordPress
4. La estructura de tu directorio de plugins