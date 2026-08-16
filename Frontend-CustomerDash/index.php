<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Priority Handling Logistics - Fast. Reliable. Worldwide.</title>

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
<body class="bg-white text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden">

    <!-- TOP CONTACT BAR -->
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

    <!-- TOP HEADER / NAVBAR (WHITE BACKGROUND) -->
    <header class="sticky top-0 bg-white/95 backdrop-blur-md z-40 border-b border-slate-200 shadow-sm transition-all">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-tr from-brand-darkblue to-brand-blue text-white rounded-xl flex items-center justify-center p-2 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-cube text-lg"></i>
                </div>
                <div class="leading-none">
                    <h1 class="text-lg font-black tracking-wider text-slate-900 uppercase">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Logistics Inc. • Since 2005</span>
                </div>
            </a>

            <!-- Right Controls -->
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline flex items-center gap-2 text-xs text-slate-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> BOC Customs Online
                </span>
                
            </div>
        </nav>
    </header>

    <!-- MAIN HERO SECTION WITH FULL-SCREEN MOVING BACKGROUND PICTURES -->
    <main id="home" class="relative w-full min-h-[calc(100vh-73px)] flex items-center justify-center overflow-hidden bg-slate-950 text-white"
          onmouseenter="stopHeroTimer()" onmouseleave="startHeroTimer()">
        
        <!-- Moving Background Slideshow Images -->
        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100">
            <img src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1600&q=80" 
                 alt="Trucking Freight" class="w-full h-full object-cover">
        </div>
        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" 
                 alt="Smart Warehousing" class="w-full h-full object-cover">
        </div>
        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
            <img src="https://images.unsplash.com/photo-1559297434-fae8a1916a79?auto=format&fit=crop&w=1600&q=80" 
                 alt="Ocean Cargo" class="w-full h-full object-cover">
        </div>
        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
            <img src="https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1600&q=80" 
                 alt="Air Cargo" class="w-full h-full object-cover">
        </div>

        <!-- Dark Gradient Overlays over the Moving Pictures for Text Readability -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/70 to-slate-950/40 z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-black/40 z-10"></div>

        <!-- Foreground Content (Original Text + Login Card) -->
        <div class="relative z-20 max-w-7xl mx-auto px-6 w-full py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Column: HERO TEXT & BUTTONS -->
                <div class="lg:col-span-7 reveal">
                    <div class="inline-flex items-center gap-2 bg-brand-blue/90 backdrop-blur-md text-white text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-6 border border-white/20 shadow-lg">
                        <i id="heroBadgeIcon" class="fa-solid fa-truck-fast"></i>
                        <span id="heroBadgeText">Cross-Border Trucking</span>
                    </div>

                    <h2 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.08] mb-6 drop-shadow-lg">
                        Delivering Nationwide.<br>Shipping Worldwide.
                    </h2>
                    
                    <p class="text-slate-200 text-base sm:text-lg mb-8 max-w-lg leading-relaxed font-normal drop-shadow">
                        End-to-end freight and courier solutions that move your business forward.
                    </p>

                    <!-- Original Action Buttons -->
                    <div class="flex flex-wrap gap-4 mb-10">
                        <button onclick="scrollToLogin()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-8 py-3.5 rounded-lg shadow-xl shadow-blue-600/30 transition-all hover:-translate-y-0.5">
                            Get a Quote
                        </button>
                        <a href="#login-card" class="bg-white/15 hover:bg-white/25 text-white backdrop-blur-md border border-white/30 font-semibold text-sm px-8 py-3.5 rounded-lg transition-all hover:-translate-y-0.5">
                            Our Services
                        </a>
                    </div>

                    <!-- Stats Badges -->
                    <div class="grid grid-cols-3 gap-6 pt-6 border-t border-white/20 max-w-md">
                        <div>
                            <strong class="text-white text-2xl font-extrabold block drop-shadow">99.8%</strong>
                            <span class="text-slate-300 text-xs">On-Time Arrival</span>
                        </div>
                        <div>
                            <strong class="text-white text-2xl font-extrabold block drop-shadow">24/7</strong>
                            <span class="text-slate-300 text-xs">BOC Customs</span>
                        </div>
                        <div>
                            <strong class="text-white text-2xl font-extrabold block drop-shadow">150+</strong>
                            <span class="text-slate-300 text-xs">Global Ports</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Glassmorphic Login Card -->
                <div id="login-card" class="lg:col-span-5 reveal">
                    <div class="bg-slate-950/80 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                        
                        <div class="text-center mb-8">
                            <div class="w-12 h-12 bg-brand-blue/20 border border-brand-blue/30 rounded-2xl flex items-center justify-center text-brand-blue text-xl mx-auto mb-3 shadow-lg">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">Client Portal Sign In</h3>
                            <p class="text-slate-400 text-xs mt-1">Enter your corporate credentials to access your portal</p>
                        </div>

                        <!-- Step 1: Credentials Form -->
                        <form id="credentialsForm" onsubmit="handleLogin(event)" class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" id="loginEmail" required value="client@company.ph" 
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="name@company.ph">
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-300">Password</label>
                                    <a href="javascript:void(0)" onclick="alert('Please contact your Priority Handling Logistics account manager to reset your portal password.')" class="text-[11px] text-sky-400 hover:underline">Forgot password?</a>
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password" id="loginPassword" required value="demo1234" 
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="••••••••">
                                </div>
                            </div>

                            <div class="flex items-center justify-between py-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" checked class="w-4 h-4 rounded bg-white/5 border-white/10 text-brand-blue focus:ring-0">
                                    <span class="text-xs text-slate-400">Remember credentials</span>
                                </label>
                            </div>

                            <button type="submit" id="loginBtn" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Client Portal
                            </button>

                            <div id="loginStatus" class="hidden p-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-center text-xs"></div>
                        </form>

                        <!-- Step 2: OTP Verification Form (Hidden Initially) -->
                        <form id="otpForm" onsubmit="handleOtpVerify(event)" class="space-y-5 hidden">
                            <div class="text-center mb-2">
                                <div class="w-12 h-12 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl flex items-center justify-center text-emerald-400 text-xl mx-auto mb-3 shadow-lg">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h3 class="text-xl font-extrabold text-white">Two-Factor Verification</h3>
                                <p class="text-slate-400 text-xs mt-1">Enter the 6-digit code sent to <span id="otpEmailDisplay" class="text-sky-400 font-mono">your email</span></p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">One-Time Password (OTP)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </span>
                                    <input type="text" id="otpInput" required maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all tracking-[0.5em] text-center font-mono"
                                           placeholder="••••••">
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1.5">Demo OTP: <span class="font-mono text-emerald-400 font-bold">123456</span></p>
                            </div>

                            <button type="submit" id="otpBtn" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check"></i> Verify & Sign In
                            </button>

                            <div class="flex items-center justify-between text-[11px]">
                                <button type="button" onclick="backToCredentials()" class="text-slate-400 hover:text-white transition-colors">
                                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                                </button>
                                <button type="button" onclick="resendOtp()" class="text-sky-400 hover:underline">
                                    <i class="fa-solid fa-rotate-right mr-1"></i> Resend Code
                                </button>
                            </div>

                            <div id="otpStatus" class="hidden p-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-center text-xs"></div>
                        </form>

                        <!-- Demo Credentials Box -->
                        <div class="mt-6 p-3.5 bg-white/5 border border-white/10 rounded-xl text-center">
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold block mb-1">Demo Access Credentials</span>
                            <p class="text-xs text-slate-300">Email: <span class="font-mono text-sky-400">client@company.ph</span> | Password: <span class="font-mono text-sky-400">demo1234</span></p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Floating Slide Controls (Bottom Right of Hero Section) -->
        <div class="absolute bottom-6 right-6 lg:right-12 z-30 flex items-center gap-4 bg-black/50 backdrop-blur-md p-2.5 px-4 rounded-full border border-white/15 shadow-2xl">
            <button onclick="prevHeroSlide()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/30 text-white flex items-center justify-center transition-colors">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <div class="flex items-center gap-2">
                <button onclick="setHeroSlide(0)" class="hero-dot w-6 h-2 rounded-full bg-white transition-all duration-300"></button>
                <button onclick="setHeroSlide(1)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
                <button onclick="setHeroSlide(2)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
                <button onclick="setHeroSlide(3)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
            </div>

            <button onclick="nextHeroSlide()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/30 text-white flex items-center justify-center transition-colors">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>

    </main>

    <!-- CARRIERS TICKER (WHITE / LIGHT BACKGROUND) -->
    <section class="py-8 bg-slate-50 border-t border-b border-slate-200 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">
                Connected Global Freight Network
            </p>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-6 items-center justify-items-center opacity-80 grayscale hover:grayscale-0 transition-all">
                <span class="font-black text-slate-800 text-sm">MAERSK</span>
                <span class="font-black text-brand-blue text-sm">FedEx</span>
                <span class="font-black italic text-red-600 text-sm">DHL</span>
                <span class="font-bold text-slate-800 text-xs"><i class="fa-solid fa-anchor mr-1"></i> COSCO</span>
                <span class="font-bold text-sky-600 text-xs"><i class="fa-solid fa-plane-departure mr-1"></i> BOEING</span>
                <span class="font-black text-slate-800 text-xs">DB SCHENKER</span>
            </div>
        </div>
    </section>

    <!-- FOOTER (WHITE BACKGROUND) -->
    <footer class="bg-white text-slate-500 py-8 px-6 text-xs border-t border-slate-100 relative z-20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <p>&copy; 2026 Priority Handling Logistics Inc. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-slate-900 transition-colors">Privacy Policy (RA 10173)</a>
                <a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-slate-900 transition-colors">Terms of Service (PH Law)</a>
            </div>
        </div>
    </footer>

    <!-- FLOATING CHAT WIDGET -->
    <div onclick="toggleChat()" class="fixed bottom-6 right-6 w-14 h-14 bg-brand-blue hover:bg-brand-darkblue text-white rounded-full flex items-center justify-center text-2xl shadow-xl shadow-blue-500/30 cursor-pointer z-50 transition-transform hover:scale-110">
        <i class="fa-solid fa-comments"></i>
    </div>

    <div id="chatBox" class="fixed bottom-24 right-6 w-[360px] max-w-[calc(100vw-3rem)] h-[480px] bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 flex flex-col overflow-hidden transition-all duration-300 opacity-0 pointer-events-none translate-y-6">
        <div class="bg-brand-navy text-white p-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-blue flex items-center justify-center text-xs">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold">Priority Support PH</h4>
                    <span class="text-[10px] text-emerald-400 font-semibold">● Online</span>
                </div>
            </div>
            <button onclick="toggleChat()" class="text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="chatBody" class="flex-1 p-4 bg-slate-50 overflow-y-auto flex flex-col gap-3">
            <div class="bg-white border border-slate-200 text-slate-800 text-xs p-3 rounded-lg max-w-[85%] self-start shadow-sm leading-relaxed">
                Mabuhay! Need assistance with your portal account or tracking? Chat with us here.
            </div>
        </div>

        <div class="p-3 border-t border-slate-200 bg-white flex gap-2">
            <input type="text" id="chatInput" onkeypress="handleChatKeyPress(event)" placeholder="Type a message..." class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-brand-blue">
            <button onclick="sendMessage()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-4 py-2 rounded-lg text-xs transition-colors">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- MODALS -->
    <!-- PRIVACY POLICY MODAL -->
    <div id="privacyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Privacy Policy</h3>
                        <span class="text-[10px] text-slate-400">Compliant with Republic Act No. 10173 (Philippine Data Privacy Act of 2012)</span>
                    </div>
                </div>
                <button onclick="closePrivacyModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p class="font-semibold text-white">Effective Date: January 1, 2026</p>
                <p>Priority Handling Logistics Inc. is committed to protecting your personal data under RA 10173 (Data Privacy Act of 2012). We collect only the personal information needed to process shipments, respond to inquiries, and fulfill our service obligations.</p>
                <p><strong class="text-white">What we collect:</strong> Name, contact details, company information, and shipment details provided through our online forms.</p>
                <p><strong class="text-white">How we use it:</strong> To arrange courier and freight forwarding services, respond to inquiries, send order/promo updates (with consent), and comply with customs and regulatory requirements.</p>
                <p><strong class="text-white">Your rights:</strong> Under RA 10173 you may access, correct, or request deletion of your personal data, or withdraw consent, by contacting our Data Protection Officer at cs@priority-ph.com.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closePrivacyModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">I Understand</button>
            </div>
        </div>
    </div>
    <!-- TERMS & CONDITIONS MODAL -->
    <div id="termsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Terms and Conditions</h3>
                        <span class="text-[10px] text-slate-400">Governed by the Laws of the Republic of the Philippines</span>
                    </div>
                </div>
                <button onclick="closeTermsModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p class="font-semibold text-white">Effective Date: January 1, 2026</p>
                <p class="font-semibold text-white mb-2">TERMS AND CONDITIONS OF CARRIAGE</p>
                <p><strong class="text-white">1. In these conditions.</strong> The &quot;Carrier&quot; means Priority Handling Logistics, Inc. carrying on business in its own name and under any business name and unless the context otherwise requires its officers, servants, agents and sub-contractors THE CARRIES IS NOT A COMMON CARRIER and will accept no liability as such.</p>
                <p><strong class="text-white">2. The Carrier reserves the right to:</strong></p>
                <p class="pl-4">a) refuse the carriage or transport of goods to its destination,</p>
                <p class="pl-4">b) carry consignor's documents or goods by any route and procedure, and by successive carriers and according to its handling storage and transportation methods, and</p>
                <p class="pl-4">c) to inspect any goods consigned to ensure that they are capable of carriage to the countries of destination within the standard operating procedures, customs declarations, and handling methods of the Carrier.</p>
                <p><strong class="text-white">4. The Consignor is responsible</strong> for the packing of the goods including the packing in any container that may be supplied to the Consignor by the Carrier. The Carrier accepts no responsibility for loss or damage or to the goods caused by inadequate of inappropriate packaging. It is the responsibility of the Consignor to address adequately each consignment to enable effective delivery to be made. The Carrier shall not be liable for delay in forwarding or delivery resulting from the Consignor's failure to comply with its obligation in this respect.</p>
                <p><strong class="text-white">5. The Consignor warrants</strong> that it is authorized to accept and is accepting these Conditions on behalf of itself but also as an agent for and on behalf of all other person who are or may hereafter become interested in the goods. The Consignor indemnifies the Carrier against any damages, costs and expenses resulting from any breach of this warranty.</p>
                <p><strong class="text-white">6. The Carrier shall have a lien</strong> on all goods shipped, for freight charges and/or advance for other charges of any kind arising out of transportation hereunder and may refuse to surrender possession of the goods until such charges are paid.</p>
                <p><strong class="text-white">7. The Carrier is liable</strong> for damage sustained in the event of the destruction or loss of, or of damage to any cargo, the occurrence which caused the damage so sustained takes place during carriage and the Carrier is also liable for damage occasioned by delay in the carriage of cargo. The Carrier is not liable if it proves that it and its servants and agents have taken all necessary measures to avoid the damage or that it was impossible for it or them to take such measures. PROVIDED ALWAYS THAT the liability of the Carrier under these conditions shall be limited to the payment by the Carrier by way of damages of a sum of not exceeding US$100.00 or its local currency equivalent and Pesos 100.00 for international and domestic carriage respectively per consignment or in the case where transit insurance is affected the amount payable thereunder in the event of loss or damage to the goods.</p>
                <p><strong class="text-white">8. Any claim brought</strong> by a customer against the Carrier hereunder in respect of duties and liabilities must be notified by the customer to an office of the Carrier with in 48 hours and in writing within 14 days of the day when the goods should have reached their destination. No claim for loss or damage may be entertained if o in is filed beyond the allowable period or until the transportation charges have been paid.</p>
                <p><strong class="text-white">9. The Carrier will not carry</strong> dangerous, hazardous, combustible or explosive materials, gold and silver bullion, coin, dust, cyanides, precipitates or any form of uncoined gold and silver bullion, platinum and other precious metals, precious and semi-precious stones, currency (paper or coin) of any nationality, negotiable securities, stocks, bonds, certificates, stamps, blank or endorsed bank cheques, money orders or travelers cheques, antiques, works of art, livestock, plants, drugs, pharmaceuticals, liquor, firearms, tobacco, foodstuff, IATA restricted articles or perishables and in the event that the Consignor should consign such items. with the Carrier the Consignor indemnities the Carrier for all claims, damages costs and expenses which may arise as a result of such carriage and the Carrier shall have the right to abandon carriage of the same immediately upon Carrier having knowledge that the goods infringe this condition.</p>
                <p><strong class="text-white">10. If the carriage involves</strong> an ultimate destination or stop in a country other than the country of departure, the Warsaw Convention may be applicable and the Convention governs and in most cases limits the liability of carriers in respect of lost of or damage to cargo.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closeTermsModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">Accept Terms</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>
