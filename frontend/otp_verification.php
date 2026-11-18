<?php
require_once 'src/controllers/auth/otp_controller.php';
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden">

    <?php include_once 'src/components/header.php'; ?>

    <main class="relative w-full min-h-[calc(100vh-120px)] flex items-center justify-center overflow-hidden bg-slate-950 text-white py-12">
        
        <div class="absolute inset-0 opacity-30">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" alt="Warehouse Background" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-950/80 to-brand-darkblue/50 z-10"></div>

        <div class="relative z-20 max-w-md w-full px-4">
            <div class="bg-slate-950/85 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                
                <div class="text-center mb-6">
                    <div class="w-12 h-12 bg-sky-500/20 border border-sky-500/30 rounded-2xl flex items-center justify-center text-sky-400 text-xl mx-auto mb-3 shadow-lg">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Security Verification</h2>
                    <p class="text-xs text-slate-400 mt-1">Enter the 6-digit verification code sent to your registered email</p>
                </div>

               <?php include_once 'src/components/auth/error_handling.php' ?>

                <form method="POST" action="otp_verification.php" class="space-y-5">
                    <div>
                        <label for="otp_code" class="block text-xs font-semibold text-slate-300 mb-2 text-center">
                            Enter OTP Code
                        </label>
                        <div class="relative">
                            <input type="text" id="otp_code" name="otp_code" required maxlength="6" autofocus inputmode="numeric" pattern="[0-9]{6}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-center text-2xl tracking-[0.4em] font-mono focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder:text-slate-600 placeholder:tracking-normal"
                                   placeholder="000000">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-circle"></i> Verify Code
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <a href="login.php" class="hover:text-white transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Login
                    </a>
                </div>

                <div class="mt-5 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex gap-2.5 items-start">
                    <i class="fa-solid fa-circle-info text-amber-400 text-sm shrink-0 mt-0.5"></i>
                    <p class="text-[11px] text-amber-200/80 leading-relaxed">
                        If you didn’t receive the code, please check your spam folder or contact system support.
                    </p>
                </div>

            </div>
        </div>

    </main>

    <?php include_once 'src/components/footer.php'; ?>

</body>
</html>