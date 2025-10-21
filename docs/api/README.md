# Avatar Steward API Documentation

This directory contains comprehensive documentation for all Avatar Steward APIs and integration points.

## Available APIs

### [Visual Identity API](visual-identity.md)
REST API for accessing color palettes and visual styles used throughout the plugin.

**Features:**
- Versioned REST endpoints (v1)
- Public access (read-only)
- Built-in caching
- Color palettes for avatars, UI, and status indicators
- Style configurations for avatars, typography, and layout
- Extensible via WordPress filters

**Base URL:** `/wp-json/avatar-steward/v1/visual-identity`

**Common Use Cases:**
- Maintain consistent styling across custom themes
- Generate CSS variables dynamically
- Create custom avatar galleries
- Build complementary UI components

### [Social Integrations API](integrations.md)
Developer documentation for extending the social integrations system.

**Features:**
- Strategy Pattern architecture
- Custom provider registration
- OAuth 2.0 implementation examples
- WordPress hooks and filters
- Token management utilities

**Common Use Cases:**
- Add new social providers (LinkedIn, Instagram, etc.)
- Customize existing provider behavior
- Track social connection events
- Build custom import workflows

## Quick Start

### Accessing Visual Identity Data

**JavaScript:**
```javascript
fetch('/wp-json/avatar-steward/v1/visual-identity')
  .then(response => response.json())
  .then(data => {
    console.log('Visual Identity:', data);
  });
```

**PHP:**
```php
$response = wp_remote_get(home_url('/wp-json/avatar-steward/v1/visual-identity'));
$data = json_decode(wp_remote_retrieve_body($response), true);
```

### Direct Service Access (Within Plugin)

```php
use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;

$service = new VisualIdentityService();
$palettes = $service->get_palettes();
$styles = $service->get_styles();
```

## API Versioning

All REST APIs follow semantic versioning:
- **Current Version:** v1
- **Stability:** Stable
- **Breaking Changes:** Will be introduced in v2 with appropriate deprecation notices

## Authentication

- **Visual Identity API:** Public endpoints, no authentication required for read operations
- **Cache Management:** Admin authentication required for cache clearing
- **Social Integrations:** OAuth 2.0 for social provider connections

## Rate Limiting

Currently, there are no rate limits on API endpoints. However:
- All responses include cache headers (1 hour)
- Clients should respect cache directives
- Excessive requests may be throttled in future versions

## Error Handling

All APIs follow WordPress REST API error conventions:

```json
{
  "code": "error_code",
  "message": "Human-readable error message",
  "data": {
    "status": 404
  }
}
```

## Support

For API questions or issues:
1. Check the specific API documentation
2. Review code examples in this directory
3. Refer to the main [plugin documentation](../../README.md)
4. Submit issues via the plugin's support channels

## Contributing

When adding new APIs:
1. Create comprehensive documentation in this directory
2. Include code examples for common use cases
3. Document all endpoints, parameters, and responses
4. Add examples for JavaScript, PHP, and CSS where applicable
5. Update this README with links to new documentation

---

**Last Updated:** October 2025  
**Plugin Version:** 0.1.0
