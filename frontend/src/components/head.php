<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Priority Handling Logistics</title>
<link rel="icon" type="image/png" href="/assets/image/new_logo.png">

<!-- Anti-flash theme boot: apply graphite dark before first paint -->
<script>
    (function () {
        try {
            var saved = localStorage.getItem('fms-theme');
            var dark = saved ? saved === 'dark'
                             : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        } catch (e) { /* ignore */ }
    })();
</script>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Google Fonts & FontAwesome -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Custom CSS Styles -->
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/auth.css">

<!-- Tailwind Config Customization -->
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    brand: {
                        blue: '#1D2E6A',
                        darkblue: '#152252',
                        navy: '#0a1628',
                        navycard: '#112240',
                        lightbg: '#f4f7fa',
                    }
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                }
            }
        }
    }
</script>

<!-- Graphite theme controller -->
<script src="/assets/js/theme.js"></script>