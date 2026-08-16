<?php
$pageTitle = $pageTitle ?? 'Priority Handling Logistics';
$extraHead = $extraHead ?? '';
?><!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS Styles -->
    <link rel="stylesheet" href="css/styles.css">

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
                            sidebar: '#0a1628',
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

    <!-- Dark Mode Initialization (runs before body render to prevent flash) -->
    <script>
        (function() {
            const DARK_MODE_KEY = 'priority_dark_mode';
            const isDark = localStorage.getItem(DARK_MODE_KEY) === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <?php echo $extraHead; ?>
</head>
<body class="bg-[#f4f7fa] dark:bg-[#0a1628] text-slate-800 dark:text-slate-200 font-sans antialiased min-h-screen flex">