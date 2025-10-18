# Initials Avatar Generator

## Overview

The `AvatarSteward\Domain\Initials\Generator` class creates SVG avatars with user initials when no uploaded image is available. This provides a personalized fallback avatar for users without requiring file uploads.

## Features

### Core Functionality
- **SVG Generation**: Creates lightweight, scalable vector graphics
- **Initials Extraction**: Intelligently extracts 1-2 characters from user names
- **Consistent Colors**: Uses hash-based selection to ensure same name = same color
- **High Performance**: Average generation time of ~0.007ms (requirement: < 10ms)

### Edge Case Handling
- Empty names → `?`
- Single word names → First 2 characters
- Multiple words → First character of first + last word
- Unicode support (José, 李明, etc.)
- Special characters filtered out
- Extra whitespace normalized

### Configuration
- **Color Palette**: 15 accessible colors with good contrast (customizable)
- **Font Family**: Default Arial, customizable
- **Text Color**: Default white, customizable
- **Size Range**: 32px (min) to 512px (max)
- **Default Size**: 96px (configurable)

## Usage

### Basic Usage

```php
use AvatarSteward\Domain\Initials\Generator;

// Create generator with default settings
$generator = new Generator();

// Generate 96x96 avatar
$svg = $generator->generate('John Doe');

// Generate custom size avatar
$svg = $generator->generate('Jane Smith', 128);

// Output SVG directly
header('Content-Type: image/svg+xml');
echo $svg;
```

### Extract Initials Only

```php
$generator = new Generator();

$initials = $generator->extract_initials('John Doe');        // 'JD'
$initials = $generator->extract_initials('Madonna');         // 'MA'
$initials = $generator->extract_initials('José García');     // 'JG'
$initials = $generator->extract_initials('李明');             // '李明'
$initials = $generator->extract_initials('');                // '?'
```

### Get Consistent Color

```php
$generator = new Generator();

// Same name always returns same color
$color = $generator->get_color_for_name('John Doe'); // '#2c3e50'
$color = $generator->get_color_for_name('john doe'); // '#2c3e50' (case-insensitive)
```

### Custom Configuration

```php
$generator = new Generator([
    'color_palette' => [
        '#ff0000',
        '#00ff00',
        '#0000ff',
    ],
    'font_family'   => 'Courier New, monospace',
    'text_color'    => '#000000',
    'default_size'  => 200,
]);

$svg = $generator->generate('Custom User');
```

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `color_palette` | `array` | 15 colors | Array of hex color codes for backgrounds |
| `font_family` | `string` | `'Arial, Helvetica, sans-serif'` | CSS font-family value |
| `text_color` | `string` | `'#ffffff'` | Hex color code for text |
| `default_size` | `int` | `96` | Default size when not specified in generate() |

## Default Color Palette

The generator includes 15 accessible colors with good contrast ratios:

```php
[
    '#1abc9c', // Turquoise
    '#2ecc71', // Emerald
    '#3498db', // Peter River
    '#9b59b6', // Amethyst
    '#34495e', // Wet Asphalt
    '#16a085', // Green Sea
    '#27ae60', // Nephritis
    '#2980b9', // Belize Hole
    '#8e44ad', // Wisteria
    '#2c3e50', // Midnight Blue
    '#f39c12', // Orange
    '#e74c3c', // Alizarin
    '#c0392b', // Pomegranate
    '#d35400', // Pumpkin
    '#7f8c8d', // Asbestos
]
```

## Size Constraints

Sizes are automatically constrained to the valid range:

```php
$generator = new Generator();

$svg = $generator->generate('User', 16);    // → 32px (min)
$svg = $generator->generate('User', 32);    // → 32px
$svg = $generator->generate('User', 96);    // → 96px
$svg = $generator->generate('User', 512);   // → 512px
$svg = $generator->generate('User', 1024);  // → 512px (max)
```

## Performance

The generator is highly optimized for performance:

- **Average generation time**: ~0.007ms
- **1000 generations**: ~7ms total
- **Requirement**: < 10ms per generation ✓ **PASSED**

Performance characteristics:
- No file I/O operations
- Simple string operations
- Fast hash-based color selection (crc32)
- Minimal memory footprint

## Security

The generator includes proper security measures:

- **XML/SVG Escaping**: All user input is escaped using `htmlspecialchars()` with `ENT_XML1 | ENT_QUOTES`
- **XSS Prevention**: Special characters in names are properly encoded
- **No External Resources**: All generation is done in-memory
- **Input Validation**: Sizes are validated and constrained

## Examples

### WordPress Integration

```php
add_filter('pre_get_avatar_data', function($args, $id_or_email) {
    $generator = new Generator();
    
    // Get user
    $user = null;
    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
        $user = get_user_by('id', $id_or_email->user_id);
    }
    
    if ($user) {
        $svg = $generator->generate($user->display_name, $args['size']);
        $args['url'] = 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    return $args;
}, 10, 2);
```

### REST API Endpoint

```php
register_rest_route('avatarsteward/v1', '/avatar/initials', [
    'methods'  => 'GET',
    'callback' => function($request) {
        $generator = new Generator();
        
        $name = $request->get_param('name') ?? '';
        $size = (int) ($request->get_param('size') ?? 96);
        
        $svg = $generator->generate($name, $size);
        
        return new WP_REST_Response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    },
    'args' => [
        'name' => [
            'required' => true,
            'type' => 'string',
        ],
        'size' => [
            'required' => false,
            'type' => 'integer',
            'default' => 96,
        ],
    ],
]);
```

## Testing

The generator includes comprehensive unit tests:

- **32 tests** covering all functionality
- **47 assertions** validating behavior
- **100% code coverage** for critical paths

Run tests:

```bash
composer test

# Run only Generator tests
vendor/bin/phpunit --filter GeneratorTest

# Run with coverage
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

### Test Coverage

- ✅ Initials extraction (all edge cases)
- ✅ Color consistency and palette validation
- ✅ Size validation and constraints
- ✅ Configuration options
- ✅ XML/SVG escaping and security
- ✅ Performance validation
- ✅ SVG validity (well-formed XML)
- ✅ Unicode and special character handling

## API Reference

### Generator Class

#### Constructor

```php
public function __construct(array $config = [])
```

Creates a new Generator instance with optional configuration.

**Parameters:**
- `$config` (array): Optional configuration array

#### generate()

```php
public function generate(string $name, int $size = 0): string
```

Generates an SVG avatar from a name.

**Parameters:**
- `$name` (string): The user's display name
- `$size` (int): Size in pixels (0 = use default)

**Returns:**
- (string): SVG markup

#### extract_initials()

```php
public function extract_initials(string $name): string
```

Extracts initials from a name.

**Parameters:**
- `$name` (string): The user's display name

**Returns:**
- (string): Extracted initials (1-2 characters)

#### get_color_for_name()

```php
public function get_color_for_name(string $name): string
```

Gets a consistent color for a name.

**Parameters:**
- `$name` (string): The user's display name

**Returns:**
- (string): Hex color code

#### Getters

```php
public function get_color_palette(): array
public function get_font_family(): string
public function get_min_size(): int
public function get_max_size(): int
```

## License

GPL-2.0-or-later

## Related

- [Avatar Steward Plugin Documentation](../../README.md)
- [Architecture Documentation](../../../documentacion/13_Arquitectura.md)
- [Development Guide](../../../documentacion/06_Guia_de_Desarrollo.md)
