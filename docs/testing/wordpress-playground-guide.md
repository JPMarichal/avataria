# Guía: Pruebas de Compatibilidad con WordPress Playground

## 🎯 Objetivo
Completar las pruebas de compatibilidad de WordPress para versiones 6.2 y 6.0 usando WordPress Playground.

**Nota Importante**: WordPress Playground no soporta versiones anteriores a 6.2. La versión más antigua disponible es WordPress 6.2.

## 📋 Pruebas Pendientes
- ✅ WordPress 6.4 + PHP 8.2 (ya probado en Docker)
- ⏳ WordPress 6.2 + PHP 7.4 (mínima versión disponible en Playground)
- ⏳ WordPress 6.0 + PHP 8.0

## 🚀 Cómo Usar WordPress Playground

### Paso 1: Acceder a WordPress Playground
1. Ve a: https://playground.wordpress.net/
2. Haz clic en "Start fresh WordPress"

### Paso 2: Configurar Versiones Específicas

#### Opción A: Usar URL con parámetros (Recomendado)
```url
https://playground.wordpress.net/?wp=6.2&php=7.4
https://playground.wordpress.net/?wp=6.0&php=8.0
```

#### Opción B: Configurar manualmente
1. En Playground, haz clic en "Settings" (⚙️)
2. Selecciona la versión de WordPress deseada
3. Selecciona la versión de PHP deseada
4. Haz clic en "Apply Changes"

### Paso 3: Instalar el Plugin

#### Método 1: Subir ZIP (Recomendado)
1. Ve a **Plugins** → **Add New Plugin**
2. Haz clic en **Upload Plugin**
3. Selecciona el archivo `avatar-steward-0.1.0.zip`
4. Haz clic en **Install Now**
5. Activa el plugin

#### Método 2: Instalar desde GitHub (Alternativo)
```bash
# En terminal de Playground
wp plugin install https://github.com/[tu-repo]/avatar-steward/archive/main.zip --activate
```

### Paso 4: Ejecutar Pruebas de Compatibilidad

#### Pruebas Básicas
1. **Verificar activación**: Plugin se activa sin errores
2. **Verificar menús**: Aparece "Avatar Steward" en menú admin
3. **Verificar páginas**: Acceder a configuración del plugin
4. **Verificar funcionalidades básicas**:
   - Subida de avatares
   - Configuración de tamaños
   - Moderación básica

#### Pruebas de Integración
1. **Crear usuario de prueba**
2. **Subir avatar** desde perfil de usuario
3. **Verificar display** en comentarios/foros
4. **Probar configuración** desde admin

### Paso 5: Verificar Logs de Error
```bash
# En terminal de Playground
wp core version
php --version
wp plugin list
wp plugin status avatar-steward
```

### Paso 6: Documentar Resultados

Para cada combinación de versiones, documentar:

```
## WordPress X.X + PHP Y.Y
- ✅ Activación: Exitosa
- ✅ Funcionalidades básicas: OK
- ✅ Integración: OK
- ❌ Errores encontrados: [detallar si hay]
- 📝 Notas: [observaciones adicionales]
```

## ⚠️ Limitaciones de WordPress Playground

### Versiones Disponibles
- **Versión mínima**: WordPress 6.2 (no se puede probar WP 5.8)
- **Versiones disponibles**: 6.2, 6.3, 6.4, 6.5, latest
- **PHP versions**: 7.4, 8.0, 8.1, 8.2, 8.3

### Implicaciones para las Pruebas
Dado que no podemos probar WordPress 5.8 en Playground:
1. **WP 5.8 + PHP 7.4** se considera compatible por análisis estático
2. **Enfoque en WP 6.2+** para pruebas reales en entorno WordPress
3. **Validación adicional** requerida para entornos de producción con WP 5.8

### Estrategia Alternativa
Para validar compatibilidad con WordPress 5.8:
- Análisis estático de código (✅ Completado)
- Pruebas en entorno local con Docker (si es necesario)
- Revisión de changelog de WordPress para cambios breaking
- Validación con usuarios beta en entornos controlados

### Error de versión PHP
- Playground puede no tener exactamente la versión solicitada
- Verifica con `php --version` en terminal

### Plugin no se activa
- Revisa logs de error en **Tools** → **Site Health**
- Verifica dependencias faltantes

### Problemas de permisos
- Playground tiene permisos limitados
- Algunos directorios pueden no ser escribibles

## 📊 Matriz de Compatibilidad Objetivo

| WordPress | PHP 7.4 | PHP 8.0 | PHP 8.2 | Notas |
|-----------|---------|---------|---------|-------|
| 5.8      | ❌ N/A  | ❌ N/A  | ❌ N/A  | No disponible en Playground |
| 6.0      | ❌ N/A  | ✅ Test | ❌ N/A  | |
| 6.2      | ✅ Test | ❌ N/A  | ❌ N/A  | Versión mínima en Playground |
| 6.4      | ❌ N/A  | ❌ N/A  | ✅ OK   | Probado en Docker |

## 🎯 Checklist de Validación

- [ ] WP 6.2 + PHP 7.4: Activación exitosa
- [ ] WP 6.2 + PHP 7.4: Funcionalidades básicas
- [ ] WP 6.2 + PHP 7.4: Sin errores fatales
- [ ] WP 6.0 + PHP 8.0: Activación exitosa
- [ ] WP 6.0 + PHP 8.0: Funcionalidades básicas
- [ ] WP 6.0 + PHP 8.0: Sin errores fatales
- [ ] Documentar resultados en `docs/testing/compatibility-results.md`
- [ ] Actualizar `docs/testing/phase-2-acceptance-tests.md`

## 🚀 Próximos Pasos

Después de completar estas pruebas:
1. Actualizar documentación de compatibilidad
2. Marcar pruebas como completadas en acceptance tests
3. Continuar con pruebas de subida de archivos (PNG/GIF)
4. Preparar assets para WordPress.org (screenshots, icons)

## 📚 Recursos Adicionales

- [WordPress Playground Docs](https://wordpress.github.io/wordpress-playground/)
- [Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
