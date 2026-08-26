<script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#0066ff',
                            darkblue: '#0052cc',
                            navy: '#0b1528',
                            sidebar: '#080e1e',
                            lightbg: '#f8fafc',
                            DEFAULT: '#7c3aed',
                            strong: '#6d28d9',
                            medium: '#c4b5fd',
                        },
                        neutral: {
                            'primary-soft': '#f8fafc',
                            'primary-medium': '#ffffff',
                            'secondary-medium': '#f1f5f9',
                            'tertiary-medium': '#e2e8f0',
                        },
                        'default': '#e2e8f0',
                        'default-medium': '#cbd5e1',
                        'body': '#475569',
                        'heading': '#0f172a',
                        'fg-brand': '#7c3aed',
                    },
                    borderRadius: {
                        base: '0.5rem',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'xs': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                    }
                }
            }
        }
    </script>