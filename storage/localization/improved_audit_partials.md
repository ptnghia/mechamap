# Improved Blade Localization Audit Report

**Directory:** partials
**Generated:** 2025-07-20 03:39:34
**Files processed:** 3

## 📝 Localizable Texts Found (6)

- `Người dùng`
- `;
    button.disabled = true;

    // Gửi request với Accept header để nhận JSON
    fetch(`{{ url(`
- `);
        // Khôi phục button
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

/**
 * Tự động phát hiện ngôn ngữ
 */
function autoDetectLanguage() {
    fetch(`
- `);
    });
}

/**
 * Hiển thị thông báo
 */
function showNotification(message, type =`
- `) {
    // Tạo toast notification đơn giản
    const toast = document.createElement(`
- `Network response was not ok`

## 🔑 Existing Translation Keys (24)

- `forum.status.pinned`
- `forum.status.locked`
- `forum.actions.bookmark_remove`
- `forum.actions.bookmarked`
- `forum.actions.bookmark_add`
- `thread.bookmark`
- `forum.actions.unfollow_thread`
- `forum.actions.following`
- `forum.actions.follow_thread`
- `thread.follow`
- `forum.views`
- `forum.replies`
- `ui.common.language.switch`
- `ui.common.language.select`
- `ui.common.language.auto_detect`
- `messages.language.switched_successfully`
- `messages.language.switch_failed`
- `messages.language.auto_detect_failed`
- `showcase.features.cad`
- `showcase.features.download`
- `buttons.view_details`
- `showcase.ratings`
- `showcase.category`
- `showcase.project_type`

## 🎯 Priority Fixes (5)

### Text: `Người dùng` (Priority: 13)
- **Key:** `partials.ngi_dng`
- **Helper:** `t_ui('partials.ngi_dng')`
- **Directive:** `@ui('partials.ngi_dng')`

### Text: `;
    button.disabled = true;

    // Gửi request với Accept header để nhận JSON
    fetch(`{{ url(` (Priority: 10)
- **Key:** `partials._buttondisabled_true_gi_reques`
- **Helper:** `t_ui('partials._buttondisabled_true_gi_reques')`
- **Directive:** `@ui('partials._buttondisabled_true_gi_reques')`

### Text: `);
        // Khôi phục button
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

/**
 * Tự động phát hiện ngôn ngữ
 */
function autoDetectLanguage() {
    fetch(` (Priority: 10)
- **Key:** `partials._khi_phc_button_buttoninnerhtm`
- **Helper:** `t_ui('partials._khi_phc_button_buttoninnerhtm')`
- **Directive:** `@ui('partials._khi_phc_button_buttoninnerhtm')`

### Text: `);
    });
}

/**
 * Hiển thị thông báo
 */
function showNotification(message, type =` (Priority: 10)
- **Key:** `partials._hin_th_thng_bo_function_shown`
- **Helper:** `t_ui('partials._hin_th_thng_bo_function_shown')`
- **Directive:** `@ui('partials._hin_th_thng_bo_function_shown')`

### Text: `) {
    // Tạo toast notification đơn giản
    const toast = document.createElement(` (Priority: 10)
- **Key:** `partials._to_toast_notification_n_gin_c`
- **Helper:** `t_ui('partials._to_toast_notification_n_gin_c')`
- **Directive:** `@ui('partials._to_toast_notification_n_gin_c')`

