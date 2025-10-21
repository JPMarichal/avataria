#!/bin/bash

# Avatar Steward - Plugin Package Generator
# Creates a clean ZIP file for WordPress.org/CodeCanyon submission and testing

echo "🎯 Avatar Steward - Plugin Package Generator"
echo "=========================================="
echo ""

# Parse arguments
VERSION=""
NO_DEV=false
PRO_VERSION=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --version)
            VERSION="$2"
            shift 2
            ;;
        --no-dev)
            NO_DEV=true
            shift
            ;;
        --pro)
            PRO_VERSION=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            echo "Usage: $0 [--version VERSION] [--no-dev] [--pro]"
            exit 1
            ;;
    esac
done

# Auto-detect version from plugin file if not provided
if [ -z "$VERSION" ]; then
    VERSION=$(grep "Version:" avatar-steward.php | head -1 | sed 's/.*Version: *\([0-9.]*\).*/\1/')
fi

# Configuration
PLUGIN_NAME="avatar-steward"
OUTPUT_DIR="dist"
TEMP_DIR="$OUTPUT_DIR/$PLUGIN_NAME"
ZIP_NAME="${PLUGIN_NAME}-${VERSION}.zip"

if [ "$PRO_VERSION" = true ]; then
    ZIP_NAME="${PLUGIN_NAME}-pro-${VERSION}.zip"
fi

# Clean previous builds
echo "🧹 Cleaning previous builds..."
rm -rf "$OUTPUT_DIR"
rm -f "$ZIP_NAME"

# Create output directory structure
mkdir -p "$TEMP_DIR"

echo "📦 Copying plugin files..."

# Copy all files first
rsync -av --progress \
    --exclude-from=.distignore \
    --exclude=.distignore \
    --exclude="$OUTPUT_DIR" \
    . "$TEMP_DIR/"

# Handle Composer dependencies
if [ "$NO_DEV" = true ]; then
    echo "📦 Installing production dependencies only..."
    cd "$TEMP_DIR"
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction --quiet
        if [ $? -eq 0 ]; then
            echo "✅ Production dependencies installed"
        else
            echo "⚠️  Warning: composer install failed, but continuing..."
        fi
    fi
    cd ../..
else
    echo "ℹ️  Including vendor directory as-is (dev dependencies included)"
fi

# Copy essential user documentation
echo "📄 Preparing user documentation..."
cd "$TEMP_DIR"
mkdir -p docs
[ -f "../../docs/user-manual.md" ] && cp "../../docs/user-manual.md" docs/ 2>/dev/null || true
[ -f "../../docs/faq.md" ] && cp "../../docs/faq.md" docs/ 2>/dev/null || true
[ -f "../../docs/support.md" ] && cp "../../docs/support.md" docs/ 2>/dev/null || true
if [ "$PRO_VERSION" = true ]; then
    [ -f "../../docs/licensing.md" ] && cp "../../docs/licensing.md" docs/ 2>/dev/null || true
    [ -f "../../docs/licenses-pro.md" ] && cp "../../docs/licenses-pro.md" docs/ 2>/dev/null || true
fi

# Clean up any remaining development artifacts
find . -name ".DS_Store" -delete
find . -name "Thumbs.db" -delete
find . -name "*.log" -delete

cd ../..

# Create ZIP file
echo "📦 Creating ZIP package..."
if command -v zip >/dev/null 2>&1; then
    cd "$OUTPUT_DIR"
    zip -rq "../$ZIP_NAME" "$PLUGIN_NAME"
    cd ..
else
    echo "⚠️  zip command not found. Creating tar.gz instead..."
    tar -czf "$ZIP_NAME" -C "$OUTPUT_DIR" "$PLUGIN_NAME"
fi

# Verify package
echo "✅ Verifying package..."
if [ -f "$ZIP_NAME" ]; then
    SIZE=$(du -h "$ZIP_NAME" | cut -f1)
    echo "📊 Package created: $ZIP_NAME ($SIZE)"

    # List contents
    echo "📋 Package contents (first 30 files):"
    if command -v unzip >/dev/null 2>&1; then
        unzip -l "$ZIP_NAME" | head -35
    elif command -v tar >/dev/null 2>&1; then
        tar -tzf "$ZIP_NAME" | head -30
    fi
    
    # Count files
    if command -v unzip >/dev/null 2>&1; then
        FILE_COUNT=$(unzip -l "$ZIP_NAME" | tail -1 | awk '{print $2}')
        echo "📁 Total files: $FILE_COUNT"
    fi
else
    echo "❌ Failed to create package"
    exit 1
fi

echo ""
echo "🎉 Package generation complete!"
echo "📁 Location: $(pwd)/$ZIP_NAME"
echo ""

if [ "$PRO_VERSION" = true ]; then
    echo "📝 Next steps for Pro version:"
    echo "1. Test the ZIP in a clean WordPress installation"
    echo "2. Verify licensing system works correctly"
    echo "3. Run CodeCanyon validation: ./scripts/validate-codecanyon.sh"
    echo "4. Upload to CodeCanyon when ready"
else
    echo "📝 Next steps:"
    echo "1. Test the ZIP in WordPress Playground: https://playground.wordpress.net/"
    echo "2. Upload to WordPress.org when ready for submission"
fi

# Clean up
echo ""
read -p "🧹 Clean up temporary files? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🧹 Cleaning up..."
    rm -rf "$OUTPUT_DIR"
    echo "✅ Cleanup complete"
fi

echo ""
echo "🚀 Ready for testing!"
