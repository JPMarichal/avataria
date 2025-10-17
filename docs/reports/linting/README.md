# Linting Reports

This directory stores the results of code linting with phpcs (PHP_CodeSniffer) and ESLint.

## PHP Linting (phpcs)

Run PHP linting with:
```bash
composer lint
```

Auto-fix issues with:
```bash
vendor/bin/phpcbf
```

Reports are saved with format: `phpcs-YYYYMMDD.txt`

## JavaScript Linting (ESLint)

Run JavaScript linting with:
```bash
npm run lint
```

Auto-fix issues with:
```bash
npm run lint:fix
```

## Standards

- **PHP**: WordPress Coding Standards (WordPress-Core, WordPress-Docs, WordPress-Extra)
- **JavaScript**: ESLint with ES6+ rules
- **Configuration**: See `phpcs.xml` and `.eslintrc.json` in the project root

## Reporting Format

Each report should include:
- Date of execution
- Branch name
- Summary of results (passed/failed)
- Any action items or issues that need manual intervention
