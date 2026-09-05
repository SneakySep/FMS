<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/config/config.php';

// Kunin ang role at ID ng naka-login na user sa PHP session
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer'; // 'customer' o 'sales_agent'

// Kunin ang tamang ID batay sa role ng naka-login:
if ($user_role === 'sales_agent') {
    $current_user_id = $_SESSION['agent_id'] ?? 'agent-101';
} else {
    // Real customers-table UUID na na-resolve during OTP verification.
    // Fall back to the logged-in user id (chat queries by customers.id).
    $current_user_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? '';
}

// Host:port ng FastAPI backend (para sa REST + WebSocket connections)
$backend_url_parts = parse_url(API_BASE_URL);
$backend_host = ($backend_url_parts['host'] ?? '127.0.0.1') . ':' . ($backend_url_parts['port'] ?? '8000');
$ws_scheme = (isset($backend_url_parts['scheme']) && $backend_url_parts['scheme'] === 'https') ? 'wss' : 'ws';
?>

<!-- FLOATING CHAT BUTTON -->
<div class="fixed bottom-5 right-5 z-50">
    <button id="toggleChatBtn" class="relative bg-navy-900 hover:bg-navy-950 dark:bg-slate-200 dark:hover:bg-white dark:text-navy-950 text-white w-12 h-12 rounded-2xl shadow-lg shadow-navy transition-all duration-200 flex items-center justify-center hover:-translate-y-0.5 active:scale-95">
        <i data-lucide="message-square" class="w-5 h-5"></i>

        <!-- COUNTER BADGE PARA SA SALES AGENT -->
        <?php if ($user_role === 'sales_agent'): ?>
            <span id="unreadBadge" class="hidden absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white dark:ring-navy-900 animate-bounce">
                0
            </span>
        <?php endif; ?>
    </button>
</div>

<!-- CHAT MAIN WINDOW -->
<div id="chatModal" class="hidden fixed bottom-20 right-5 z-50 w-[95vw] md:w-[750px] h-[550px] crm-card !rounded-2xl !shadow-lift flex overflow-hidden">

    <?php if ($user_role === 'sales_agent'): ?>
        <!-- SIDEBAR INBOX (SALES AGENT ONLY) -->
        <div class="w-1/3 bg-surface-muted border-r border-line flex flex-col">
            <div class="p-3 border-b border-line flex justify-between items-center">
                <h2 class="font-bold text-navy-900 dark:text-slate-100 text-sm">Customer Inbox</h2>
                <button onclick="loadCustomerList()" class="text-xs text-brand-blue hover:underline">Refresh</button>
            </div>
            <!-- LIST NG MGA CUSTOMER NA NAG-CHAT -->
            <div id="customerList" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <!-- Dito pino-populate ng JS ang profile items -->
            </div>
        </div>
    <?php endif; ?>

    <!-- CHAT BOX WINDOW (COMMON) -->
    <div class="flex-1 flex flex-col bg-canvas">

        <!-- Header -->
        <div class="p-3 bg-surface border-b border-line flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-500/20"></div>
                <h3 id="chatHeaderTitle" class="font-semibold text-navy-900 dark:text-slate-100 text-sm">
                    <?php echo $user_role === 'sales_agent' ? 'Choose Customer' : 'SwiftFreight Support'; ?>
                </h3>
            </div>

            <div class="flex gap-2">
                <?php if ($user_role === 'sales_agent'): ?>
                    <!-- BUTTON PARA IBALIK AGAD KAY AI -->
                    <button id="handoverBtn" onclick="handoverToAI()" class="hidden text-[11px] bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg font-semibold transition">
                        Hand Back to AI
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="messagesContainer" class="crm-scroll flex-1 p-4 overflow-y-auto space-y-3 text-sm">
            <!-- Dito lumalabas ang chat bubbles -->
        </div>

        <!-- Input Area -->
        <form id="widgetChatForm" class="p-3 bg-surface border-t border-line flex gap-2">
            <input
                type="text"
                id="widgetMessageInput"
                placeholder="Type your message..."
                class="crm-input !text-sm flex-1"
                required
            >
            <button type="submit" class="crm-btn crm-btn-primary !text-sm">
                Send
            </button>
        </form>

    </div>
