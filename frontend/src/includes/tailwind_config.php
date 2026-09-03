<script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            // Accent colours are driven by CSS custom properties
                            // (see assets/css/style.css -> "ACCENT THEMES") so the
                            // customer Appearance settings can retint the portal at
                            // runtime via <html data-accent="...">.
                            // The <alpha-value> placeholder keeps /10, /20, ...
                            // opacity modifiers working.
                            blue: 'rgb(var(--accent-rgb) / <alpha-value>)',
                            darkblue: 'rgb(var(--accent-strong-rgb) / <alpha-value>)',
                            navy: '#0f172a',
                            sidebar: '#080e1e',
                            lightbg: '#f7f8fc',
                        },
                        // "Navy & White" design scale - mirrors the CSS custom
                        // properties in assets/css/theme.css so utilities and
                        // component classes never drift apart.
                        navy: {
                            50:  '#f1f5f9',
                            100: '#e2e8f0',
                            200: '#cbd5e1',
                            300: '#94a3b8',
                            400: '#64748b',
                            500: '#475569',
                            600: '#334155',
                            700: '#1e293b',
                            800: '#0f172a',
                            850: '#0b1326',
                            900: '#080e1e',
                            950: '#050914',
                        },
                        canvas: '#f7f8fc',
                        surface: {
                            DEFAULT: '#ffffff',
                            muted: '#f8fafc',
                            hover: '#f2f5fb',
                        },
                        line: '#e5e9f2',
                        'line-strong': '#d7deeb',
                        neutral: {
                            'primary-soft': '#f8fafc',
                            'primary-medium': '#ffffff',
                            'secondary-medium': '#f1f5f9',
                            'tertiary-medium': '#e2e8f0',
                        },
                        'default': '#e2e8f0',
                        'default-medium': '#cbd5e1',
                        'body': '#334155',
                        'heading': '#080e1e',
                    },
                    borderRadius: {
                        base: '0.5rem',
                        card: '1rem',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'xs': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                        'card': '0 1px 2px rgba(8,14,30,.04), 0 1px 3px rgba(8,14,30,.06)',
                        'lift': '0 12px 32px -14px rgba(8,14,30,.20)',
                        'navy': '0 8px 20px -10px rgba(8,14,30,.55)',
                    }
                }
            }
        }
    </script>