# Social Integrations API Documentation

This document describes the internal APIs and extension points for the Avatar Steward social integrations system.

## Architecture Overview

The social integrations system is built using the **Strategy Pattern**, allowing easy extension with new social media providers.

### Core Components

```
src/AvatarSteward/Domain/Integrations/
├── SocialProviderInterface.php    # Interface all providers must implement
├── AbstractSocialProvider.php     # Base class with common functionality
├── TwitterProvider.php            # Twitter/X implementation
├── FacebookProvider.php           # Facebook implementation
└── IntegrationService.php         # Service coordinating all providers
```

## Interfaces

### SocialProviderInterface

All social providers must implement this interface:

```php
interface SocialProviderInterface {
    public function get_name(): string;
    public function get_label(): string;
    public function get_authorization_url( int $user_id, string $redirect_url ): ?string;
    public function handle_callback( string $code, int $user_id ): bool;
    public function import_avatar( int $user_id ): bool;
    public function disconnect( int $user_id ): bool;
    public function is_connected( int $user_id ): bool;
    public function is_configured(): bool;
}
```

#### Methods

##### `get_name(): string`
Returns the unique identifier for the provider (e.g., 'twitter', 'facebook').

##### `get_label(): string`
Returns the human-readable display name (e.g., 'Twitter / X', 'Facebook').

##### `get_authorization_url( int $user_id, string $redirect_url ): ?string`
Generates the OAuth authorization URL for the user to connect their account.

**Parameters:**
- `$user_id` - WordPress user ID
- `$redirect_url` - Callback URL after OAuth authorization

**Returns:**
- Authorization URL string, or `null` if provider is not configured

##### `handle_callback( string $code, int $user_id ): bool`
Processes the OAuth callback, exchanges authorization code for access token, and stores the connection.

**Parameters:**
- `$code` - Authorization code from OAuth callback
- `$user_id` - WordPress user ID

**Returns:**
- `true` on success, `false` on failure

##### `import_avatar( int $user_id ): bool`
Downloads the user's profile picture from the social platform and sets it as their WordPress avatar.

**Parameters:**
- `$user_id` - WordPress user ID

**Returns:**
- `true` on success, `false` on failure

##### `disconnect( int $user_id ): bool`
Removes the connection and deletes stored tokens.

**Parameters:**
- `$user_id` - WordPress user ID

**Returns:**
- `true` on success, `false` on failure

##### `is_connected( int $user_id ): bool`
Checks if the user has connected this social provider.

**Parameters:**
- `$user_id` - WordPress user ID

**Returns:**
- `true` if connected, `false` otherwise

##### `is_configured(): bool`
Checks if the provider has valid API credentials configured.

**Returns:**
- `true` if configured, `false` otherwise

## Base Class: AbstractSocialProvider

The `AbstractSocialProvider` class provides common functionality for all providers:

### Protected Methods

#### Token Management

```php
protected function get_access_token( int $user_id ): ?string;
protected function store_access_token( int $user_id, string $token ): bool;
protected function delete_access_token( int $user_id ): bool;
```

#### User Data Management

```php
protected function store_user_data( int $user_id, array $data ): bool;
protected function get_user_data( int $user_id ): ?array;
protected function delete_user_data( int $user_id ): bool;
```

#### HTTP Requests

```php
protected function make_request( string $url, array $args = array() ): ?array;
```

Makes an HTTP request with automatic error handling and JSON decoding.

#### Image Download

```php
protected function download_and_save_image( string $image_url, int $user_id ): ?int;
```

Downloads an image from URL and saves it to WordPress media library.

**Returns:**
- Attachment ID on success, `null` on failure

## Creating a Custom Provider

### Step 1: Create Provider Class

```php
<?php
namespace AvatarSteward\Domain\Integrations;

class LinkedInProvider extends AbstractSocialProvider {
    
    public function __construct() {
        $this->name = 'linkedin';
        $this->label = 'LinkedIn';
    }
    
    public function get_authorization_url( int $user_id, string $redirect_url ): ?string {
        if ( ! $this->is_configured() ) {
            return null;
        }
        
        $client_id = get_option( 'avatarsteward_linkedin_client_id', '' );
        $state = wp_create_nonce( 'linkedin_oauth_' . $user_id );
        
        set_transient( 'avatarsteward_linkedin_state_' . $user_id, $state, HOUR_IN_SECONDS );
        
        $params = array(
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => $redirect_url,
            'scope' => 'r_liteprofile r_emailaddress',
            'state' => $state,
        );
        
        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query( $params );
    }
    
    public function handle_callback( string $code, int $user_id ): bool {
        // Implement OAuth token exchange
        // Store token with $this->store_access_token()
        // Fetch and store user data with $this->store_user_data()
        // Return true on success
    }
    
    public function import_avatar( int $user_id ): bool {
        $token = $this->get_access_token( $user_id );
        if ( ! $token ) {
            return false;
        }
        
        // Fetch profile picture URL from LinkedIn API
        // Download and save image
        $attachment_id = $this->download_and_save_image( $image_url, $user_id );
        
        if ( ! $attachment_id ) {
            return false;
        }
        
        update_user_meta( $user_id, 'avatarsteward_avatar', $attachment_id );
        return true;
    }
    
    public function is_configured(): bool {
        $client_id = get_option( 'avatarsteward_linkedin_client_id', '' );
        $client_secret = get_option( 'avatarsteward_linkedin_client_secret', '' );
        
        return ! empty( $client_id ) && ! empty( $client_secret );
    }
}
```