</div>


<!-- JAVASCRIPT INTEGRATION -->
<script>
    lucide.createIcons();

    const USER_ROLE = "<?php echo $user_role; ?>";
    const CURRENT_USER_ID = "<?php echo htmlspecialchars($current_user_id); ?>";
    const BACKEND_HOST = "<?php echo $backend_host; ?>";
    const WS_SCHEME = "<?php echo $ws_scheme; ?>";
    const API_ROOT = `http://${BACKEND_HOST}`;

    let activeConvId = null;
    let chatWs = null;

    const toggleChatBtn = document.getElementById('toggleChatBtn');
    const chatModal = document.getElementById('chatModal');
    const messagesContainer = document.getElementById('messagesContainer');
    const widgetChatForm = document.getElementById('widgetChatForm');
    const widgetMessageInput = document.getElementById('widgetMessageInput');

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    // Toggle Chat Window
    toggleChatBtn.addEventListener('click', () => {
        chatModal.classList.toggle('hidden');
        if (!chatModal.classList.contains('hidden')) {
            loadConversations();
        }
    });

    // Render Chat Bubbles (sender_type: customer | ai | sales_agent | agent | system)
    function appendBubble(text, senderType, timeStr) {
        const isMe = (USER_ROLE === 'customer' && senderType === 'customer') ||
                     (USER_ROLE === 'sales_agent' && (senderType === 'sales_agent' || senderType === 'agent'));

        if (senderType === 'system') {
            const sys = document.createElement('div');
            sys.className = 'text-center';
            sys.innerHTML = `<span class="text-[10px] text-navy-400 dark:text-slate-500 italic">${escapeHtml(text)}</span>`;
            messagesContainer.appendChild(sys);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            return;
        }

        const div = document.createElement('div');
        div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
        const time = timeStr ? `<span class="block text-[9px] mt-1 opacity-60">${escapeHtml(timeStr)}</span>` : '';
        div.innerHTML = `
            <div class="${isMe ? 'bg-navy-900 text-white dark:bg-slate-200 dark:text-navy-950' : 'bg-surface text-navy-700 border border-line'} p-2.5 rounded-xl max-w-[80%] shadow-sm">
                ${escapeHtml(text)}${time}
            </div>
        `;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function showChatNotice(message) {
        messagesContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center text-center px-6 py-8">
                <p class="text-xs text-navy-400 dark:text-slate-400 leading-relaxed">${escapeHtml(message)}</p>
            </div>`;
    }

    // Load conversations: customer opens their latest one; agent renders inbox.
    async function loadConversations() {
        if (USER_ROLE === 'customer') {
            if (!CURRENT_USER_ID) {
                showChatNotice('You need to sign in to use live chat.');
                return;
            }
            try {
                const res = await fetch(`${API_ROOT}/customer/v1/chat/conversations/${encodeURIComponent(CURRENT_USER_ID)}`);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const convs = await res.json();

                if (!Array.isArray(convs) || convs.length === 0) {
                    disconnectWs();
                    activeConvId = null;
                    showChatNotice('No active conversation yet. Once our team opens a chat with you, it will appear here instantly.');
                    return;
                }

                convs.sort((a, b) => new Date(b.updated_at || 0) - new Date(a.updated_at || 0));
                openConversation(convs[0].id);
            } catch (err) {
                console.error("Failed to load conversations:", err);
                showChatNotice('Could not reach the chat service. Please try again later.');
            }
        } else {
            loadCustomerList();
        }
    }

    // Open a conversation: history + websocket
    async function openConversation(convId, title) {
        activeConvId = convId;
        if (title) {
            document.getElementById('chatHeaderTitle').innerText = title;
        }
        await fetchChatHistory(convId);
        connectWs(convId);
    }

    // Load message history for a conversation
    async function fetchChatHistory(convId) {
        try {
            const res = await fetch(`${API_ROOT}/agent/v1/chat/messages/${encodeURIComponent(convId)}`);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const msgs = await res.json();
            messagesContainer.innerHTML = '';
            if (Array.isArray(msgs)) {
                msgs.forEach(m => appendBubble(m.message, m.sender_type, m.formatted_time));
            }
        } catch (err) {
            console.error("Failed to load history:", err);
            showChatNotice('Failed to load chat history.');
        }
    }

    // WEBSOCKET: realtime messages (AI / agent replies broadcast by backend)
    function disconnectWs() {
        if (chatWs) {
            try { chatWs.close(); } catch (e) { /* noop */ }
            chatWs = null;
        }
    }

    function connectWs(convId) {
        disconnectWs();
        try {
            chatWs = new WebSocket(`${WS_SCHEME}://${BACKEND_HOST}/customer/v1/chat/ws/chat/${encodeURIComponent(convId)}`);
            chatWs.onmessage = (event) => {
                try {
                    const msg = JSON.parse(event.data);
                    if (msg && msg.conversation_id === convId && msg.message) {
                        appendBubble(msg.message, msg.sender_type);
                    }
                } catch (e) {
                    console.error("Bad WS payload:", e);
                }
            };
            chatWs.onerror = () => console.warn("Chat websocket error");
        } catch (err) {
            console.error("Could not open chat websocket:", err);
        }
    }

    // Submit Chat Message via WebSocket
    widgetChatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = widgetMessageInput.value.trim();
        if (!msg) return;

        if (!activeConvId) {
            showChatNotice('No active conversation yet. Please book a shipment or contact support to start a chat.');
            return;
        }
        if (!chatWs || chatWs.readyState !== WebSocket.OPEN) {
            connectWs(activeConvId);
            appendBubble('Connection is still opening — please try sending again in a moment.', 'system');
            return;
        }

        chatWs.send(JSON.stringify({
            sender_type: USER_ROLE === 'customer' ? 'customer' : 'sales_agent',
            sender_id: USER_ROLE === 'customer' ? CURRENT_USER_ID : null,
            message: msg
        }));
        widgetMessageInput.value = '';
    });

    // FOR SALES AGENT: Kuhanin ang active customer list
    async function loadCustomerList() {
        if (USER_ROLE !== 'sales_agent') return;

        try {
            const res = await fetch(`${API_ROOT}/agent/v1/chat/conversations`);
            const conversations = await res.json();

            const listEl = document.getElementById('customerList');
            const unreadBadge = document.getElementById('unreadBadge');
            listEl.innerHTML = '';

            if (Array.isArray(conversations) && conversations.length > 0) {
                unreadBadge.innerText = conversations.length;
                unreadBadge.classList.remove('hidden');

                conversations.forEach((conv) => {
                    const item = document.createElement('div');
                    item.className = "p-3 hover:bg-surface-hover cursor-pointer flex items-center gap-3 transition border-b border-line" +
                        (conv.id === activeConvId ? " bg-blue-50/80" : "");
                    const displayName = conv.customer_name || conv.customer_id || 'Customer';
                    item.innerHTML = `
                        <div class="w-8 h-8 rounded-lg bg-navy-900 text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                            ${escapeHtml(String(displayName).substring(0, 2).toUpperCase())}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-semibold text-navy-900 dark:text-slate-100 truncate">${escapeHtml(displayName)}</p>
                            <p class="text-[11px] text-navy-400 dark:text-slate-400 truncate">${escapeHtml(conv.last_message || 'No messages yet')}</p>
                        </div>
                    `;
                    item.onclick = () => openConversation(conv.id, `Customer: ${displayName}`);
                    listEl.appendChild(item);
                });

                if (!activeConvId) {
                    const first = conversations[0];
                    openConversation(first.id, `Customer: ${first.customer_name || first.customer_id || 'Customer'}`);
                }
            } else {
                listEl.innerHTML = '<div class="p-4 text-xs text-navy-400 text-center">No conversations yet</div>';
            }
        } catch (err) {
            console.error("Error fetching customers:", err);
        }
    }
    window.loadCustomerList = loadCustomerList;

    // Manual Handover Back to AI (agent-side action)
    async function handoverToAI() {
        if (!activeConvId) return;
        alert('Conversation handed back to AI assistant.');
        loadCustomerList();
    }
    window.handoverToAI = handoverToAI;
</script>


