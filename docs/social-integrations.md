# Social Integrations Setup Guide

This guide explains how to set up and use the social media integrations feature in Avatar Steward.

## Overview

Avatar Steward allows users to import their profile pictures from social media platforms as their WordPress avatar. Currently supported platforms:

- **Twitter / X** - Uses OAuth 2.0 with PKCE
- **Facebook** - Uses OAuth 2.0

## Administrator Setup

### 1. Twitter / X Integration

#### Create a Twitter App

1. Go to [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard)
2. Create a new project and app (or use an existing one)
3. In your app settings, navigate to "OAuth 2.0 Settings"
4. Configure the following:
   - **Type of App**: Web App
   - **Callback URL**: `https://yoursite.com/wp-admin/profile.php?avatarsteward_oauth=twitter`
   - **Website URL**: Your WordPress site URL
5. Enable OAuth 2.0 and note your **Client ID** and **Client Secret**

#### Configure in WordPress

1. Go to **Settings > Avatar Steward** in WordPress admin
2. Scroll to the **Social Integrations** section
3. Enter your Twitter **Client ID** and **Client Secret**
4. Save changes

### 2. Facebook Integration

#### Create a Facebook App

1. Go to [Facebook for Developers](https://developers.facebook.com/)
2. Create a new app or use an existing one
3. Add the **Facebook Login** product to your app
4. Configure OAuth Settings:
   - **Valid OAuth Redirect URIs**: `https://yoursite.com/wp-admin/profile.php?avatarsteward_oauth=facebook`
5. In **Settings > Basic**, note your **App ID** and **App Secret**

#### Configure in WordPress

1. Go to **Settings > Avatar Steward** in WordPress admin
2. Scroll to the **Social Integrations** section
3. Enter your Facebook **App ID** and **App Secret**
4. Save changes

## User Workflow

### Connecting a Social Account

1. Go to your WordPress **Profile** page
2. Scroll to the **Social Avatar Import** section
3. Click **Connect Twitter** or **Connect Facebook**
4. You'll be redirected to the social platform to authorize the connection
5. After authorization, you'll be redirected back to your profile
6. The connection status will show as "Connected" with a green checkmark

### Importing an Avatar

1. Ensure your social account is connected (see above)
2. In the **Social Avatar Import** section, click **Import Avatar**
3. Your profile picture from the social platform will be downloaded and set as your WordPress avatar
4. The avatar will be visible immediately across your site

### Disconnecting a Social Account

1. Go to your WordPress **Profile** page
2. In the **Social Avatar Import** section, find the connected account
3. Click **Disconnect**
4. The connection will be removed (your avatar will remain unless you change it)

## Privacy and Security

### Data Stored

Avatar Steward stores the following data for connected social accounts:

- **Access Token**: Encrypted and stored in WordPress user meta
- **User Data**: Basic profile information from the social platform (username, profile URL)

### Data Usage

- Access tokens are only used to fetch profile pictures when you click "Import Avatar"
- No automatic syncing or data sharing occurs
- Tokens are deleted when you disconnect an account

### Permissions Requested

- **Twitter**: `users.read` and `tweet.read` (to access profile information)
- **Facebook**: `public_profile` (to access profile picture)

## Troubleshooting

### "Invalid state parameter" Error

This usually occurs if you wait too long between initiating the connection and completing it. The OAuth state token expires after 1 hour. Simply try connecting again.

### "Failed to import avatar" Message

Possible causes:
- The social account may not have a profile picture set
- Network connectivity issues
- API credentials may be incorrect or expired

To resolve:
1. Verify your API credentials in **Settings > Avatar Steward**
2. Ensure the social account has a profile picture
3. Try disconnecting and reconnecting the account

### Connection Shows "Not connected" After Authorization

This may indicate:
- Callback URL mismatch - ensure the callback URLs in your app settings exactly match your WordPress site URL
- API credentials are incorrect
- OAuth flow was interrupted

## Developer Notes

### Extending with Additional Providers

Avatar Steward uses the Strategy Pattern for social providers. To add a new provider:

1. Create a new class implementing `SocialProviderInterface`
2. Extend `AbstractSocialProvider` for common functionality
3. Register your provider using the `avatarsteward_register_providers` action

Example:

```php
add_action( 'avatarsteward_register_providers', function( $integration_service ) {
    $integration_service->register_provider( new MyCustomProvider() );
} );
```

### Available Hooks

- `avatarsteward_social_connected` - Fired when a user connects a social account
- `avatarsteward_social_disconnected` - Fired when a user disconnects a social account
- `avatarsteward_avatar_imported` - Fired when an avatar is imported from a social platform
- `avatarsteward_register_providers` - Allows registration of custom providers

## Requirements

- WordPress >= 5.8
- PHP >= 7.4
- HTTPS enabled (required for OAuth)
- Active API credentials for each social platform you want to support

## Support

For additional help:
- Check the [main documentation](../README.md)
- Review the [API documentation](api/integrations.md) for developers
- Submit issues on [GitHub](https://github.com/JPMarichal/avataria/issues)