### Step 2: Register Provider

```php
add_action( 'avatarsteward_register_providers', function( $integration_service ) {
    $integration_service->register_provider( new LinkedInProvider() );
} );
```

### Step 3: Add Settings Fields

Hook into `admin_init` to register settings fields for your provider's credentials:

```php
add_action( 'admin_init', function() {
    register_setting( 'avatar_steward_settings', 'avatarsteward_linkedin_client_id', 'sanitize_text_field' );
    register_setting( 'avatar_steward_settings', 'avatarsteward_linkedin_client_secret', 'sanitize_text_field' );
    
    add_settings_field(
        'linkedin_client_id',
        __( 'LinkedIn Client ID', 'your-plugin' ),
        function() {
            $value = get_option( 'avatarsteward_linkedin_client_id', '' );
            echo '<input type="text" name="avatarsteward_linkedin_client_id" value="' . esc_attr( $value ) . '" class="regular-text" />';
        },
        'avatar-steward',
        'avatar_steward_social_integrations'
    );
    
    // Add similar field for client secret
} );
```

## WordPress Hooks

### Actions

#### `avatarsteward_register_providers`

Fired during integration service initialization. Use this to register custom providers.

**Parameters:**
- `$integration_service` - Instance of `IntegrationService`

**Example:**
```php
add_action( 'avatarsteward_register_providers', function( $service ) {
    $service->register_provider( new MyProvider() );
} );
```

#### `avatarsteward_social_connected`

Fired when a user successfully connects a social account.

**Parameters:**
- `$user_id` (int) - WordPress user ID
- `$provider_name` (string) - Provider identifier (e.g., 'twitter')

**Example:**
```php
add_action( 'avatarsteward_social_connected', function( $user_id, $provider_name ) {
    error_log( "User $user_id connected $provider_name" );
}, 10, 2 );
```

#### `avatarsteward_social_disconnected`

Fired when a user disconnects a social account.

**Parameters:**
- `$user_id` (int) - WordPress user ID
- `$provider_name` (string) - Provider identifier

#### `avatarsteward_avatar_imported`

Fired when an avatar is successfully imported from a social platform.

**Parameters:**
- `$user_id` (int) - WordPress user ID
- `$provider_name` (string) - Provider identifier
- `$attachment_id` (int) - WordPress attachment ID of imported image

**Example:**
```php
add_action( 'avatarsteward_avatar_imported', function( $user_id, $provider, $attachment_id ) {
    // Send notification, log event, etc.
}, 10, 3 );
```

## Database Schema

### User Meta Keys

For each provider, the following meta keys are used:

| Meta Key | Description | Type |
|----------|-------------|------|
| `avatarsteward_social_{provider}_token` | OAuth access token | string |
| `avatarsteward_social_{provider}_data` | Provider-specific user data | array |

Example for Twitter:
- `avatarsteward_social_twitter_token`
- `avatarsteward_social_twitter_data`

### Transients

Used for OAuth state validation (expires after 1 hour):

| Transient Key | Description |
|---------------|-------------|
| `avatarsteward_{provider}_state_{user_id}` | OAuth state token |
| `avatarsteward_{provider}_verifier_{user_id}` | PKCE code verifier (Twitter only) |

## Security Considerations

### Token Storage

- Access tokens are stored in WordPress user meta
- Tokens are only accessible to the user who owns them and administrators
- Consider implementing encryption for sensitive tokens

### OAuth Security

- State parameter validation prevents CSRF attacks
- PKCE (Proof Key for Code Exchange) used for Twitter enhances security
- Nonces used for all form submissions
- Capability checks ensure proper authorization

### Best Practices

1. Always validate user permissions before accessing/modifying social connections
2. Use HTTPS for all OAuth callbacks (required by most providers)
3. Implement rate limiting for import operations
4. Log security-relevant events
5. Sanitize and validate all user inputs

## Testing

Tests are located in `tests/phpunit/Domain/Integrations/`.

### Running Tests

```bash
composer test
```

### Test Coverage

- `TwitterProviderTest.php` - Tests for Twitter provider
- `FacebookProviderTest.php` - Tests for Facebook provider
- `IntegrationServiceTest.php` - Tests for integration service

## Additional Resources

- [Twitter API Documentation](https://developer.twitter.com/en/docs)
- [Facebook Graph API Documentation](https://developers.facebook.com/docs/graph-api/)
- [OAuth 2.0 RFC](https://tools.ietf.org/html/rfc6749)
- [PKCE RFC](https://tools.ietf.org/html/rfc7636)
