import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                serif: ['Lora', ...defaultTheme.fontFamily.serif],
                mono:  ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                parchment: {
                    50:  '#faf8f4',
                    100: '#f4f0e6',
                    200: '#ece4d0',
                    300: '#d9cdb4',
                },
                ink: {
                    DEFAULT: '#2c2416',
                    light:   '#5a4a32',
                    faint:   '#8c7a60',
                },
            },
            typography: (theme) => ({
                reading: {
                    css: {
                        '--tw-prose-body':     theme('colors.ink.DEFAULT'),
                        '--tw-prose-headings': theme('colors.ink.DEFAULT'),
                        '--tw-prose-links':    theme('colors.ink.light'),
                        '--tw-prose-bold':     theme('colors.ink.DEFAULT'),
                        '--tw-prose-code':     theme('colors.ink.light'),
                        fontFamily:            theme('fontFamily.serif').join(', '),
                        fontSize:              '1.0625rem',
                        lineHeight:            '1.8',
                    },
                },
            }),
        },
    },

    plugins: [forms, typography],
};
