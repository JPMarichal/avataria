#!/bin/bash
##
# Extract JavaScript Dependency Licenses
#
# This script extracts license information from NPM dependencies
# and generates a markdown report suitable for documentation.
#
# Usage: ./extract-js-licenses.sh [--dev] [--output FILE]
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

# Check if npm is available
if ! command -v npm &> /dev/null; then
    echo -e "${RED}Error: npm not found${NC}"
    echo "Please install Node.js and NPM: https://nodejs.org/"
    exit 1
fi

# Check if package.json exists
if [ ! -f "package.json" ]; then
    echo -e "${RED}Error: package.json not found${NC}"
    echo "This script must be run from the project root directory"
    exit 1
fi

# Install license-checker if needed
if ! npm list -g license-checker > /dev/null 2>&1; then
    echo -e "${YELLOW}Installing license-checker globally...${NC}" >&2
    npm install -g license-checker > /dev/null 2>&1 || {
        echo -e "${RED}Failed to install license-checker${NC}" >&2
        echo -e "${YELLOW}Falling back to npm list...${NC}" >&2
        USE_LICENSE_CHECKER=false
    }
else
    USE_LICENSE_CHECKER=true
fi

# Determine header
if [ "$INCLUDE_DEV" = true ]; then
    HEADER="# JavaScript Dependencies (Including Development)"
else
    HEADER="# JavaScript Dependencies (Production Only)"
fi

# Generate report
generate_report() {
    echo "$HEADER"
    echo ""
    echo "Generated on: $(date '+%Y-%m-%d %H:%M:%S')"
    echo ""
    
    # Check if there are any production dependencies
    if [ "$INCLUDE_DEV" = false ]; then
        PROD_DEPS=$(node -e "console.log(Object.keys(require('./package.json').dependencies || {}).length)" 2>/dev/null || echo "0")
        if [ "$PROD_DEPS" -eq 0 ]; then
            echo "## No Production Dependencies"
            echo ""
            echo "Avatar Steward currently has no production JavaScript dependencies."
            echo "All JavaScript is custom-written for this plugin."
            echo ""
            return
        fi
    fi
    
    echo "## Dependency List"
    echo ""
    echo "| Package | Version | License |"
    echo "|---------|---------|---------|"
    
    if [ "$USE_LICENSE_CHECKER" = true ]; then
        # Use license-checker for detailed information
        if [ "$INCLUDE_DEV" = false ]; then
            LICENSE_OUTPUT=$(npx license-checker --production --json 2>/dev/null || echo "{}")
        else
            LICENSE_OUTPUT=$(npx license-checker --json 2>/dev/null || echo "{}")
        fi
        
        # Parse JSON output
        if command -v jq &> /dev/null; then
            echo "$LICENSE_OUTPUT" | jq -r 'to_entries[] | "| \(.key) | \(.value.version // "unknown") | \(.value.licenses // "unknown") |"'
        else
            echo "| _(Install jq for better output formatting)_ | - | - |"
        fi
    else
        # Fallback to npm list
        if [ "$INCLUDE_DEV" = false ]; then
            npm list --prod --depth=0 2>/dev/null | grep -v "^npm" | grep "@" | sed 's/^[├└│─ ]*//g' | awk '{printf "| %s | - | See package.json |\n", $1}'
        else
            npm list --depth=0 2>/dev/null | grep -v "^npm" | grep "@" | sed 's/^[├└│─ ]*//g' | awk '{printf "| %s | - | See package.json |\n", $1}'
        fi
    fi
    
    echo ""
    echo "## License Compatibility Notes"
    echo ""
    echo "- ✅ MIT: GPL-compatible, permissive"
    echo "- ✅ BSD-*: GPL-compatible, permissive"
    echo "- ✅ Apache 2.0: GPL-compatible (with some restrictions)"
    echo "- ✅ ISC: GPL-compatible, similar to MIT"
    echo "- ⚠️  Proprietary: Review required"
    echo ""
    echo "## Verification"
    echo ""
    if [ "$INCLUDE_DEV" = false ]; then
        echo "Run \`npm list --prod --depth=0\` or \`npx license-checker --production\` to verify this information."
    else
        echo "Run \`npm list --depth=0\` or \`npx license-checker\` to verify this information."
    fi
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
if [ "$USE_LICENSE_CHECKER" = true ]; then
    echo -e "${YELLOW}Checking for GPL-incompatible licenses...${NC}" >&2
    
    INCOMPATIBLE=""
    if [ "$INCLUDE_DEV" = false ]; then
        INCOMPATIBLE=$(npx license-checker --production --json 2>/dev/null | grep -iE "proprietary|AGPL-3.0|GPL-3.0" || true)
    else
        INCOMPATIBLE=$(npx license-checker --json 2>/dev/null | grep -iE "proprietary|AGPL-3.0|GPL-3.0" || true)
    fi
    
    if [ -n "$INCOMPATIBLE" ]; then
        echo -e "${RED}⚠️  Warning: Found potentially incompatible licenses!${NC}" >&2
        echo -e "${YELLOW}Please review the dependencies manually${NC}" >&2
    else
        echo -e "${GREEN}✓ No obviously incompatible licenses detected${NC}" >&2
    fi
else
    echo -e "${YELLOW}Install license-checker for automated compatibility checking${NC}" >&2
fi
