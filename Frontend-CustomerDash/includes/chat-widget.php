<?php $chatMessage = $chatMessage ?? 'Mabuhay! Need assistance with your portal account or tracking? Chat with us here.'; ?>
    <!-- FLOATING CHAT SUPPORT WIDGET -->
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
                <?php echo $chatMessage; ?>
            </div>
        </div>

        <div class="p-3 border-t border-slate-200 bg-white flex gap-2">
            <input type="text" id="chatInput" onkeypress="handleChatKeyPress(event)" placeholder="Type a message..." class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-brand-blue">
            <button onclick="sendMessage()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-4 py-2 rounded-lg text-xs transition-colors">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>