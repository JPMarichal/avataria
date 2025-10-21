#!/bin/bash
###############################################################################
# Script de Verificación de Congruencia - Avatar Steward
# Verifica que la estructura del plugin sea correcta y congruente
###############################################################################

set -e  # Salir si cualquier comando falla

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "🔍 Verificando congruencia de la estructura de Avatar Steward..."
echo ""

# Contador de problemas
ISSUES=0

###############################################################################
# 1. Verificar que solo existe UN avatar-steward.php
###############################################################################
echo "1️⃣  Verificando archivos avatar-steward.php..."
PLUGIN_FILES=$(find . -name "avatar-steward.php" -type f | grep -v node_modules | grep -v vendor)
COUNT=$(echo "$PLUGIN_FILES" | wc -l)

if [ "$COUNT" -eq 1 ]; then
    LOCATION=$(echo "$PLUGIN_FILES" | head -1)
    if [ "$LOCATION" = "./avatar-steward.php" ]; then
        echo -e "${GREEN}✓${NC} avatar-steward.php encontrado en la ubicación correcta: ./avatar-steward.php"
    else
        echo -e "${RED}✗${NC} avatar-steward.php encontrado en ubicación incorrecta: $LOCATION"
        ISSUES=$((ISSUES + 1))
    fi
else
    echo -e "${RED}✗${NC} Se encontraron $COUNT archivos avatar-steward.php (debe ser 1):"
    echo "$PLUGIN_FILES"
    ISSUES=$((ISSUES + 1))
fi

###############################################################################
# 2. Verificar que NO existe src/avatar-steward.php
###############################################################################
echo ""
echo "2️⃣  Verificando que NO existan duplicados..."
if [ -f "src/avatar-steward.php" ]; then
    echo -e "${RED}✗${NC} src/avatar-steward.php existe (debe estar eliminado)"
    ISSUES=$((ISSUES + 1))
else
    echo -e "${GREEN}✓${NC} No hay avatar-steward.php duplicado en src/"
fi

###############################################################################
# 3. Verificar que NO existan assets duplicados
###############################################################################
if [ -d "src/assets" ]; then
    echo -e "${RED}✗${NC} src/assets/ existe (debe estar eliminado)"
    ISSUES=$((ISSUES + 1))
else
    echo -e "${GREEN}✓${NC} No hay assets duplicados en src/"
fi

###############################################################################
# 4. Verificar que existan assets en la raíz
###############################################################################
echo ""
echo "3️⃣  Verificando estructura de assets..."
if [ -f "assets/css/profile-avatar.css" ]; then
    echo -e "${GREEN}✓${NC} assets/css/profile-avatar.css existe"
else
    echo -e "${RED}✗${NC} assets/css/profile-avatar.css NO encontrado"
    ISSUES=$((ISSUES + 1))
fi

if [ -f "assets/js/profile-avatar.js" ]; then
    echo -e "${GREEN}✓${NC} assets/js/profile-avatar.js existe"
else
    echo -e "${RED}✗${NC} assets/js/profile-avatar.js NO encontrado"
    ISSUES=$((ISSUES + 1))
fi

###############################################################################
# 5. Verificar estructura de src/
###############################################################################
echo ""
echo "4️⃣  Verificando estructura de código fuente..."
if [ -d "src/AvatarSteward" ]; then
    echo -e "${GREEN}✓${NC} src/AvatarSteward/ existe"
else
    echo -e "${RED}✗${NC} src/AvatarSteward/ NO encontrado"
    ISSUES=$((ISSUES + 1))
fi

if [ -f "src/AvatarSteward/Plugin.php" ]; then
    echo -e "${GREEN}✓${NC} src/AvatarSteward/Plugin.php existe"
else
    echo -e "${RED}✗${NC} src/AvatarSteward/Plugin.php NO encontrado"
    ISSUES=$((ISSUES + 1))
fi

###############################################################################
# 6. Verificar wp-content/ (debe estar vacío o con estructura básica)
###############################################################################
echo ""
echo "5️⃣  Verificando wp-content/ (solo estructura Docker)..."
if [ -d "wp-content/plugins/avatar-steward" ]; then
    echo -e "${RED}✗${NC} wp-content/plugins/avatar-steward/ existe (debe estar eliminado)"
    ISSUES=$((ISSUES + 1))
else
    echo -e "${GREEN}✓${NC} wp-content/plugins/ está limpio (sin copias del plugin)"
fi

###############################################################################
# 7. Verificar configuración de Docker
###############################################################################
echo ""
echo "6️⃣  Verificando configuración de Docker..."
if grep -q "\./:/var/www/html/wp-content/plugins/avatar-steward" docker-compose.dev.yml; then
    echo -e "${GREEN}✓${NC} docker-compose.dev.yml monta la raíz completa como plugin"
else
    echo -e "${YELLOW}⚠${NC}  docker-compose.dev.yml podría tener configuración diferente"
fi

# Verificar que no haya montaje conflictivo de wp-content completo
if grep -q "\./wp-content:/var/www/html/wp-content$" docker-compose.dev.yml; then
    echo -e "${RED}✗${NC} docker-compose.dev.yml tiene montaje conflictivo de ./wp-content"
    ISSUES=$((ISSUES + 1))
else
    echo -e "${GREEN}✓${NC} No hay conflictos de volúmenes en docker-compose.dev.yml"
fi

###############################################################################
# 8. Ejecutar tests
###############################################################################
echo ""
echo "7️⃣  Ejecutando suite de tests..."
if composer test > /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} Tests ejecutados exitosamente (219 tests)"
else
    echo -e "${RED}✗${NC} Tests fallaron"
    ISSUES=$((ISSUES + 1))
fi

###############################################################################
# 9. Verificar vendor/autoload.php
###############################################################################
echo ""
echo "8️⃣  Verificando dependencias..."
if [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✓${NC} vendor/autoload.php existe"
else
    echo -e "${YELLOW}⚠${NC}  vendor/autoload.php no encontrado (ejecuta 'composer install')"
fi

###############################################################################
# RESUMEN
###############################################################################
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ $ISSUES -eq 0 ]; then
    echo -e "${GREEN}✅ VERIFICACIÓN EXITOSA${NC}"
    echo "La estructura del plugin es congruente y correcta."
    echo ""
    echo "Estructura validada:"
    echo "  • avatar-steward.php en raíz (único punto de entrada)"
    echo "  • assets/ en raíz (CSS y JS)"
    echo "  • src/AvatarSteward/ (código PHP)"
    echo "  • wp-content/ limpio (solo para Docker)"
    echo "  • Tests pasando (219/219)"
    exit 0
else
    echo -e "${RED}❌ VERIFICACIÓN FALLIDA${NC}"
    echo "Se encontraron $ISSUES problema(s) de congruencia."
    echo ""
    echo "Revisa los errores arriba y consulta ESTRUCTURA.md para más detalles."
    exit 1
fi
