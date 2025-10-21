# Guía de Testing de Compatibilidad WordPress/PHP
**Fecha:** Octubre 2025  
**Plugin:** Avatar Steward  
**Versiones Objetivo:** WP 5.8+, PHP 7.4+

---

## Estrategias de Testing Ágil

### 🎯 Opción 1: Análisis Estático (Más Rápido - 5 minutos)

**Herramienta:** PHPCompatibility + WordPress Coding Standards

#### Ventajas
- ✅ No requiere instalar múltiples versiones
- ✅ Detecta incompatibilidades de sintaxis PHP
- ✅ Verifica funciones deprecadas de WordPress
- ✅ Automatizable en CI/CD

#### Implementación

```bash
# Ya instalado en el proyecto via Composer
composer require --dev phpcompatibility/phpcompatibility-wp

# Verificar compatibilidad PHP 7.4+
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4- src/

# Verificar compatibilidad PHP 8.0+
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.0- src/

# Verificar compatibilidad PHP 8.2+
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.2- src/
```

#### Qué detecta
- ✅ Funciones removidas en PHP 8+
- ✅ Sintaxis incompatible
- ✅ Funciones deprecadas de WordPress
- ✅ Constantes removidas
- ❌ NO detecta: Problemas de runtime, bugs de versiones específicas de WP

---

### 🐳 Opción 2: Docker Multi-Stage (Medio - 30 minutos setup inicial)

**Herramienta:** Docker Compose con múltiples servicios

#### Ventajas
- ✅ Entornos aislados
- ✅ Una vez configurado, muy rápido cambiar
- ✅ Reproducible
- ✅ Permite testing visual real

#### Implementación

Crear `docker-compose.test.yml`:

```yaml
version: '3.8'

services:
  # WordPress 5.8 + PHP 7.4
  wp58-php74:
    image: wordpress:5.8-php7.4-apache
    ports:
      - "8081:80"
    environment:
      WORDPRESS_DB_HOST: db58
      WORDPRESS_DB_NAME: wp58
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
    volumes:
      - ./:/var/www/html/wp-content/plugins/avatar-steward
    depends_on:
      - db58

  db58:
    image: mysql:5.7
    environment:
      MYSQL_DATABASE: wp58
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
      MYSQL_ROOT_PASSWORD: rootpass

  # WordPress 6.0 + PHP 8.0
  wp60-php80:
    image: wordpress:6.0-php8.0-apache
    ports:
      - "8082:80"
    environment:
      WORDPRESS_DB_HOST: db60
      WORDPRESS_DB_NAME: wp60
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
    volumes:
      - ./:/var/www/html/wp-content/plugins/avatar-steward
    depends_on:
      - db60

  db60:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: wp60
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
      MYSQL_ROOT_PASSWORD: rootpass

  # WordPress 6.4 + PHP 8.2
  wp64-php82:
    image: wordpress:6.4-php8.2-apache
    ports:
      - "8083:80"
    environment:
      WORDPRESS_DB_HOST: db64
      WORDPRESS_DB_NAME: wp64
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
    volumes:
      - ./:/var/www/html/wp-content/plugins/avatar-steward
    depends_on:
      - db64

  db64:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: wp64
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
      MYSQL_ROOT_PASSWORD: rootpass
```

**Uso:**

```bash
# Levantar solo una versión para pruebas rápidas
docker compose -f docker-compose.test.yml up wp58-php74 db58

# Acceder a http://localhost:8081
# Instalar WP, activar plugin, probar funcionalidad

# Bajar y probar otra versión
docker compose -f docker-compose.test.yml down
docker compose -f docker-compose.test.yml up wp60-php80 db60
```

#### Tiempo estimado
- Setup inicial: 30 min (crear archivo, pull de imágenes)
- Cambiar entre versiones: 2-3 min
- Testing por versión: 10-15 min
- **Total para 3 versiones: ~45 minutos**

---

### ☁️ Opción 3: Servicios Online (Más Rápido - 15 minutos)

