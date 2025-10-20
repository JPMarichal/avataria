# Scripts de Utilidad - Avatar Steward

Este directorio contiene scripts de utilidad para desarrollo, testing y verificación del plugin.

## 📜 Scripts Disponibles

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

## 📚 Scripts Planificados

- `build-release.sh` - Generar ZIP del plugin para distribución
- `run-lint.sh` - Ejecutar linters con configuración específica
- `setup-dev.sh` - Configurar entorno de desarrollo completo
- `db-reset.sh` - Resetear base de datos de desarrollo

---

**Última actualización:** 20 de octubre, 2025
