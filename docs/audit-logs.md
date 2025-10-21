# Audit Logs

Avatar Steward Pro includes a comprehensive audit logging system that tracks all avatar-related events in your WordPress site. This feature helps administrators maintain security, compliance, and accountability.

## Features

- **Structured Logging**: All avatar-related events are logged to a dedicated database table with indexes for fast queries
- **Event Types**: Tracks uploads, deletions, moderation decisions, metadata changes, library selections, and social imports
- **Filtering**: Filter logs by user, event type, action, and date range
- **Export**: Export audit logs in CSV or JSON format for external analysis
- **Automatic Retention**: Configurable log retention with automatic purging of old entries
- **IP & User Agent Tracking**: Records IP addresses and user agents for security auditing

## Event Types

The audit system tracks the following event types:

### Avatar Events
- `avatar_uploaded` - When a user uploads a new avatar
- `avatar_deleted` - When an avatar is removed
- `avatar_changed` - When a user changes their avatar

### Moderation Events
- `avatar_approved` - When a moderator approves an avatar
- `avatar_rejected` - When a moderator rejects an avatar (includes reason)

### Metadata Events
- `metadata_changed` - When avatar-related metadata is modified

### Library Events
- `library_avatar_selected` - When a user selects an avatar from the library

### Social Events
- `social_import` - When an avatar is imported from a social network

### System Events
- Various system-level events related to avatar management

## Admin Interface

### Viewing Audit Logs

1. Navigate to **Avatar Steward > Audit Logs** in the WordPress admin menu
2. The audit logs page displays a table with the following columns:
   - ID
   - User (display name and username)
   - Event Type
   - Action
   - Object (affected attachment or object)
   - IP Address
   - Date/Time

### Filtering Logs

Use the filters at the top of the page to narrow down results:

- **User ID**: Filter logs for a specific user
- **Event Type**: Show only specific types of events (avatar, moderation, etc.)
- **Date From/To**: Limit results to a specific date range

Click **Apply Filters** to refresh the results, or **Clear Filters** to reset.

### Exporting Logs

The audit logs can be exported in two formats:

1. **CSV Export**: Click the "Export CSV" button to download logs in comma-separated values format
2. **JSON Export**: Click the "Export JSON" button to download logs in JSON format

Exports respect the currently applied filters, allowing you to export only relevant data.

### Log Retention

Configure automatic log purging to prevent the database from growing indefinitely:

1. Scroll to the **Log Retention** section
2. Set the **Retention Period** (in days) - default is 90 days
3. Click **Save Retention Period** to update the setting
4. Click **Purge Old Logs Now** to manually delete logs older than the retention period

The system automatically purges old logs daily via WordPress cron.

## REST API

Avatar Steward provides REST API endpoints for programmatic access to audit logs.

### Authentication

All audit endpoints require:
- WordPress authentication (logged in user with `manage_options` capability)
- Active Pro license

### Endpoints

#### Get Audit Logs

```
GET /wp-json/avatar-steward/v1/audit
```

**Query Parameters:**
- `page` (int): Page number for pagination (default: 1)
- `per_page` (int): Number of items per page (default: 50, max: 100)
- `user_id` (int): Filter by user ID
- `event_type` (string): Filter by event type (avatar, moderation, metadata, library, social, system)
- `event_action` (string): Filter by specific action
- `date_from` (string): Filter from date (Y-m-d format)
- `date_to` (string): Filter to date (Y-m-d format)
- `orderby` (string): Sort field (id, user_id, event_type, created_at)
- `order` (string): Sort order (asc, desc)

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/avatar-steward/v1/audit?event_type=avatar&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
[
  {
    "id": 123,
    "user_id": 5,
    "event_type": "avatar",
    "event_action": "avatar_uploaded",
    "object_id": 456,
    "object_type": "attachment",
    "old_value": null,
    "new_value": null,
    "ip_address": "192.0.2.1",
    "user_agent": "Mozilla/5.0...",
    "metadata": null,
    "created_at": "2024-01-15 14:30:00"
  }
]
```

#### Export Audit Logs

```
GET /wp-json/avatar-steward/v1/audit/export?format={csv|json}
```

**Query Parameters:**
- `format` (string, required): Export format - either `csv` or `json`
- Same filter parameters as the get endpoint

**Example Request (CSV):**
```bash
curl -X GET "https://example.com/wp-json/avatar-steward/v1/audit/export?format=csv&event_type=moderation" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o audit-logs.csv
```

**Example Request (JSON):**
```bash
curl -X GET "https://example.com/wp-json/avatar-steward/v1/audit/export?format=json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o audit-logs.json
```

## Programmatic Usage

### Logging Events in Code

You can log custom audit events in your code:

```php
// Get the audit service instance
$plugin = \AvatarSteward\Plugin::instance();
$audit_service = $plugin->get_audit_service();

