<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kunin ang role at ID ng naka-login na user sa PHP session
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer'; // 'customer' o 'sales_agent'
// 2. Kunin ang tamang ID batay sa role ng naka-login:
if ($user_role === 'sales_agent') {
    $current_user_id = $_SESSION['agent_id'] ?? 'agent-101';
} else {
    $current_user_id = $_SESSION['customer_id'] ?? 'cust-101';
}
?>

<!-- FLOATING CHAT BUTTON -->
<div class="fixed bottom-5 right-5 z-50">
    <button id="toggleChatBtn" class="relative bg-brand-blue hover:bg-brand-darkblue text-white p-4 rounded-full shadow-lg transition flex items-center justify-center">
        <i data-lucide="message-square" class="w-6 h-6"></i>
        
        <!-- COUNTER BADGE PARA SA SALES AGENT -->
        <?php if ($user_role === 'sales_agent'): ?>
            <span id="unreadBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-bounce">
                0
            </span>
        <?php endif; ?>
    </button>
</div>

<!-- CHAT MAIN WINDOW -->
<div id="chatModal" class="hidden fixed bottom-20 right-5 z-50 w-[95vw] md:w-[750px] h-[550px] bg-white border border-slate-200 rounded-2xl shadow-lg flex overflow-hidden">

    <?php if ($user_role === 'sales_agent'): ?>
        <!-- SIDEBAR INBOX (SALES AGENT ONLY) -->
        <div class="w-1/3 bg-slate-50 border-r border-slate-200 flex flex-col">
            <div class="p-3 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-bold text-slate-800 text-sm">Customer Inbox</h2>
                <button onclick="loadCustomerList()" class="text-xs text-blue-400 hover:underline">Refresh</button>
            </div>
            <!-- LIST NG MGA MGA CUSTOMER NA NAG-CHAT -->
            <div id="customerList" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <!-- Dito pino-populate ng JS ang profile items -->
            </div>
        </div>
    <?php endif; ?>

    <!-- CHAT BOX WINDOW (COMMON) -->
    <div class="flex-1 flex flex-col bg-slate-50/40">
        
        <!-- Header -->
        <div class="p-3 bg-white border-b border-slate-200 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <h3 id="chatHeaderTitle" class="font-semibold text-slate-800 text-sm">
                    <?php echo $user_role === 'sales_agent' ? 'Choose Customer' : 'SwiftFreight AI Support'; ?>
                </h3>
            </div>
            
            <div class="flex gap-2">
                <?php if ($user_role === 'sales_agent'): ?>
                    <!-- BUTTON PARA IBALIK AGAD KAY AI -->
                    <button id="handoverBtn" onclick="handoverToAI()" class="hidden text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200 px-2 py-1 rounded transition">
                        Hand Back to AI
                    </button>
                <?php endif; ?>

                <?php if ($user_role === 'customer'): ?>
                    <button id="clearHistoryBtn" onclick="clearChatHistory()" class="text-xs text-red-400 hover:text-red-300">Clear Chat</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="messagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3 text-sm">
            <!-- Dito lumalabas ang chat bubbles -->
        </div>

        <!-- Input Area -->
        <form id="widgetChatForm" class="p-3 bg-white border-t border-slate-200 flex gap-2">
            <input 
                type="text" 
                id="widgetMessageInput" 
                placeholder="I-type ang mensahe..." 
                class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"
                required
            >
            <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Send
            </button>
        </form>

    </div>
</div>

