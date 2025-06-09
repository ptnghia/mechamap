/**
 * Theme Switching Diagnostics
 *
 * Tool này giúp chẩn đoán vấn đề với hệ thống chuyển đổi theme
 * Để sử dụng: thêm ?theme-diagnostics=1 vào URL trang web
 */
(function() {
    'use strict';

    // Chỉ chạy nếu được yêu cầu trong URL
    if (!window.location.search.includes('theme-diagnostics')) {
        return;
    }

    function createDiagnosticOverlay() {
        var overlay = document.createElement('div');
        overlay.id = 'theme-diagnostics-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
            z-index: 9999;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            color: #212529;
        `;

        var header = document.createElement('div');
        header.style.cssText = `
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        `;

        var title = document.createElement('h3');
        title.textContent = 'Theme Diagnostics';
        title.style.margin = '0';

        var closeButton = document.createElement('button');
        closeButton.textContent = 'X';
        closeButton.style.cssText = `
            background: none;
            border: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
        `;
        closeButton.addEventListener('click', function() {
            overlay.remove();
        });

        header.appendChild(title);
        header.appendChild(closeButton);
        overlay.appendChild(header);

        var content = document.createElement('div');
        content.id = 'theme-diagnostics-content';
        overlay.appendChild(content);

        var actions = document.createElement('div');
        actions.style.cssText = `
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
        `;

        var toggleButton = document.createElement('button');
        toggleButton.textContent = 'Toggle Theme';
        toggleButton.style.cssText = `
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            margin-right: 8px;
            cursor: pointer;
        `;
        toggleButton.addEventListener('click', function() {
            var newTheme;
            if (window.themeManager) {
                newTheme = window.themeManager.toggle();
                updateDiagnostics();
            } else {
                logMessage('Error: themeManager not found');
            }
        });

        var resetButton = document.createElement('button');
        resetButton.textContent = 'Reset Storage';
        resetButton.style.cssText = `
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
        `;
        resetButton.addEventListener('click', function() {
            try {
                localStorage.removeItem('theme');
                sessionStorage.removeItem('theme');
                document.cookie = 'dark_mode=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
                logMessage('✅ Theme storage reset successful');
                updateDiagnostics();
            } catch (e) {
                logMessage('❌ Error resetting storage: ' + e.message);
            }
        });

        actions.appendChild(toggleButton);
        actions.appendChild(resetButton);
        overlay.appendChild(actions);

        var log = document.createElement('div');
        log.id = 'theme-diagnostics-log';
        log.style.cssText = `
            margin-top: 12px;
            padding: 8px;
            background-color: #f1f3f5;
            border-radius: 4px;
            max-height: 150px;
            overflow-y: auto;
            font-size: 12px;
        `;
        overlay.appendChild(log);

        document.body.appendChild(overlay);

        return {
            overlay: overlay,
            content: content,
            log: log
        };
    }

    function logMessage(message) {
        var log = document.getElementById('theme-diagnostics-log');
        if (log) {
            var entry = document.createElement('div');
            entry.innerHTML = '<span style="color:#999">' + new Date().toLocaleTimeString() + '</span> ' + message;
            log.appendChild(entry);
            log.scrollTop = log.scrollHeight;
        }
    }

    function createDataItem(label, value, status) {
        var statusClass = status === 'ok' ? 'text-success' :
                         status === 'warning' ? 'text-warning' :
                         status === 'error' ? 'text-danger' : '';

        var statusIcon = status === 'ok' ? '✅' :
                        status === 'warning' ? '⚠️' :
                        status === 'error' ? '❌' : '';

        return `
            <div class="diagnostics-item" style="margin-bottom: 8px; display: flex;">
                <div style="flex: 1;">${label}:</div>
                <div style="flex: 2; ${statusClass ? 'color: ' + statusClass : ''}">${statusIcon} ${value}</div>
            </div>
        `;
    }

    function updateDiagnostics() {
        var content = document.getElementById('theme-diagnostics-content');
        if (!content) return;

        // Collect diagnostic data
        var diagnosticData = {
            browserInfo: {
                userAgent: navigator.userAgent,
                language: navigator.language,
                cookiesEnabled: navigator.cookieEnabled
            },
            themeSystem: {
                themeSwitchExists: !!document.getElementById('theme-toggle'),
                darkModeSwitchExists: !!document.getElementById('darkModeSwitch'),
                currentTheme: document.documentElement.getAttribute('data-theme') || 'light',
                bodyHasDarkClass: document.body.classList.contains('dark-mode'),
                localStorageTheme: '',
                cookieTheme: '',
                themeManagerExists: !!window.themeManager
            },
            storage: {
                localStorageAvailable: false,
                sessionStorageAvailable: false,
                cookiesAvailable: navigator.cookieEnabled
            },
            cssInfo: {
                darkModeCSS: false
            }
        };

        // Check storage availability
        try {
            var test = 'test';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            diagnosticData.storage.localStorageAvailable = true;
            diagnosticData.themeSystem.localStorageTheme = localStorage.getItem('theme') || 'not set';
        } catch (e) {
            diagnosticData.storage.localStorageAvailable = false;
        }

        try {
            var test = 'test';
            sessionStorage.setItem(test, test);
            sessionStorage.removeItem(test);
            diagnosticData.storage.sessionStorageAvailable = true;
        } catch (e) {
            diagnosticData.storage.sessionStorageAvailable = false;
        }

        // Check for cookie
        var getCookie = function(name) {
            var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? match[2] : '';
        };

        diagnosticData.themeSystem.cookieTheme = getCookie('dark_mode') || 'not set';

        // Check if dark mode CSS is loaded
        var styleSheets = document.styleSheets;
        for (var i = 0; i < styleSheets.length; i++) {
            try {
                if (styleSheets[i].href && styleSheets[i].href.includes('dark-mode.css')) {
                    diagnosticData.cssInfo.darkModeCSS = true;
                    break;
                }
            } catch (e) {
                // Some stylesheets may not be accessible due to CORS
            }
        }

        // Generate HTML content
        var html = `
            <h4>Browser Information</h4>
            ${createDataItem('User Agent', diagnosticData.browserInfo.userAgent)}
            ${createDataItem('Language', diagnosticData.browserInfo.language)}
            ${createDataItem('Cookies Enabled',
                            diagnosticData.browserInfo.cookiesEnabled ? 'Yes' : 'No',
                            diagnosticData.browserInfo.cookiesEnabled ? 'ok' : 'error')}

            <h4>Theme System</h4>
            ${createDataItem('Theme Button',
                            diagnosticData.themeSystem.themeSwitchExists ? 'Found' : 'Missing',
                            diagnosticData.themeSystem.themeSwitchExists ? 'ok' : 'error')}
            ${createDataItem('Dark Mode Switch',
                            diagnosticData.themeSystem.darkModeSwitchExists ? 'Found' : 'Missing',
                            diagnosticData.themeSystem.darkModeSwitchExists ? 'warning' : 'info')}
            ${createDataItem('Current Theme',
                            diagnosticData.themeSystem.currentTheme,
                            'info')}
            ${createDataItem('Body has Dark Class',
                            diagnosticData.themeSystem.bodyHasDarkClass ? 'Yes' : 'No',
                            diagnosticData.themeSystem.bodyHasDarkClass === (diagnosticData.themeSystem.currentTheme === 'dark') ? 'ok' : 'warning')}
            ${createDataItem('Theme Manager',
                            diagnosticData.themeSystem.themeManagerExists ? 'Available' : 'Missing',
                            diagnosticData.themeSystem.themeManagerExists ? 'ok' : 'error')}

            <h4>Storage</h4>
            ${createDataItem('LocalStorage',
                            diagnosticData.storage.localStorageAvailable ? 'Available' : 'Unavailable',
                            diagnosticData.storage.localStorageAvailable ? 'ok' : 'error')}
            ${createDataItem('LocalStorage Theme',
                            diagnosticData.themeSystem.localStorageTheme,
                            diagnosticData.storage.localStorageAvailable ? 'info' : 'warning')}
            ${createDataItem('Cookie Theme',
                            diagnosticData.themeSystem.cookieTheme,
                            diagnosticData.storage.cookiesAvailable ? 'info' : 'warning')}
            ${createDataItem('SessionStorage',
                            diagnosticData.storage.sessionStorageAvailable ? 'Available' : 'Unavailable',
                            'info')}

            <h4>CSS</h4>
            ${createDataItem('Dark Mode CSS',
                            diagnosticData.cssInfo.darkModeCSS ? 'Loaded' : 'Not Found',
                            diagnosticData.cssInfo.darkModeCSS ? 'ok' : 'error')}
        `;

        content.innerHTML = html;

        // Log diagnostic summary
        var themeStatus = diagnosticData.themeSystem.themeSwitchExists &&
                        diagnosticData.storage.localStorageAvailable &&
                        diagnosticData.cssInfo.darkModeCSS ? '✅ Theme system appears operational' : '❌ Theme system has issues';

        logMessage(themeStatus);

        // Detect inconsistencies
        if (diagnosticData.themeSystem.currentTheme === 'dark' && !diagnosticData.themeSystem.bodyHasDarkClass) {
            logMessage('⚠️ Inconsistency: data-theme is dark but body doesn\'t have dark-mode class');
        }

        if (diagnosticData.themeSystem.localStorageTheme !== 'not set' && diagnosticData.themeSystem.localStorageTheme !== diagnosticData.themeSystem.currentTheme) {
            logMessage('⚠️ Inconsistency: localStorage theme doesn\'t match current theme');
        }

        if (diagnosticData.themeSystem.cookieTheme !== 'not set' && diagnosticData.themeSystem.cookieTheme !== diagnosticData.themeSystem.currentTheme) {
            logMessage('⚠️ Inconsistency: cookie theme doesn\'t match current theme');
        }

        return diagnosticData;
    }

    // Wait for page to load
    window.addEventListener('load', function() {
        var diagnostics = createDiagnosticOverlay();
        updateDiagnostics();
        logMessage('Diagnostic tool initialized');

        // Listen for theme changes
        document.addEventListener('themeToggled', function(e) {
            logMessage('Theme toggled to: ' + e.detail.theme);
            updateDiagnostics();
        });
    });
})();
