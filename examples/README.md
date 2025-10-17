# Avatar Steward - Code Examples

This directory contains example code snippets, configurations, and integration examples for Avatar Steward.

## Available Examples

### Configuration Examples
- **`custom-palette.php`** - How to create custom color palettes for the initials generator
- **`role-restrictions.php`** - How to restrict avatar uploads based on user roles
- **`migration-script.php`** - Example script for migrating from Gravatar or other avatar plugins

### Environment Setup
- **`docker-compose.demo.yml`** - Docker Compose configuration for demo environment
- **`.env.example`** - Environment variables template for configuration

## Usage

Each example file includes:
1. Clear comments explaining the purpose
2. Step-by-step implementation guide
3. Expected results and testing tips
4. WordPress hooks and filters being used

## Integration with WordPress

All examples follow WordPress best practices:
- Use of proper hooks (`add_filter`, `add_action`)
- Sanitization and validation of inputs
- Respect for user capabilities and permissions
- Translation-ready strings

## Testing Examples

You can test these examples in a local development environment:

1. Start the demo environment:
   ```bash
   docker compose -f examples/docker-compose.demo.yml up -d
   ```

2. Access WordPress at `http://localhost:8080`

3. Copy the example code to your theme's `functions.php` or a custom plugin

4. Test the functionality and observe the results

## Need Help?

- Check the main documentation in `/docs`
- Review the FAQ in `/docs/faq.md`
- Contact support (see `/docs/support.md`)

## Contributing Examples

If you have useful examples to share:
1. Follow the existing code style
2. Add clear documentation
3. Test thoroughly
4. Submit a pull request (for open-source version)

---

**Last Updated**: October 17, 2025
