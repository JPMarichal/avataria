#!/bin/bash
##
# Extract PHP Dependency Licenses
#
# This script extracts license information from Composer dependencies
# and generates a markdown report suitable for documentation.
#
# Usage: ./extract-php-licenses.sh [--dev] [--output FILE]
#
# @package AvatarSteward
# @license GPL-2.0-or-later
##

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Default values
INCLUDE_DEV=false
OUTPUT_FILE=""

# Parse arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --dev)
            INCLUDE_DEV=true
            shift
            ;;
        --output)
            OUTPUT_FILE="$2"
            shift 2
            ;;
        --help|-h)
            echo "Usage: $0 [--dev] [--output FILE]"
            echo ""
            echo "Options:"
            echo "  --dev          Include development dependencies"
            echo "  --output FILE  Write output to FILE instead of stdout"
            echo "  --help, -h     Show this help message"
            exit 0
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

# Check if composer is available
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Error: composer not found${NC}"
    echo "Please install Composer: https://getcomposer.org/"
    exit 1
fi

# Check if jq is available (for JSON parsing)
if ! command -v jq &> /dev/null; then
    echo -e "${YELLOW}Warning: jq not found, falling back to basic output${NC}"
    USE_JQ=false
else
    USE_JQ=true
fi

# Determine composer command
if [ "$INCLUDE_DEV" = true ]; then
    COMPOSER_CMD="composer show --all"
    HEADER="# PHP Dependencies (Including Development)"
else
    COMPOSER_CMD="composer show --no-dev"
    HEADER="# PHP Dependencies (Production Only)"
fi

# Generate report
generate_report() {
    echo "$HEADER"
    echo ""
    echo "Generated on: $(date '+%Y-%m-%d %H:%M:%S')"
    echo ""
    echo "## Dependency List"
    echo ""
    echo "| Package | Version | License |"
    echo "|---------|---------|---------|"
    
    # Try to use composer licenses command first (most reliable)
    if composer licenses --no-interaction > /dev/null 2>&1; then
        if [ "$INCLUDE_DEV" = false ]; then
            composer licenses --no-dev --format=json 2>/dev/null | \
                jq -r '.[] | "| \(.name) | \(.version) | \(.license | join(", ")) |"' 2>/dev/null || \
                composer licenses --no-dev 2>&1 | grep -E "^\w" | awk '{printf "| %s | %s | %s |\n", $1, $2, $3}'
        else
            composer licenses --format=json 2>/dev/null | \
                jq -r '.[] | "| \(.name) | \(.version) | \(.license | join(", ")) |"' 2>/dev/null || \
                composer licenses 2>&1 | grep -E "^\w" | awk '{printf "| %s | %s | %s |\n", $1, $2, $3}'
        fi
    else
        # Fallback to composer show
        echo "| _(Error: Could not extract license information)_ | - | - |"
    fi
    
    echo ""
    echo "## License Compatibility Notes"
    echo ""
    echo "- ✅ MIT: GPL-compatible, permissive"
    echo "- ✅ BSD-*: GPL-compatible, permissive"
    echo "- ✅ LGPL: GPL-compatible"
    echo "- ✅ Apache 2.0: GPL-compatible (with some restrictions)"
    echo "- ⚠️  Proprietary: Review required"
    echo ""
    echo "## Verification"
    echo ""
    echo "Run \`composer licenses\` to verify this information."
    echo ""
}

# Output to file or stdout
if [ -n "$OUTPUT_FILE" ]; then
    echo -e "${GREEN}Generating license report...${NC}"
    generate_report > "$OUTPUT_FILE"
    echo -e "${GREEN}Report written to: $OUTPUT_FILE${NC}"
else
    generate_report
fi

# Check for GPL-incompatible licenses
echo -e "${YELLOW}Checking for GPL-incompatible licenses...${NC}" >&2
if composer licenses 2>&1 | grep -qE "proprietary|AGPL-3.0|GPL-3.0"; then
    echo -e "${RED}⚠️  Warning: Found potentially incompatible licenses!${NC}" >&2
    echo -e "${YELLOW}Please review the following packages:${NC}" >&2
    composer licenses 2>&1 | grep -E "proprietary|AGPL-3.0|GPL-3.0" >&2
else
    echo -e "${GREEN}✓ All licenses appear GPL-compatible${NC}" >&2
fi
