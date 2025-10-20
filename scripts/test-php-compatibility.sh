#!/bin/bash

echo "🔍 Avatar Steward - PHP Compatibility Analysis"
echo "=============================================="
echo ""

echo "📦 Testing PHP 7.4+ Compatibility..."
echo "-----------------------------------"
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4- src/
PHP74_EXIT=$?
echo ""

echo "📦 Testing PHP 8.0+ Compatibility..."
echo "-----------------------------------"
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.0- src/
PHP80_EXIT=$?
echo ""

echo "📦 Testing PHP 8.2+ Compatibility..."
echo "-----------------------------------"
vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 8.2- src/
PHP82_EXIT=$?
echo ""

echo "=============================================="
echo "📊 Compatibility Test Results Summary"
echo "=============================================="

if [ $PHP74_EXIT -eq 0 ]; then
    echo "✅ PHP 7.4+: PASS - No compatibility issues found"
else
    echo "❌ PHP 7.4+: FAIL - Review errors above"
fi

if [ $PHP80_EXIT -eq 0 ]; then
    echo "✅ PHP 8.0+: PASS - No compatibility issues found"
else
    echo "❌ PHP 8.0+: FAIL - Review errors above"
fi

if [ $PHP82_EXIT -eq 0 ]; then
    echo "✅ PHP 8.2+: PASS - No compatibility issues found"
else
    echo "❌ PHP 8.2+: FAIL - Review errors above"
fi

echo ""
if [ $PHP74_EXIT -eq 0 ] && [ $PHP80_EXIT -eq 0 ] && [ $PHP82_EXIT -eq 0 ]; then
    echo "🎉 All PHP compatibility tests PASSED!"
    echo "Avatar Steward is compatible with PHP 7.4, 8.0, 8.1, and 8.2+"
    exit 0
else
    echo "⚠️  Some compatibility tests FAILED."
    echo "Please review the errors above and fix compatibility issues."
    exit 1
fi
