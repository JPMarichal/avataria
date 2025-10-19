# Logger System Documentation

## Overview

Avatar Steward includes a comprehensive logging system designed for debugging and problem detection throughout the plugin. The logger follows PSR-3 style logging conventions and integrates seamlessly with WordPress debug mechanisms.

## Features

- **PSR-3 Compatible Interface**: Standard log levels (emergency, alert, critical, error, warning, notice, info, debug)
- **WordPress Integration**: Respects `WP_DEBUG` and `WP_DEBUG_LOG` constants
- **Context Support**: Log structured data alongside messages for better debugging
- **Configurable Levels**: Set minimum log level to control verbosity
- **Hook Integration**: Provides `avatarsteward_log` action hook for custom handling
- **Zero Performance Impact**: When disabled, logging has no performance overhead

## Usage

### Basic Example

```php
use AvatarSteward\Infrastructure\Logger;

// Create a logger instance
$logger = new Logger();

// Log at different levels
$logger->debug('Debug information');
$logger->info('Something informative happened');
$logger->warning('Warning: potential issue');
$logger->error('An error occurred');
$logger->critical('Critical system failure');
```

### Logging with Context

Add structured context data to your log messages:

```php
$logger->info(
    'User avatar uploaded',
    array(
        'user_id'       => 123,
        'attachment_id' => 456,
        'file_size'     => 102400,
        'mime_type'     => 'image/jpeg',
    )
);
```

### Dependency Injection

The logger can be injected into services via constructor:

```php
use AvatarSteward\Infrastructure\LoggerInterface;

class UploadService {
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    public function process_upload(array $file, int $user_id): array {
        if ($this->logger) {
            $this->logger->info('Processing upload', array('user_id' => $user_id));
        }
        // ... upload logic
    }
}
```

## Configuration

### Minimum Log Level

Control which messages are logged by setting a minimum level:

```php
// Only log warnings and above (warning, error, critical, alert, emergency)
$logger = new Logger('warning');
```

Available levels in order of severity (lowest to highest):
- `debug` - Detailed debug information
- `info` - Interesting events
- `notice` - Normal but significant events
- `warning` - Exceptional occurrences that are not errors
- `error` - Runtime errors
- `critical` - Critical conditions
- `alert` - Action must be taken immediately
- `emergency` - System is unusable

### Enabling/Disabling

The logger automatically respects WordPress debug settings:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true); // Logs to wp-content/debug.log
```

You can also explicitly control the logger state:

```php
$logger = new Logger('debug', true);  // Explicitly enabled
$logger = new Logger('debug', false); // Explicitly disabled

// Or change state after instantiation
$logger->set_enabled(true);
$logger->set_enabled(false);
```

## Log Format

Log entries are formatted as:

```
[Avatar Steward] [LEVEL] Message | Context: {"key":"value"}
```

Example output:
```
[Avatar Steward] [INFO] User avatar uploaded | Context: {"user_id":123,"attachment_id":456,"file_size":102400}
[Avatar Steward] [ERROR] Failed to create attachment | Context: {"error":"Unable to write file","user_id":789}
```

## WordPress Integration

### Using WordPress Error Log

When `WP_DEBUG_LOG` is enabled, all logs are automatically written to the WordPress debug log file (typically `wp-content/debug.log`).

### Custom Log Handling

You can add custom log handlers using the `avatarsteward_log` action hook:

```php
add_action('avatarsteward_log', function($level, $message, $context) {
    // Send critical errors to an external monitoring service
    if (in_array($level, array('critical', 'emergency', 'alert'))) {
        send_to_monitoring_service($level, $message, $context);
    }
    
    // Store logs in database for admin review
    if ($level === 'error') {
        store_error_in_database($message, $context);
    }
}, 10, 3);
```

## Best Practices

### 1. Use Appropriate Log Levels

- **Debug**: Detailed diagnostic information for developers
- **Info**: General informational messages about normal operations
- **Warning**: Something unexpected happened but not necessarily an error
- **Error**: Runtime errors that should be investigated
- **Critical/Alert/Emergency**: Severe issues requiring immediate attention

### 2. Include Relevant Context

Always provide context data when logging:

```php
// Good: Provides context for debugging
$logger->error(
    'Avatar upload failed',
    array(
        'user_id'    => $user_id,
        'error_code' => $error_code,
        'file_size'  => $file_size,
    )
);