<!-- JAVASCRIPT INTEGRATION -->
<script>
    lucide.createIcons();

    const USER_ROLE = "<?php echo $user_role; ?>";
    const CURRENT_USER_ID = "<?php echo $current_user_id; ?>";
    const API_BASE_URL = "http://127.0.0.1:8000/api/v1/chat";

    let activeCustomerId = USER_ROLE === 'customer' ? CURRENT_USER_ID : null;

    const toggleChatBtn = document.getElementById('toggleChatBtn');
    const chatModal = document.getElementById('chatModal');
    const messagesContainer = document.getElementById('messagesContainer');
    const widgetChatForm = document.getElementById('widgetChatForm');
    const widgetMessageInput = document.getElementById('widgetMessageInput');

    // Toggle Chat Window
    toggleChatBtn.addEventListener('click', () => {
        chatModal.classList.toggle('hidden');
        if (USER_ROLE === 'sales_agent') {
            loadCustomerList();
        } else if (USER_ROLE === 'customer') {
            fetchChatHistory(CURRENT_USER_ID);
        }
    });

    // Render Chat Bubbles
    function appendBubble(text, senderRole) {
        const div = document.createElement('div');
        const isMe = (USER_ROLE === 'customer' && senderRole === 'user') || (USER_ROLE === 'sales_agent' && senderRole === 'model');
        
        div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
        div.innerHTML = `
            <div class="${isMe ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-800 border border-slate-200'} p-2.5 rounded-xl max-w-[80%]">
                ${text}
            </div>
        `;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Load Chat History mula sa MongoDB Endpoint
    async function fetchChatHistory(customerId) {
        try {
            const res = await fetch(`${API_BASE_URL}/history/${customerId}`);
            const data = await res.json();
            messagesContainer.innerHTML = '';
            
            if (data.history) {
                data.history.forEach(item => {
                    appendBubble(item.parts[0].text, item.role);
                });
            }
        } catch (err) {
            console.error("Failed to load history:", err);
        }
    }

    // Submit Chat Message
    widgetChatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = widgetMessageInput.value.trim();
        if (!msg || !activeCustomerId) return;

        appendBubble(msg, USER_ROLE === 'customer' ? 'user' : 'model');
        widgetMessageInput.value = '';

        try {
            const res = await fetch(API_BASE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customer_id: activeCustomerId,
                    message: msg,
                    sender_role: USER_ROLE
                })
            });
            const data = await res.json();
            
            // Ipakita ang sagot ni AI kung Customer ang nag-chat
            if (USER_ROLE === 'customer' && data.reply) {
                appendBubble(data.reply, 'model');
            }
        } catch (err) {
            console.error("Error sending message:", err);
        }
    });

    // FOR SALES AGENT: Kuhanin ang active customer list
    async function loadCustomerList() {
        if (USER_ROLE !== 'sales_agent') return;

        try {
            const res = await fetch(`${API_BASE_URL}/active-conversations`);
            const customers = await res.json();

            const listEl = document.getElementById('customerList');
            const unreadBadge = document.getElementById('unreadBadge');
            listEl.innerHTML = '';

            if (customers.length > 0) {
                unreadBadge.innerText = customers.length;
                unreadBadge.classList.remove('hidden');
            }

            customers.forEach(cust => {
                const item = document.createElement('div');
                item.className = "p-3 hover:bg-slate-100 cursor-pointer flex items-center gap-3 transition border-b border-slate-100";
                item.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold text-xs">
                        ${cust.customer_id.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-xs font-semibold text-brand-blue truncate">${cust.customer_id}</p>
                        <p class="text-[11px] text-gray-400 truncate">${cust.last_message}</p>
                    </div>
                `;
                item.onclick = () => selectCustomerForSales(cust.customer_id);
                listEl.appendChild(item);
            });
        } catch (err) {
            console.error("Error fetching customers:", err);
        }
    }

    // Pumili ng Customer na kakausapin (Sales Agent)
    function selectCustomerForSales(customerId) {
        activeCustomerId = customerId;
        document.getElementById('chatHeaderTitle').innerText = `Customer: ${customerId}`;
        document.getElementById('handoverBtn').classList.remove('hidden');
        fetchChatHistory(customerId);
    }

    // Manual Handover Back to AI Button
    async function handoverToAI() {
        if (!activeCustomerId) return;
        await fetch(`${API_BASE_URL}/handover/${activeCustomerId}`, { method: 'POST' });
        alert(`Binalik na ang chat ni ${activeCustomerId} kay Gemini AI.`);
    }

    // Clear History Button
    async function clearChatHistory() {
        if (confirm("Gusto mo bang linisin ang chat history?")) {
            await fetch(`${API_BASE_URL}/history/${CURRENT_USER_ID}`, { method: 'DELETE' });
            messagesContainer.innerHTML = '';
        }
    }
</script>