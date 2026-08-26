<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Sales Agent Chat · PRIORITY HANDLING";

include_once '../../includes/header.php';

$user_role = $_SESSION['user_role'] ?? 'sales_agent'; 

if ($user_role === 'sales_agent') {
    $current_user_id = $_SESSION['agent_id'] ?? 'agent-101';
} else {
    $current_user_id = $_SESSION['customer_id'] ?? 'cust-101';
}
?>

<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

<div class="w-full h-[calc(100vh-120px)] bg-slate-950 text-slate-100 flex overflow-hidden font-sans border border-slate-800/80 rounded-2xl shadow-2xl">
    <?php if ($user_role === 'sales_agent'): ?>
    <!-- ================= LEFT SIDEBAR: INBOX LIST ================= -->
    <aside class="w-80 lg:w-96 bg-slate-900/60 border-r border-slate-800 flex flex-col shrink-0">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-slate-800/80 flex justify-between items-center bg-slate-900/40">
            <div class="flex items-center gap-2">
                <i data-lucide="inbox" class="w-5 h-5 text-indigo-400"></i>
                <h2 class="font-bold text-slate-100 text-sm tracking-wide">Conversations</h2>
            </div>
            <button onclick="loadCustomerList()" class="p-1.5 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition">
                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="p-3 border-b border-slate-800/50">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-slate-500"></i>
                <input 
                    type="text" 
                    placeholder="Search client or ticket..." 
                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl pl-9 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500/80 transition"
                >
            </div>
        </div>

        <!-- Conversations Stream -->
        <div id="customerList" class="flex-1 overflow-y-auto divide-y divide-slate-800/40">
            <!-- Dynamic Conversations populated by JavaScript -->
        </div>
    </aside>
    <?php endif; ?>

    <!-- ================= MAIN CHAT PANEL ================= -->
    <main class="flex-1 flex flex-col bg-slate-950 relative">

        <!-- Chat Header Bar -->
        <header class="h-16 px-6 bg-slate-900/40 border-b border-slate-800 flex justify-between items-center backdrop-blur-md z-10">
            <div class="flex items-center gap-3">
                <div id="headerAvatar" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center font-bold text-sm shadow-lg shadow-indigo-500/20 text-white">
                    <?php echo $user_role === 'sales_agent' ? 'SF' : 'AI'; ?>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="chatHeaderTitle" class="font-semibold text-slate-100 text-sm">
                            <?php echo $user_role === 'sales_agent' ? 'Select a customer' : 'SwiftFreight AI Assistant'; ?>
                        </h3>
                        <span id="ticketBadge" class="hidden text-[10px] font-mono px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            #TQ-3391
                        </span>
                    </div>
                    <p id="chatHeaderSub" class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Active Session
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($user_role === 'sales_agent'): ?>
                    <button id="handoverBtn" onclick="handoverToAI()" class="hidden text-xs font-medium bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/30 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                        <i data-lucide="bot" class="w-3.5 h-3.5"></i>
                        Hand Back to AI
                    </button>
                    <button class="text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded-lg transition border border-slate-700 flex items-center gap-1">
                        Open Ticket
                    </button>
                <?php else: ?>
                    <button onclick="clearChatHistory()" class="text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 px-3 py-1.5 rounded-lg transition border border-rose-500/20">
                        Clear History
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <!-- Message Stream Area -->
        <div id="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-6 scrollbar-thin scrollbar-thumb-slate-800">
            <!-- Messages populated dynamically -->
        </div>

        <!-- Input Compose Box -->
        <div class="p-4 bg-slate-900/40 border-t border-slate-800">
            <form id="saasChatForm" class="flex gap-3 max-w-5xl mx-auto">
                <input 
                    type="text" 
                    id="saasMessageInput" 
                    placeholder="Type your message..." 
                    class="flex-1 bg-slate-900 border border-slate-800 focus:border-indigo-500/80 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none transition shadow-inner"
                    required
                >
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-6 py-3 rounded-xl text-sm transition flex items-center gap-2 shadow-lg shadow-indigo-600/30">
                    <span>Send</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </main>
</div>

<!-- ================= FRONTEND LOGIC ================= -->

</main>

