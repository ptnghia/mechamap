# MechaMap Localization Developer Guide

**Version:** 2.0
**Last Updated:** 2025-07-20

## 🏗️ New Structure Overview

The localization system has been completely restructured using a feature-based approach:

```
resources/lang_new/
├── vi/                 # Vietnamese translations
├── en/                 # English translations
    ├── core/           # System core (auth, validation, pagination, passwords)
    ├── ui/             # User interface (common, navigation, buttons, forms, modals)
    ├── content/        # Page content (home, pages, alerts)
    ├── features/       # Features (forum, marketplace, showcase, knowledge, community)
    ├── user/           # User functionality (profile, settings, notifications, messages)
    └── admin/          # Admin interface (dashboard, users, system)
```

## 🔧 Helper Functions

Use these shorthand functions for cleaner code:

```php
// Instead of __('core.auth.login.title')
t_core('auth.login.title')

// Instead of __('ui.buttons.save')
t_ui('buttons.save')

// Instead of __('features.forum.create')
t_feature('forum.create')
```

## 🎨 Blade Directives

Use these directives in your Blade templates:

```blade
{{-- Instead of {{ __('core.auth.login.title') }} --}}
@core('auth.login.title')

{{-- Instead of {{ __('ui.buttons.save') }} --}}
@ui('buttons.save')

{{-- Generic shorthand --}}
@t('any.translation.key')
```

## 📝 Naming Convention

Follow this pattern: `{category}.{subcategory}.{key}`

**Examples:**
- `core.auth.login.title`
- `ui.buttons.save`
- `features.forum.threads.create`
- `user.profile.edit.title`
- `admin.dashboard.stats.users`

## 🚀 Best Practices

1. **Always use the helper functions** for better readability
2. **Group related keys** in the same file
3. **Keep keys descriptive** but not too long
4. **Maintain VI/EN synchronization** when adding new keys
5. **Use IDE helper** for autocomplete support

## 🔍 IDE Support

The project includes `_ide_helper_translations.php` for autocomplete support.
Make sure your IDE recognizes this file for better development experience.

## 🛠️ Artisan Commands

Available commands for localization management:

```bash
# Check for missing or unused keys
php artisan lang:check

# Sync translations between languages
php artisan lang:sync

# Validate translation file syntax
php artisan lang:validate
```
