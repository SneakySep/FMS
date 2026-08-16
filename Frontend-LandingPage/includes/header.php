<?php
/* ==========================================================================
   PRIORITY HANDLING LOGISTICS - SHARED PAGE HEADER
   Usage:
     $pageTitle  = 'Home';   // optional, appended to <title>
     $activePage = 'home';   // optional, keys: home about services forms faqs why-us careers contact
     include 'includes/header.php';
   ========================================================================== */
$pageTitle  = isset($pageTitle)  ? $pageTitle  : 'Priority Handling Logistics';
$activePage = isset($activePage) ? $activePage : '';
function activeNav($target, $current) { return $target === $current; }
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Priority Handling Logistics</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet.js CSS (Dark Map Engine) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- Tailwind Config Customization -->
    <script>
        tailwind.config = {
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
</head>
<body class="bg-white text-slate-900 font-sans antialiased overflow-x-hidden" data-page="<?php echo htmlspecialchars($activePage); ?>">

    <!-- TOP CONTACT BAR (priority-ph.com inspired) -->
    <div class="bg-brand-navy text-white text-[11px] relative z-40">
        <div class="max-w-7xl mx-auto px-6 py-2 flex flex-wrap items-center justify-between gap-x-6 gap-y-1">
            <div class="flex items-center gap-x-5 gap-y-1 flex-wrap">
                <a href="tel:+6328437484" class="hover:text-sky-300 transition-colors">
                    <i class="fa-solid fa-phone text-sky-400 mr-1.5"></i>(632) 843-7484
                </a>
                <a href="mailto:cs@priority-ph.com" class="hover:text-sky-300 transition-colors">
                    <i class="fa-solid fa-envelope text-sky-400 mr-1.5"></i>cs@priority-ph.com
                </a>
            </div>
            <div class="hidden sm:flex items-center gap-x-5">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-sky-400"></i> Makati City, Philippines</span>
                <span class="text-emerald-400 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-circle text-[6px]"></i> Open 24/7</span>
            </div>
        </div>
    </div>

    <!-- HEADER / NAVBAR -->
    <header class="sticky top-0 bg-white/95 backdrop-blur-md z-40 border-b border-slate-100 shadow-sm">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center gap-4">
            <a href="index.php" class="flex items-center gap-3 group shrink-0">
                <?php include 'includes/logo.php'; ?>
                <div class="leading-none">
                    <h1 class="text-lg font-black tracking-wider text-slate-900 uppercase leading-tight">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Logistics Inc. • Since 2005</span>
                </div>
            </a>
<!-- DESKTOP NAVIGATION -->
            <ul class="hidden lg:flex items-center gap-4 xl:gap-5 text-sm font-semibold text-slate-600">
                <li><a href="index.php"     class="nav-link <?php echo activeNav('home', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Home</a></li>
                <li><a href="about.php"     class="nav-link <?php echo activeNav('about', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">About</a></li>
                <li><a href="services.php"  class="nav-link <?php echo activeNav('services', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Services</a></li>
                <li>
                    <button onclick="openPromoModal()" class="nav-link nav-promo-link inline-flex items-center gap-1.5" title="View exclusive promos">
                        <i class="fa-solid fa-gift"></i> Promos
                        <span class="promo-hot px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider">Hot</span>
                    </button>
                </li>
                <li><a href="forms.php"     class="nav-link <?php echo activeNav('forms', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Forms</a></li>
                <li><a href="faqs.php"      class="nav-link <?php echo activeNav('faqs', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">FAQs</a></li>
                <li><a href="why-us.php"    class="nav-link <?php echo activeNav('why-us', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Why Us?</a></li>
                <li><a href="careers.php"   class="nav-link <?php echo activeNav('careers', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Careers</a></li>
                <li><a href="contact.php"   class="nav-link <?php echo activeNav('contact', $activePage) ? 'nav-link-active' : 'hover:text-brand-blue'; ?>">Contact Us</a></li>
            </ul>

            <!-- RIGHT SIDE ACTIONS -->
            <div class="flex items-center gap-3">
                <a href="forms.php" class="hidden md:inline-flex bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-5 py-2.5 rounded-lg transition-all shadow-md shadow-blue-500/20 hover:-translate-y-0.5">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Online Inquiry
                </a>
                <button id="mobileMenuBtn" onclick="toggleMobileMenu()" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-bars" id="mobileMenuIcon"></i>
                </button>
            </div>
        </nav>

        <!-- MOBILE MENU -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-slate-100 shadow-lg">
            <ul class="px-6 py-4 space-y-1.5 text-sm font-semibold text-slate-600">
                <li><a href="index.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('home', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Home</a></li>
                <li><a href="about.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('about', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">About</a></li>
                <li><a href="services.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('services', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Services</a></li>
                <li>
                    <button onclick="openPromoModal(); toggleMobileMenu()" class="w-full text-left py-2 px-3 rounded-lg text-amber-600 hover:bg-amber-50 flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i> Promos
                        <span class="promo-hot px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider">Hot</span>
                    </button>
                </li>
                <li><a href="forms.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('forms', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Forms</a></li>
                <li><a href="faqs.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('faqs', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">FAQs</a></li>
                <li><a href="why-us.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('why-us', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Why Us?</a></li>
                <li><a href="careers.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('careers', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Careers</a></li>
                <li><a href="contact.php" class="block py-2 px-3 rounded-lg <?php echo activeNav('contact', $activePage) ? 'bg-brand-blue/10 text-brand-blue' : 'hover:bg-slate-50'; ?>">Contact Us</a></li>
                <li class="pt-2">
                    <a href="forms.php" class="block text-center bg-brand-blue hover:bg-brand-darkblue text-white font-semibold py-2.5 rounded-lg">Online Inquiry</a>
                </li>
            </ul>
        </div>
    </header>

    <main>