if ( $audit_service ) {
    // Log an avatar upload
    $audit_service->log_avatar_upload( $user_id, $attachment_id );
    
    // Log a moderation approval
    $audit_service->log_moderation_approval( $user_id, $moderator_id, $attachment_id );
    
    // Log a moderation rejection with reason
    $audit_service->log_moderation_rejection( $user_id, $moderator_id, $attachment_id, 'Inappropriate content' );
    
    // Log a generic event
    $audit_service->log_event(
        $user_id,
        \AvatarSteward\Domain\Audit\AuditService::EVENT_TYPE_SYSTEM,
        'custom_action',
        $object_id,
        'object_type',
        $old_value,
        $new_value,
        array( 'custom' => 'metadata' )
    );
}
```

### Querying Logs

```php
// Get logs with filters
$logs = $audit_service->get_logs(
    array(
        'user_id'      => 5,
        'event_type'   => 'avatar',
        'date_from'    => '2024-01-01 00:00:00',
        'date_to'      => '2024-01-31 23:59:59',
        'limit'        => 50,
        'offset'       => 0,
        'orderby'      => 'created_at',
        'order'        => 'DESC',
    )
);

// Count logs
$total = $audit_service->count_logs(
    array(
        'event_type' => 'moderation',
    )
);
```

### Exporting Logs

```php
// Export to CSV
$csv = $audit_service->export_to_csv(
    array(
        'event_type' => 'avatar',
    )
);

// Export to JSON
$json = $audit_service->export_to_json(
    array(
        'user_id' => 5,
    )
);
```

## Database Schema

Audit logs are stored in a custom table: `{prefix}avatar_steward_audit_logs`

### Columns

- `id` (bigint): Primary key
- `user_id` (bigint): User who performed the action
- `event_type` (varchar): Type of event (avatar, moderation, etc.)
- `event_action` (varchar): Specific action performed
- `object_id` (bigint): ID of the affected object (attachment, etc.)
- `object_type` (varchar): Type of object affected
- `old_value` (text): Previous value (for changes)
- `new_value` (text): New value (for changes)
- `ip_address` (varchar): IP address of the user
- `user_agent` (varchar): Browser user agent
- `metadata` (text): Additional metadata in JSON format
- `created_at` (datetime): Timestamp of the event

### Indexes

The table includes indexes for optimal query performance:
- Primary key on `id`
- Index on `user_id`
- Index on `event_type`
- Index on `event_action`
- Index on `created_at`
- Composite index on `(user_id, event_type, created_at)`

## Security & Privacy

### Pro License Requirement

Access to audit logs requires an active Avatar Steward Pro license. Both the admin interface and REST API endpoints enforce this requirement.

### Capability Checks

Only users with the `manage_options` capability can view and export audit logs.

### Data Retention

To comply with privacy regulations (GDPR, etc.), configure appropriate log retention periods:

1. Set retention to the minimum period required for your use case
2. Consider your local regulations regarding data retention
3. Use the purge feature to ensure old data is removed

### Sensitive Data

The audit system is designed to avoid logging sensitive information:
- Passwords are never logged
- Authentication tokens are never logged
- Only necessary metadata is recorded

## Troubleshooting

### Logs Not Appearing

1. Verify Pro license is active
2. Check that events are being triggered (uploads, moderations, etc.)
3. Verify database table was created: `{prefix}avatar_steward_audit_logs`

### Export Not Working

1. Ensure Pro license is active
2. Check file permissions if downloading directly
3. Try the REST API endpoint for more detailed error messages

### Performance Issues

If you have a very large number of logs:
1. Use date range filters to limit results
2. Reduce the retention period
3. Consider exporting and archiving old logs externally
4. Ensure database indexes are present (recreate table if needed)

## Best Practices

1. **Regular Exports**: Periodically export logs for long-term archival
2. **Appropriate Retention**: Set retention based on your compliance needs (typically 30-90 days)
3. **Monitor Critical Events**: Regularly review moderation rejections and unusual patterns
4. **Integration**: Integrate with external SIEM or log management systems via REST API
5. **Backup**: Include the audit logs table in your database backups

## Support

For additional help with audit logs:
- Review the [Avatar Steward documentation](../README.md)
- Contact support at [your support email]
- Check the [GitHub repository](https://github.com/JPMarichal/avataria) for updates