<script>
      lucide.createIcons();

    const USER_ROLE = "<?php echo $user_role; ?>";
    const CURRENT_USER_ID = "<?php echo $current_user_id; ?>";
    const API_BASE_URL = "http://127.0.0.1:8000/api/v1/chat";

    let activeCustomerId = USER_ROLE === 'customer' ? CURRENT_USER_ID : null;

    // Component Initialization
    document.addEventListener("DOMContentLoaded", () => {
        if (USER_ROLE === 'sales_agent') {
            loadCustomerList();
        } else {
            fetchChatHistory(CURRENT_USER_ID);
        }
    });

    // Smart Bubble Component Generator
    function appendSaasBubble(text, role) {
        const container = document.getElementById('messagesContainer');
        const wrapper = document.createElement('div');
        
        let labelHtml = '';
        let styleClasses = '';
        let alignment = 'justify-start';

        if (role === 'user') {
            // Customer Style (Light SaaS container style in reference)
            labelHtml = `<span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mb-1.5 block">Customer</span>`;
            styleClasses = 'bg-slate-900/90 text-slate-200 border border-slate-800/80 rounded-2xl rounded-tl-sm p-4 max-w-xl shadow-lg';
        } else if (role === 'ai') {
            // AI Agent Style (Indigo tint with confidence metrics)
            labelHtml = `<span class="text-[10px] font-bold tracking-wider text-indigo-400 uppercase mb-1.5 block flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3 h-3"></i> AI Agent
                         </span>`;
            styleClasses = 'bg-indigo-950/40 border border-indigo-800/40 text-indigo-100 rounded-2xl rounded-tl-sm p-4 max-w-xl shadow-lg';
        } else {
            // Human Sales Agent Style (Accent Blue bubble - right aligned)
            alignment = 'justify-end';
            labelHtml = `<span class="text-[10px] font-bold tracking-wider text-indigo-200 uppercase mb-1.5 block text-right">Sales Agent</span>`;
            styleClasses = 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm p-4 max-w-xl shadow-xl shadow-indigo-600/20';
        }

        wrapper.className = `flex ${alignment} w-full`;
        wrapper.innerHTML = `
            <div class="${styleClasses}">
                ${labelHtml}
                <p class="text-sm leading-relaxed">${text}</p>
            </div>
        `;

        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        lucide.createIcons();
    }

    // Fetch Chat History
    async function fetchChatHistory(customerId) {
        try {
            const res = await fetch(`${API_BASE_URL}/history/${customerId}`);
            const data = await res.json();
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '';
            
            if (data.history) {
                data.history.forEach(item => {
                    const text = item.parts[0].text;
                    const role = item.role === 'user' ? 'user' : (item.is_human_agent ? 'agent' : 'ai');
                    appendSaasBubble(text, role);
                });
            }
        } catch (err) {
            console.error("Failed loading chat history:", err);
        }
    }

    // Form Submit Event Handler
    document.getElementById('saasChatForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('saasMessageInput');
        const msg = input.value.trim();
        if (!msg || !activeCustomerId) return;

        appendSaasBubble(msg, USER_ROLE === 'customer' ? 'user' : 'agent');
        input.value = '';

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
            
            if (USER_ROLE === 'customer' && data.reply) {
                appendSaasBubble(data.reply, 'ai');
            }
        } catch (err) {
            console.error("Failed sending message:", err);
        }
    });

    // Sales Agent Inbox Stream Loader
    async function loadCustomerList() {
        if (USER_ROLE !== 'sales_agent') return;

        try {
            const res = await fetch(`${API_BASE_URL}/active-conversations`);
            const customers = await res.json();
            const listEl = document.getElementById('customerList');
            listEl.innerHTML = '';

            customers.forEach(cust => {
                const item = document.createElement('div');
                item.className = "p-4 hover:bg-slate-800/50 cursor-pointer flex items-center gap-3.5 transition group border-l-2 border-transparent hover:border-indigo-500";
                item.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 text-indigo-400 group-hover:text-indigo-300 font-bold flex items-center justify-center text-xs shrink-0 shadow">
                        ${cust.customer_id.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <h4 class="text-xs font-semibold text-slate-200 truncate group-hover:text-white">${cust.customer_id}</h4>
                            <span class="text-[10px] text-slate-500">9:14 AM</span>
                        </div>
                        <p class="text-xs text-slate-400 truncate group-hover:text-slate-300">${cust.last_message}</p>
                    </div>
                `;
                item.onclick = () => selectCustomer(cust.customer_id);
                listEl.appendChild(item);
            });
        } catch (err) {
            console.error("Error loading conversations list:", err);
        }
    }

    // Select Customer Profile
    function selectCustomer(customerId) {
        activeCustomerId = customerId;
        document.getElementById('chatHeaderTitle').innerText = customerId;
        document.getElementById('headerAvatar').innerText = customerId.substring(0, 2).toUpperCase();
        document.getElementById('ticketBadge').classList.remove('hidden');
        document.getElementById('handoverBtn').classList.remove('hidden');
        fetchChatHistory(customerId);
    }

    // Trigger Handover Back to AI Agent
    async function handoverToAI() {
        if (!activeCustomerId) return;
        await fetch(`${API_BASE_URL}/handover/${activeCustomerId}`, { method: 'POST' });
        alert(`Successfully reassigned ${activeCustomerId} back to AI Agent.`);
    }

    // Clear Customer History
    async function clearChatHistory() {
        if (confirm("Are you sure you want to clear this conversation history?")) {
            await fetch(`${API_BASE_URL}/history/${CURRENT_USER_ID}`, { method: 'DELETE' });
            document.getElementById('messagesContainer').innerHTML = '';
        }
    }
</script>


<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>