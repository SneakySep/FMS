<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftFreight - Fast. Reliable. Worldwide.</title>

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
                            blue: '#0066ff',
                            darkblue: '#0052cc',
                            navy: '#001529',
                            navycard: '#002244',
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

    <!-- TOP HEADER / NAVBAR (WHITE BACKGROUND) -->
    <header class="sticky top-0 bg-white/95 backdrop-blur-md z-40 border-b border-slate-200 shadow-sm transition-all">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-tr from-brand-darkblue to-brand-blue text-white rounded-xl flex items-center justify-center p-2 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <svg viewBox="0 0 24 24" fill="none" class="w-full h-full text-white" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M2 17L12 22L22 17" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 2V12" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="leading-none">
                    <h1 class="text-lg font-black tracking-wider text-slate-900 uppercase">SWIFT<span class="text-brand-blue">FREIGHT</span></h1>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Enterprise Portal</span>
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
                        Fast. Reliable.<br>Worldwide.
                    </h2>
                    
                    <p class="text-slate-200 text-base sm:text-lg mb-8 max-w-lg leading-relaxed font-normal drop-shadow">
                        End-to-end freight solutions that move your business forward.
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
                                    <a href="javascript:void(0)" onclick="alert('Please contact your SwiftFreight account manager to reset your portal password.')" class="text-[11px] text-sky-400 hover:underline">Forgot password?</a>
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
                                <i class="fa-solid fa-right-to-bracket"></i> Sign In to SwiftPortal
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
                <span class="font-black text-purple-700 text-sm">FedEx</span>
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
            <p>&copy; 2026 SwiftFreight Logistics Philippines Inc. All rights reserved.</p>
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
                    <h4 class="text-xs font-bold">SwiftSupport PH</h4>
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
    <div id="privacyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-2xl w-full p-6 text-slate-200 text-xs space-y-3">
            <h3 class="text-base font-bold text-white">Privacy Policy (RA 10173 Compliance)</h3>
            <p>SwiftFreight processes corporate portal accounts strictly under the Philippine Data Privacy Act of 2012.</p>
            <button onclick="closePrivacyModal()" class="mt-4 bg-brand-blue text-white px-4 py-2 rounded-lg font-semibold">Close</button>
        </div>
    </div>

    <div id="termsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-2xl w-full p-6 text-slate-200 text-xs space-y-3">
            <h3 class="text-base font-bold text-white">Terms of Service (Philippine Law)</h3>
            <p>Governed by Philippine Commercial Law, Customs Modernization and Tariff Act (RA 10863), and Civil Code on Carriers.</p>
            <button onclick="closeTermsModal()" class="mt-4 bg-brand-blue text-white px-4 py-2 rounded-lg font-semibold">Close</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>