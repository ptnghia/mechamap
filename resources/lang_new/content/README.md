# Content Translations

**Purpose:** Page content and static text
**Created:** 2025-07-20 02:35:44

## Files Overview

### home.php
**Description:** Homepage content and sections
**Key structure:** `content.home.*`
**Source files:** home.php

### pages.php
**Description:** Static pages and general content
**Key structure:** `content.pages.*`
**Source files:** pages.php, content.php, coming_soon.php

### alerts.php
**Description:** Alert messages and notifications
**Key structure:** `content.alerts.*`
**Source files:** alerts.php

## Usage Examples

```php
// Homepage content
__('content.home.hero.title')
__('content.home.sections.featured_showcases')

// Static pages
__('content.pages.about.title')
__('content.pages.contact.email')

// Alert messages
__('content.alerts.types.success')
__('content.alerts.common.operation_successful')
```
