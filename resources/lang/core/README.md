# Core Translations

**Purpose:** System core functionality translations
**Created:** 2025-07-20 02:30:27

## Files Overview

### auth.php
**Description:** Authentication related translations
**Key structure:** `core.auth.*`
**Source files:** auth.php

### validation.php
**Description:** Form validation messages
**Key structure:** `core.validation.*`
**Source files:** validation.php

### pagination.php
**Description:** Pagination controls
**Key structure:** `core.pagination.*`
**Source files:** pagination.php

### passwords.php
**Description:** Password reset functionality
**Key structure:** `core.passwords.*`
**Source files:** passwords.php

## Usage Examples

```php
// Authentication
__('core.auth.failed')
__('core.auth.throttle')

// Validation
__('core.validation.required')
__('core.validation.email')

// Pagination
__('core.pagination.previous')
__('core.pagination.next')

// Passwords
__('core.passwords.reset')
__('core.passwords.sent')
```

## Migration Notes

- All keys maintain their original structure within the core namespace
- No key content was modified, only relocated
- Both VI and EN versions are synchronized
