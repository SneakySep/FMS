<?php
session_start();
$lockout_email = $_SESSION["lockout_email"] ?? "your account";
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
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-950/80 to-rose-950/40 z-10"></div>

        <div class="relative z-20 max-w-md w-full px-4">
            <div class="bg-slate-950/85 border border-rose-500/30 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative text-center">
                
                <div class="w-16 h-16 bg-rose-500/20 border border-rose-500/40 rounded-2xl flex items-center justify-center text-rose-400 text-2xl mx-auto mb-4 shadow-lg animate-pulse">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <h2 class="text-2xl font-extrabold text-white tracking-tight">Account Temporarily Locked</h2>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                   Due to multiple consecutive incorrect password entries, security access for this email has been temporarily locked. (<span class="text-rose-400 font-semibold"><?php echo htmlspecialchars($lockout_email); ?></span>).
                </p>

                <div class="mt-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-left">
                    <div class="flex gap-2.5 items-start">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm shrink-0 mt-0.5"></i>
                        <p class="text-[11px] text-rose-200/80 leading-relaxed">
                            Try again after <strong>15 minutes</strong>, or contact the Super Admin to reset your login restriction.
                        </p>
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-white/10">
                    <a href="login.php" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Return to Login
                    </a>
                </div>

            </div>
        </div>
    </main>

    <?php include_once 'src/components/footer.php'; ?>

</body>
</html>