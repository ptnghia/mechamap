# New Localization Structure

**Created:** 2025-07-20 02:28:24
**Purpose:** Feature-based localization organization

## Directory Structure

```
resources/lang_new/
├── vi/                 # Vietnamese translations
└── en/                 # English translations
    ├── core/           # System core functionality
    │   ├── auth.php
    │   ├── validation.php
    │   ├── pagination.php
    │   ├── passwords.php
    ├── ui/           # User interface elements
    │   ├── common.php
    │   ├── navigation.php
    │   ├── buttons.php
    │   ├── forms.php
    │   ├── modals.php
    ├── content/           # Page content and static text
    │   ├── home.php
    │   ├── pages.php
    │   ├── alerts.php
    ├── features/           # Feature-specific content
    │   ├── forum.php
    │   ├── marketplace.php
    │   ├── showcase.php
    │   ├── knowledge.php
    │   ├── community.php
    ├── user/           # User-related functionality
    │   ├── profile.php
    │   ├── settings.php
    │   ├── notifications.php
    │   ├── messages.php
    ├── admin/           # Admin interface
    │   ├── dashboard.php
    │   ├── users.php
    │   ├── system.php
```

## Categories

### core/
**Purpose:** System core functionality

**Files:**
- `auth.php` - Keys: `core.auth.*`
- `validation.php` - Keys: `core.validation.*`
- `pagination.php` - Keys: `core.pagination.*`
- `passwords.php` - Keys: `core.passwords.*`

### ui/
**Purpose:** User interface elements

**Files:**
- `common.php` - Keys: `ui.common.*`
- `navigation.php` - Keys: `ui.navigation.*`
- `buttons.php` - Keys: `ui.buttons.*`
- `forms.php` - Keys: `ui.forms.*`
- `modals.php` - Keys: `ui.modals.*`

### content/
**Purpose:** Page content and static text

**Files:**
- `home.php` - Keys: `content.home.*`
- `pages.php` - Keys: `content.pages.*`
- `alerts.php` - Keys: `content.alerts.*`

### features/
**Purpose:** Feature-specific content

**Files:**
- `forum.php` - Keys: `features.forum.*`
- `marketplace.php` - Keys: `features.marketplace.*`
- `showcase.php` - Keys: `features.showcase.*`
- `knowledge.php` - Keys: `features.knowledge.*`
- `community.php` - Keys: `features.community.*`

### user/
**Purpose:** User-related functionality

**Files:**
- `profile.php` - Keys: `user.profile.*`
- `settings.php` - Keys: `user.settings.*`
- `notifications.php` - Keys: `user.notifications.*`
- `messages.php` - Keys: `user.messages.*`

### admin/
**Purpose:** Admin interface

**Files:**
- `dashboard.php` - Keys: `admin.dashboard.*`
- `users.php` - Keys: `admin.users.*`
- `system.php` - Keys: `admin.system.*`

## Usage Examples

```php
// Old way
__('messages.common.loading')
__('nav.home')
__('buttons.save')

// New way
__('ui.common.loading')
__('ui.navigation.home')
__('ui.buttons.save')
```

## Migration

Use the migration helper script:
```bash
php resources/lang_new/migrate_keys.php
```
