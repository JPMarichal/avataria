#!/bin/bash

# Avatar Steward - Plugin Package Generator
# Creates a clean ZIP file for WordPress.org submission and testing

echo "🎯 Avatar Steward - Plugin Package Generator"
echo "=========================================="
echo ""

# Configuration
PLUGIN_NAME="avatar-steward"
VERSION="0.1.0"
OUTPUT_DIR="dist"
ZIP_NAME="${PLUGIN_NAME}-${VERSION}.zip"

# Clean previous builds
echo "🧹 Cleaning previous builds..."
rm -rf "$OUTPUT_DIR"
rm -f "$ZIP_NAME"

# Create output directory
mkdir -p "$OUTPUT_DIR"

# Copy plugin files (excluding development files)
echo "📦 Copying plugin files..."

# Core plugin files
cp -r avatar-steward.php "$OUTPUT_DIR/"
cp -r src/ "$OUTPUT_DIR/"
cp -r assets/ "$OUTPUT_DIR/"
cp -r languages/ "$OUTPUT_DIR/" 2>/dev/null || true
cp -r templates/ "$OUTPUT_DIR/" 2>/dev/null || true

# Documentation (only essential files)
mkdir -p "$OUTPUT_DIR/docs"
cp README.md "$OUTPUT_DIR/"
cp CHANGELOG.md "$OUTPUT_DIR/"
cp LICENSE.txt "$OUTPUT_DIR/"

# Exclude development files
echo "🚫 Excluding development files..."
cd "$OUTPUT_DIR"

# Remove development directories
rm -rf vendor/
rm -rf node_modules/
rm -rf tests/
rm -rf .git/
rm -rf .github/
rm -rf docker/
rm -rf docker_logs/
rm -rf design/
rm -rf examples/
rm -rf scripts/
rm -rf simple-local-avatars/
rm -rf wp-config.php/

# Remove development files
find . -name "*.log" -delete
find . -name ".DS_Store" -delete
find . -name "Thumbs.db" -delete
find . -name "*.tmp" -delete
find . -name "*.bak" -delete
find . -name "composer.*" -delete
find . -name "package*.json" -delete
find . -name "phpunit*.xml*" -delete
find . -name "phpcs.xml*" -delete
find . -name "webpack.config.js" -delete
find . -name "test-generator.php" -delete
find . -name "*.md" -not -name "README.md" -not -name "CHANGELOG.md" -delete

# Remove docs directory (keep only essential docs)
rm -rf docs/

cd ..

# Create ZIP file
echo "📦 Creating ZIP package..."
if command -v zip >/dev/null 2>&1; then
    cd "$OUTPUT_DIR"
    zip -r "../$ZIP_NAME" ./*
    cd ..
else
    echo "⚠️  zip command not found. Creating tar.gz instead..."
    tar -czf "$ZIP_NAME" -C "$OUTPUT_DIR" .
fi

# Verify package
echo "✅ Verifying package..."
if [ -f "$ZIP_NAME" ]; then
    SIZE=$(du -h "$ZIP_NAME" | cut -f1)
    echo "📊 Package created: $ZIP_NAME ($SIZE)"

    # List contents
    echo "📋 Package contents:"
    if command -v unzip >/dev/null 2>&1; then
        unzip -l "$ZIP_NAME" | head -20
    elif command -v tar >/dev/null 2>&1; then
        tar -tzf "$ZIP_NAME" | head -20
    fi
else
    echo "❌ Failed to create package"
    exit 1
fi

echo ""
echo "🎉 Package generation complete!"
echo "📁 Location: $(pwd)/$ZIP_NAME"
echo ""
echo "📝 Next steps:"
echo "1. Test the ZIP in WordPress Playground: https://playground.wordpress.net/"
echo "2. Upload to WordPress.org when ready for submission"
echo "3. Keep this ZIP for CodeCanyon submission (Pro version)"

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
