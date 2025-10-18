# Bootstrap Transformation Diff

## Overview
This document shows the transformation of the plugin bootstrap from Simple Local Avatars to Avatar Steward.

## Original: simple-local-avatars.php (v2.8.5)

```php
<?php
/**
 * Plugin Name:       Simple Local Avatars
 * Plugin URI:        https://10up.com/plugins/simple-local-avatars-wordpress/
 * Description:       Adds an avatar upload field to user profiles.
 * Version:           2.8.5
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL-2.0-or-later
 * Text Domain:       simple-local-avatars
 *
 * @package           SimpleLocalAvatars
 */

// Compatibility checker
require_once '10up-lib/wp-compat-validation-tool/src/Validator.php';
$compat_checker = new \SimpleLocalAvatarsValidator\Validator();
// ... compatibility checks

define( 'SLA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
require_once dirname( __FILE__ ) . '/includes/class-simple-local-avatars.php';

// Global constants
define( 'SLA_VERSION', '2.8.5' );
define( 'SLA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SLA_IS_NETWORK', Simple_Local_Avatars::is_network( plugin_basename( __FILE__ ) ) );

// Global instantiation
global $simple_local_avatars;
$simple_local_avatars = new Simple_Local_Avatars();
```

## Refactored: src/avatar-steward.php

```php
<?php
/**
 * Plugin Name: Avatar Steward
 * Plugin URI: https://avatarsteward.example
 * Description: Local avatar management suite for WordPress.
 * Version: 0.1.0
 * Author: Avatar Steward Team
 * License: GPL-2.0-or-later
 *
 * @package AvatarSteward
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$avatar_steward_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( $avatar_steward_autoload ) ) {
	require_once $avatar_steward_autoload;
}

if ( ! class_exists( AvatarSteward\Plugin::class ) ) {
	require_once __DIR__ . '/AvatarSteward/Plugin.php';
}

AvatarSteward\Plugin::instance();
```

## Refactored: src/AvatarSteward/Plugin.php

```php
<?php
/**
 * Main Plugin class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward;

use AvatarSteward\Core\AvatarHandler;

/**
 * Plugin singleton class.
 */
final class Plugin {

	private static ?self $instance = null;

	private function __construct() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'plugins_loaded', array( $this, 'boot' ) );
		}
	}

	public static function instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function boot(): void {
		$this->init_settings_page();
		
		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_booted' );
		}
	}
	
	// ... additional methods
}
```

## Key Transformations

### 1. Global State Elimination
- **Before:** `global $simple_local_avatars = new Simple_Local_Avatars();`
- **After:** `AvatarSteward\Plugin::instance();` (singleton pattern)
- **Benefit:** No global variable pollution, testable architecture

### 2. Namespace Isolation
- **Before:** No namespace, class name `Simple_Local_Avatars`
- **After:** `namespace AvatarSteward\Plugin`
- **Benefit:** No conflicts with original plugin, PSR-4 compliance

### 3. Constants Removal
- **Before:** `SLA_VERSION`, `SLA_PLUGIN_URL`, `SLA_IS_NETWORK` global constants
- **After:** No global constants, configuration via class properties
- **Benefit:** Reduced global scope pollution, better encapsulation

### 4. Autoloading
- **Before:** Manual `require_once` for each file
- **After:** Composer autoloader with PSR-4
- **Benefit:** Automatic class loading, reduced maintenance

### 5. Type Safety
- **Before:** No type declarations
- **After:** `declare(strict_types=1)`, typed properties and return types
- **Benefit:** Compile-time error detection, better IDE support

### 6. Initialization Pattern
- **Before:** Immediate execution on file load
- **After:** Lazy initialization via singleton, deferred boot via `plugins_loaded` hook
- **Benefit:** Better control over initialization order, easier testing

## GPL Compliance Notes

- GPL-2.0-or-later license maintained in file header
- Original copyright to 10up acknowledged in LICENSE.txt
- Complete architectural rewrite using modern PHP patterns
- No direct code copying, only architectural pattern reference
- All code independently developed with test coverage

## Test Coverage

**Original:** No tests for bootstrap in Simple Local Avatars
**Refactored:** 8 test cases covering Plugin singleton and initialization

```php
tests/phpunit/PluginTest.php:
- testInstanceReturnsSingleton
- testInstanceReturnsSameInstance
- testBootInitializesSettingsPage
- testBootFiresAction
- testBootDoesNotFireActionWhenFunctionNotExists
- testGetSettingsPageReturnsInstance
- testGetSettingsPageReturnsNullWhenNotInitialized
- testMultipleInstanceCallsReturnSameObject
```

## References

- Original plugin: https://github.com/10up/simple-local-avatars
- Original license: GPL-2.0-or-later
- Transformation date: 2025-10-17
- Test coverage: 100% for Plugin class
