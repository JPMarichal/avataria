# Scripts de Utilidad - Avatar Steward

Este directorio contiene scripts de utilidad para desarrollo, testing y verificación del plugin.

## 📜 Scripts Disponibles

### `package-plugin.sh` / `package-plugin.ps1`

**Propósito:** Generar un paquete ZIP limpio del plugin para distribución (WordPress.org o CodeCanyon).

**Uso:**
```bash
# Linux/Mac
./scripts/package-plugin.sh [--version VERSION] [--no-dev] [--pro]

# Windows PowerShell
.\scripts\package-plugin.ps1 [-Version VERSION] [-NoDev] [-Pro]
```

**Opciones:**
- `--version VERSION` / `-Version VERSION`: Especifica la versión del paquete (auto-detectada si se omite)
- `--no-dev` / `-NoDev`: Instala solo dependencias de producción con `composer install --no-dev`
- `--pro` / `-Pro`: Genera el paquete Pro (incluye documentación de licencias)

**Características:**
- Usa `.distignore` para excluir archivos de desarrollo
- Opcionalmente instala dependencias de producción
- Incluye documentación esencial para usuarios
- Crea estructura de directorios correcta para WordPress
- Verifica el contenido del paquete generado

**Ejemplos:**
```bash
# Generar paquete básico
./scripts/package-plugin.sh

# Generar paquete Pro para CodeCanyon sin dependencias dev
./scripts/package-plugin.sh --no-dev --pro --version 1.0.0

# PowerShell equivalente
.\scripts\package-plugin.ps1 -NoDev -Pro -Version "1.0.0"
```

**Output:** `avatar-steward-{version}.zip` o `avatar-steward-pro-{version}.zip`

**Relacionado con:** Tarea 3.9 - Pipeline de empaquetado

### `validate-codecanyon.sh` / `validate-codecanyon.ps1`

**Propósito:** Validar que el plugin cumple con los requisitos de calidad de CodeCanyon antes de empaquetar.

**Uso:**
```bash
# Linux/Mac
./scripts/validate-codecanyon.sh

# Windows PowerShell
.\scripts\validate-codecanyon.ps1
```

**Verificaciones realizadas:**
1. ✓ Documentación requerida (README, CHANGELOG, LICENSE, manual, FAQ, soporte)
2. ✓ Configuración de calidad de código (phpcs.xml, phpunit.xml.dist, ESLint)
3. ✓ Assets y capturas de pantalla
4. ✓ Documentación de licencias
5. ✓ Infraestructura de testing
6. ✓ Entorno de demo reproducible
7. ✓ Estructura del paquete
8. ✓ Cumplimiento de estándares WordPress
9. ✓ Metadata del plugin
10. ✓ Mejores prácticas de seguridad

**Output:**
- ✅ Verde: Verificación exitosa
- ⚠️  Amarillo: Advertencias (no críticas)
- ❌ Rojo: Fallos críticos

**Exit codes:**
- `0`: Validación exitosa, listo para empaquetar
- `1`: Problemas críticos encontrados

**Ejemplo de flujo:**
```bash
# 1. Validar requisitos
./scripts/validate-codecanyon.sh

# 2. Si pasa, generar paquete
./scripts/package-plugin.sh --no-dev --pro
```

**Relacionado con:** 
- `documentacion/08_CodeCanyon_Checklist.md`
- Tarea 3.9 - Pipeline de empaquetado

### `demo-avatar-initials-fix.php`

**Propósito:** Demostración interactiva del fix de avatar por defecto con iniciales.

**Uso:**
```bash
php scripts/demo-avatar-initials-fix.php
```

**Características:**
- Simula escenarios de usuarios sin avatar local
- Muestra cómo se generan avatares con iniciales automáticamente
- Verifica que URLs rotas como `2x` sean reemplazadas
- Prueba ambos filtros: `filter_avatar_data` y `filter_avatar_url`
- Output colorizado y detallado con resultados de prueba

**Output esperado:**
```
✓ SUCCESS: Initials avatar generated!
Initials: JD
Background Color: #2c3e50
```

**Relacionado con:** Issue #JPMarichal/avataria (Avatar removal URL fix)

### `verify-structure.sh`

**Propósito:** Verificar la congruencia absoluta de la estructura del plugin.

**Uso:**
```bash
./scripts/verify-structure.sh
```

**Verificaciones realizadas:**
1. ✓ Solo existe UN archivo `avatar-steward.php` (en la raíz)
2. ✓ No hay duplicados en `src/avatar-steward.php`
3. ✓ No hay assets duplicados en `src/assets/`
4. ✓ Assets existen en la ubicación correcta (`assets/css/`, `assets/js/`)
5. ✓ Estructura de código fuente (`src/AvatarSteward/`)
6. ✓ `wp-content/plugins/` está limpio (sin copias del plugin)
7. ✓ Configuración de Docker es correcta
8. ✓ Suite de tests pasa (219 tests)
9. ✓ Dependencias de Composer instaladas

**Output:**
- ✅ Verde: Todo correcto
- ❌ Rojo: Errores encontrados
- ⚠️  Amarillo: Advertencias

**Exit codes:**
- `0`: Verificación exitosa
- `1`: Problemas encontrados

**Ejemplo de uso en CI:**
```bash
# En .github/workflows/ci.yml
- name: Verify structure
  run: ./scripts/verify-structure.sh
```

## 🔧 Agregar Nuevos Scripts

Cuando agregues un nuevo script:

1. **Hazlo ejecutable:**
   ```bash
   chmod +x scripts/nuevo-script.sh
   ```

2. **Documéntalo aquí** con:
   - Propósito
   - Uso
   - Opciones/argumentos
   - Ejemplos

3. **Incluye shebang y set -e:**
   ```bash
   #!/bin/bash
   set -e  # Salir en caso de error
   ```

4. **Usa colores para output:**
   ```bash
   RED='\033[0;31m'
   GREEN='\033[0;32m'
   YELLOW='\033[1;33m'
   NC='\033[0m'  # No Color
   ```

### `licensing/` Directory

**Propósito:** Scripts de automatización para extracción y documentación de licencias.

**Ver:** `scripts/licensing/README.md` para documentación completa.

**Scripts incluidos:**
- `extract-php-licenses.sh` - Extrae licencias de dependencias de Composer
- `extract-js-licenses.sh` - Extrae licencias de dependencias de NPM

**Uso rápido:**
```bash
# PHP dependencies
./scripts/licensing/extract-php-licenses.sh --output /tmp/php-licenses.md

# JavaScript dependencies
./scripts/licensing/extract-js-licenses.sh --output /tmp/js-licenses.md
```

**Relacionado con:** 
- `docs/licenses-pro.md` - Registro completo de assets Pro
- Tarea 3.8 - Documentar licencias de fuentes, iconos e imágenes

## 📚 Scripts Planificados

- ~~`build-release.sh`~~ → ✅ Implementado como `package-plugin.sh`
- `run-lint.sh` - Ejecutar linters con configuración específica
- `setup-dev.sh` - Configurar entorno de desarrollo completo
- `db-reset.sh` - Resetear base de datos de desarrollo

---

**Última actualización:** 21 de octubre, 2025
