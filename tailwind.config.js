import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';
import animate from 'tailwindcss-animate';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        container: {
            center: true,
            padding: '2rem',
            screens: {
                '2xl': '1400px',
            },
        },
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                border: 'hsl(var(--border))',
                input: 'hsl(var(--input))',
                ring: 'hsl(var(--ring))',
                background: 'hsl(var(--background))',
                foreground: 'hsl(var(--foreground))',

                // MechaMap Custom Colors
                mechamap: {
                    // Base Colors
                    cobalt: 'hsl(var(--mechamap-cobalt))', // #3366CC - Màu chủ đạo
                    teal: 'hsl(var(--mechamap-teal))', // #1DCABC - Màu phụ
                    white: 'hsl(var(--mechamap-white))', // #FFFFFF - Màu nền
                    'light-gray': 'hsl(var(--mechamap-light-gray))', // #F7F9FC - Màu nền phụ
                    orange: 'hsl(var(--mechamap-orange))', // #FF7846 - Màu nhấn
                    'dark-gray': 'hsl(var(--mechamap-dark-gray))', // #2D3748 - Màu văn bản

                    // Dark Mode Colors
                    'cobalt-dark': 'hsl(var(--mechamap-cobalt-dark))',
                    'teal-dark': 'hsl(var(--mechamap-teal-dark))',
                    'dark-bg': 'hsl(var(--mechamap-dark-bg))',
                    'dark-surface': 'hsl(var(--mechamap-dark-surface))',
                    'orange-dark': 'hsl(var(--mechamap-orange-dark))',
                    'light-text': 'hsl(var(--mechamap-light-text))',

                    // Action Colors
                    success: 'hsl(var(--mechamap-success))', // #22C55E - Màu thành công
                    danger: 'hsl(var(--mechamap-danger))', // #EF4444 - Màu nguy hiểm
                    warning: 'hsl(var(--mechamap-warning))', // #F59E0B - Màu cảnh báo
                    info: 'hsl(var(--mechamap-info))', // #3366CC - Màu thông tin
                    light: 'hsl(var(--mechamap-light))', // #F7F9FC - Màu sáng
                    dark: 'hsl(var(--mechamap-dark))', // #2D3748 - Màu tối

                    // Action Colors - Dark Mode
                    'success-dark': 'hsl(var(--mechamap-success-dark))',
                    'danger-dark': 'hsl(var(--mechamap-danger-dark))',
                    'warning-dark': 'hsl(var(--mechamap-warning-dark))',
                    'info-dark': 'hsl(var(--mechamap-info-dark))',
                    'light-dark': 'hsl(var(--mechamap-light-dark))',
                    'dark-dark': 'hsl(var(--mechamap-dark-dark))',
                },

                // System Colors
                primary: {
                    DEFAULT: 'hsl(var(--primary))',
                    foreground: 'hsl(var(--primary-foreground))',
                    50: '#eef4ff', // Lighter version of cobalt
                    100: '#d9e6ff',
                    200: '#b3ccff',
                    300: '#80a6ff',
                    400: '#4d80ff',
                    500: '#3366cc', // Cobalt - Primary color
                    600: '#2952a3',
                    700: '#1f3d7a',
                    800: '#142952',
                    900: '#0a1429',
                    950: '#050a14',
                },
                secondary: {
                    DEFAULT: 'hsl(var(--secondary))',
                    foreground: 'hsl(var(--secondary-foreground))',
                    50: '#e6faf8', // Lighter version of teal
                    100: '#ccf5f1',
                    200: '#99ebe3',
                    300: '#66e0d5',
                    400: '#33d6c7',
                    500: '#1dcabc', // Teal - Secondary color
                    600: '#17a296',
                    700: '#117971',
                    800: '#0c514b',
                    900: '#062826',
                    950: '#031413',
                },
                accent: {
                    DEFAULT: 'hsl(var(--accent))',
                    foreground: 'hsl(var(--accent-foreground))',
                    50: '#fff2ee', // Lighter version of orange
                    100: '#ffe5dd',
                    200: '#ffccbb',
                    300: '#ffb399',
                    400: '#ff9977',
                    500: '#ff7846', // Orange - Accent color
                    600: '#cc6038',
                    700: '#99482a',
                    800: '#66301c',
                    900: '#33180e',
                    950: '#190c07',
                },
                destructive: {
                    DEFAULT: 'hsl(var(--destructive))',
                    foreground: 'hsl(var(--destructive-foreground))',
                },
                success: {
                    DEFAULT: 'hsl(var(--success))',
                    foreground: 'hsl(var(--success-foreground))',
                },
                warning: {
                    DEFAULT: 'hsl(var(--warning))',
                    foreground: 'hsl(var(--warning-foreground))',
                },
                info: {
                    DEFAULT: 'hsl(var(--info))',
                    foreground: 'hsl(var(--info-foreground))',
                },
                muted: {
                    DEFAULT: 'hsl(var(--muted))',
                    foreground: 'hsl(var(--muted-foreground))',
                },
                popover: {
                    DEFAULT: 'hsl(var(--popover))',
                    foreground: 'hsl(var(--popover-foreground))',
                },
                card: {
                    DEFAULT: 'hsl(var(--card))',
                    foreground: 'hsl(var(--card-foreground))',
                },
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 0.25rem)',
                sm: 'calc(var(--radius) - 0.5rem)',
            },
            boxShadow: {
                'soft-sm': '0 2px 4px 0 rgba(0, 0, 0, 0.05)',
                'soft-md': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                'soft-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                'soft-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02)',
            },
            keyframes: {
                'accordion-down': {
                    from: { height: 0 },
                    to: { height: 'var(--radix-accordion-content-height)' },
                },
                'accordion-up': {
                    from: { height: 'var(--radix-accordion-content-height)' },
                    to: { height: 0 },
                },
            },
            animation: {
                'accordion-down': 'accordion-down 0.2s ease-out',
                'accordion-up': 'accordion-up 0.2s ease-out',
            },
            spacing: {
                '4.5': '1.125rem',
                '5.5': '1.375rem',
                '6.5': '1.625rem',
                '7.5': '1.875rem',
                '8.5': '2.125rem',
                '9.5': '2.375rem',
            },
        },
    },

    plugins: [forms, typography, aspectRatio, animate],
};