// Bad: No context
$logger->error('Avatar upload failed');
```

### 3. Log at Entry and Exit Points

Log when entering and exiting critical operations:

```php
public function migrate_from_gravatar(bool $force = false): array {
    if ($this->logger) {
        $this->logger->info('Starting Gravatar migration', array('force' => $force));
    }
    
    // ... migration logic
    
    if ($this->logger) {
        $this->logger->info('Gravatar migration completed', array(
            'imported' => $imported,
            'skipped'  => $skipped,
            'failed'   => $failed,
        ));
    }
}
```

### 4. Avoid Logging Sensitive Data

Never log passwords, API keys, or other sensitive information:

```php
// Bad: Logs sensitive data
$logger->debug('User login', array('password' => $password));

// Good: Omits sensitive data
$logger->debug('User login attempt', array('username' => $username));
```

### 5. Check Logger Existence

Since the logger is optional, always check before using:

```php
if ($this->logger) {
    $this->logger->info('Operation completed');
}
```

## Testing

The logger is fully tested with comprehensive unit tests. See `tests/phpunit/Infrastructure/LoggerTest.php` for examples.

### Testing with Logger

When testing services that use the logger, you can:

1. Pass `null` to disable logging in tests:
   ```php
   $service = new UploadService(2097152, 2000, 2000, array(), null);
   ```

2. Pass a test logger to capture logs:
   ```php
   $logger = new Logger('debug', true);
   $service = new UploadService(2097152, 2000, 2000, array(), $logger);
   ```

3. Use the action hook to capture logs in tests:
   ```php
   $logged = array();
   add_action('avatarsteward_log', function($level, $message, $context) use (&$logged) {
       $logged[] = compact('level', 'message', 'context');
   }, 10, 3);
   ```

## Performance Considerations

- When disabled, the logger has zero performance impact
- Log messages are only formatted when they will actually be logged
- Context data is only serialized when needed
- No I/O operations occur when `WP_DEBUG_LOG` is disabled

## Examples in the Codebase

See these files for real-world logger usage examples:

- `src/AvatarSteward/Domain/Uploads/UploadService.php` - File upload logging
- `src/AvatarSteward/Domain/Migration/MigrationService.php` - Migration process logging

## Troubleshooting

### Logs Not Appearing

1. Verify `WP_DEBUG` is enabled in `wp-config.php`
2. Verify `WP_DEBUG_LOG` is enabled
3. Check that the minimum log level allows your message
4. Ensure the logger instance has `is_enabled()` returning `true`
5. Check file permissions on `wp-content/debug.log`

### Too Many Logs

1. Increase the minimum log level:
   ```php
   $logger->set_min_level('warning'); // Only warnings and above
   ```

2. Disable debug logging in production:
   ```php
   // In wp-config.php for production
   define('WP_DEBUG', false);
   define('WP_DEBUG_LOG', false);
   ```

## API Reference

### `LoggerInterface`

Interface defining the logger contract.

#### Methods

- `emergency(string $message, array $context = array()): void`
- `alert(string $message, array $context = array()): void`
- `critical(string $message, array $context = array()): void`
- `error(string $message, array $context = array()): void`
- `warning(string $message, array $context = array()): void`
- `notice(string $message, array $context = array()): void`
- `info(string $message, array $context = array()): void`
- `debug(string $message, array $context = array()): void`
- `log(string $level, string $message, array $context = array()): void`

### `Logger`

Concrete implementation of `LoggerInterface`.

#### Constructor

```php
public function __construct(
    string $min_level = 'debug',
    ?bool $enabled = null
)
```

**Parameters:**
- `$min_level` - Minimum log level to record (default: 'debug')
- `$enabled` - Enable/disable logging (default: auto-detect from `WP_DEBUG`)

#### Additional Methods

- `get_min_level(): string` - Get current minimum log level
- `set_min_level(string $level): void` - Set minimum log level
- `is_enabled(): bool` - Check if logging is enabled
- `set_enabled(bool $enabled): void` - Enable or disable logging

## Changelog

### Version 1.0.0 (Phase 2a)

- Initial implementation of logger system
- PSR-3 compatible interface
- WordPress integration via `WP_DEBUG` and `WP_DEBUG_LOG`
- Context support for structured logging
- Configurable log levels
- Hook integration via `avatarsteward_log` action
- Comprehensive unit test coverage
- Integration into `UploadService` and `MigrationService`