**Herramienta:** Playground de WordPress / Instawp / TasteWP

#### WordPress Playground (Gratis, Instantáneo)
- URL: https://playground.wordpress.net/
- ✅ Completamente en el navegador (WebAssembly)
- ✅ Múltiples versiones de PHP y WP
- ✅ Sube ZIP del plugin
- ❌ Limitado a testing básico (no producción)

#### InstaWP (Gratis para testing)
- URL: https://instawp.com/
- ✅ Instancias temporales de WP reales
- ✅ Múltiples versiones disponibles
- ✅ Acceso admin completo
- ⏱️ Expiran después de 48 horas

#### Proceso
1. Generar ZIP del plugin: `composer run package` (si tienes script)
2. Subir a playground/instawp
3. Activar y probar funcionalidad básica
4. Cambiar versión de PHP en settings
5. Reactivar y verificar errores

#### Tiempo estimado
- Preparar ZIP: 2 min
- Subir y activar: 3 min
- Testing básico: 5 min
- Cambiar PHP y retest: 5 min
- **Total: ~15 minutos**

---

### 🤖 Opción 4: CI/CD con GitHub Actions (Automatizado)

**Herramienta:** WordPress Plugin Unit Test Setup

#### Ventajas
- ✅ Completamente automatizado
- ✅ Testing en cada push/PR
- ✅ Matrix de múltiples versiones simultáneas
- ✅ Gratis en GitHub
- ❌ Solo para tests unitarios (no UI)

#### Implementación

Crear `.github/workflows/compatibility-tests.yml`:

```yaml
name: PHP/WordPress Compatibility Tests

on:
  push:
    branches: [ master, develop ]
  pull_request:
    branches: [ master ]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['7.4', '8.0', '8.1', '8.2']
        wordpress: ['5.8', '6.0', '6.4', 'latest']
        
    name: PHP ${{ matrix.php }} / WP ${{ matrix.wordpress }}
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php }}
        extensions: mbstring, intl, mysql
        coverage: none
        
    - name: Install Composer dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Setup WordPress test environment
      run: |
        bash bin/install-wp-tests.sh wordpress_test root '' localhost ${{ matrix.wordpress }}
        
    - name: Run PHPUnit tests
      run: composer test
      
    - name: Check coding standards
      run: composer lint
```

#### Tiempo estimado
- Setup inicial: 30 min
- Ejecución automática: 5-10 min por push
- **Total: Setup único + 0 min por cambio futuro**

---

## 🎯 Recomendación para Avatar Steward

### Para Fase 2 MVP (Ahora)

**Estrategia Híbrida - 20 minutos total:**

1. **Análisis Estático (5 min)** ✅ Hacer AHORA
   ```bash
   # PHP 7.4 compatibility
   vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4- src/
   
   # PHP 8.0 compatibility
   vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.0- src/
   
   # PHP 8.2 compatibility
   vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.2- src/
   ```
   - Si pasa: ✅ Alta confianza en compatibilidad
   - Si falla: ❌ Arreglar antes de continuar

2. **WordPress Playground (15 min)** ✅ Hacer AHORA
   - Crear ZIP del plugin
   - Probar en playground.wordpress.net con:
     - WP 5.8 + PHP 7.4
     - WP 6.4 + PHP 8.2
   - Verificar: activación, upload avatar, visualización, eliminación
   - Documentar resultados

3. **Documentar en acceptance tests** ✅ Marcar como completado si pasa

### Para Post-Publicación (Opcional)

4. **GitHub Actions** - Para future-proofing
   - Setup CI/CD para testing automático
   - Detectar problemas antes de release

5. **Docker Multi-Stage** - Solo si encuentras bugs específicos
   - Debugging profundo de problemas de versión

---

## Script de Validación Rápida

Crear `scripts/test-compatibility.sh`:

```bash
#!/bin/bash

echo "🔍 Testing PHP Compatibility..."
echo ""

echo "📦 PHP 7.4 Compatibility Check..."
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4- src/ --report=summary
PHP74_EXIT=$?

echo ""
echo "📦 PHP 8.0 Compatibility Check..."
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.0- src/ --report=summary
PHP80_EXIT=$?

echo ""
echo "📦 PHP 8.2 Compatibility Check..."
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.2- src/ --report=summary
PHP82_EXIT=$?

echo ""
echo "================================"
echo "Compatibility Test Results:"
echo "================================"

if [ $PHP74_EXIT -eq 0 ]; then
    echo "✅ PHP 7.4: PASS"
else
    echo "❌ PHP 7.4: FAIL"
fi

if [ $PHP80_EXIT -eq 0 ]; then
    echo "✅ PHP 8.0: PASS"
else
    echo "❌ PHP 8.0: FAIL"
fi

if [ $PHP82_EXIT -eq 0 ]; then
    echo "✅ PHP 8.2: PASS"
else
    echo "❌ PHP 8.2: FAIL"
fi

echo ""
if [ $PHP74_EXIT -eq 0 ] && [ $PHP80_EXIT -eq 0 ] && [ $PHP82_EXIT -eq 0 ]; then
    echo "🎉 All PHP compatibility tests PASSED!"
    exit 0
else
    echo "⚠️  Some compatibility tests FAILED. Review output above."
    exit 1
fi
```

**Uso:**
```bash
chmod +x scripts/test-compatibility.sh
./scripts/test-compatibility.sh
```

---

## Para WordPress Compatibility

**Verificación Manual Mínima (WordPress Playground):**

1. **Ir a:** https://playground.wordpress.net/
2. **Seleccionar versión:** Menú → PHP/WP version
3. **Probar matriz:**
   - WP 5.8 + PHP 7.4 → Upload avatar JPG → Remove → Ver en comentarios
   - WP 6.0 + PHP 8.0 → Upload avatar JPG → Remove → Ver en comentarios
   - WP 6.4 + PHP 8.2 → Upload avatar JPG → Remove → Ver en comentarios

4. **Criterio de éxito:**
   - ✅ Plugin activa sin errores
   - ✅ Upload funciona
   - ✅ Avatar se visualiza
   - ✅ Remove funciona con fallback a iniciales

**Tiempo total: 15 minutos** (5 min por versión)

---

## Documentación de Resultados

Después de las pruebas, documentar en `docs/testing/compatibility-results.md`:

```markdown
# Compatibility Test Results

## PHP Compatibility (Static Analysis)
- ✅ PHP 7.4: No issues found
- ✅ PHP 8.0: No issues found
- ✅ PHP 8.2: No issues found

## WordPress Compatibility (Manual Testing)
### WP 5.8 + PHP 7.4
- ✅ Plugin activation successful
- ✅ Avatar upload working
- ✅ Avatar display in comments working
- ✅ Avatar removal with fallback working

### WP 6.0 + PHP 8.0
- ✅ Plugin activation successful
- ✅ Avatar upload working
- ✅ Avatar display in comments working
- ✅ Avatar removal with fallback working

### WP 6.4 + PHP 8.2
- ✅ Plugin activation successful
- ✅ Avatar upload working
- ✅ Avatar display in comments working
- ✅ Avatar removal with fallback working

**Conclusion:** Avatar Steward is fully compatible with WordPress 5.8-6.4+ and PHP 7.4-8.2+
```

---

## Resumen: Camino Más Rápido

### ⚡ Testing Ágil (20 minutos)

1. **Ahora (5 min):** Ejecutar análisis estático con PHPCompatibility
2. **Ahora (15 min):** Testing manual en WordPress Playground
3. **Después:** Marcar tests de compatibilidad como ✅ en acceptance tests

### 🚀 Para Futuro (30 min setup)

4. **Post-publicación:** Setup GitHub Actions para testing automático continuo

**No necesitas Docker adicional ni instalaciones locales complejas.** El análisis estático + playground es suficiente para MVP.

¿Quieres que ejecute el análisis estático de compatibilidad PHP ahora mismo?
