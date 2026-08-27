<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Sales Agent Chat - PRIORITY HANDLING";

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
<main class="flex-1 overflow-y-auto bg-[#f8fafc] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- CHAT WORKSPACE -->
  <div class="w-full h-[calc(100vh-180px)] bg-white border border-slate-200 rounded-2xl shadow-sm flex overflow-hidden">

    <?php if ($user_role === 'sales_agent'): ?>
    <!-- ================= LEFT SIDEBAR: INBOX LIST ================= -->
    <aside class="w-80 lg:w-96 bg-white border-r border-slate-200 flex flex-col shrink-0">
      <!-- Sidebar Header -->
      <div class="p-4 border-b border-slate-200 flex justify-between items-center">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center">
            <i data-lucide="inbox" class="w-5 h-5"></i>
          </div>
          <div>
            <h2 class="font-bold text-slate-800 text-sm leading-tight">Conversations</h2>
            <p class="text-[11px] text-slate-500">Priority Handling Inbox</p>
          </div>
        </div>
        <button onclick="loadCustomerList()" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-brand-blue transition" title="Refresh">
          <i data-lucide="rotate-cw" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Search Bar -->
      <div class="p-3 border-b border-slate-200">
        <div class="relative">
          <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-slate-400"></i>
          <input
            type="text"
            placeholder="Search client or ticket..."
            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition"
          >
        </div>
      </div>

      <!-- Conversations Stream -->
      <div id="customerList" class="flex-1 overflow-y-auto divide-y divide-slate-100">
        <!-- Dynamic Conversations populated by JavaScript -->
      </div>
    </aside>
    <?php endif; ?>

    <!-- ================= MAIN CHAT PANEL ================= -->
    <section class="flex-1 flex flex-col bg-slate-50/40 relative min-w-0">

      <!-- Chat Header Bar -->
      <header class="h-16 px-6 bg-white border-b border-slate-200 flex justify-between items-center z-10">
        <div class="flex items-center gap-3 min-w-0">
          <div id="headerAvatar" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-blue to-brand flex items-center justify-center font-bold text-sm shadow-sm shadow-brand-blue/30 text-white shrink-0">
            <?php echo $user_role === 'sales_agent' ? 'SF' : 'AI'; ?>
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <h3 id="chatHeaderTitle" class="font-semibold text-slate-800 text-sm truncate">
                <?php echo $user_role === 'sales_agent' ? 'Select a customer' : 'SwiftFreight AI Assistant'; ?>
              </h3>
              <span id="ticketBadge" class="hidden text-[10px] font-semibold px-2 py-0.5 rounded-md bg-brand-blue/10 text-brand-blue border border-brand-blue/20">
                #TQ-3391
              </span>
            </div>
            <p id="chatHeaderSub" class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5 truncate">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Active Session
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <?php if ($user_role === 'sales_agent'): ?>
            <button id="handoverBtn" onclick="handoverToAI()" class="hidden text-xs font-medium bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
              <i data-lucide="bot" class="w-3.5 h-3.5"></i>
              Hand Back to AI
            </button>
            <button class="text-xs font-medium bg-brand-blue hover:bg-brand-darkblue text-white px-3 py-1.5 rounded-lg transition border border-brand-blue flex items-center gap-1 shadow-sm">
              Open Ticket
            </button>
          <?php else: ?>
            <button onclick="clearChatHistory()" class="text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition border border-rose-200 bg-rose-50">
              Clear History
            </button>
          <?php endif; ?>
        </div>
      </header>

      <!-- Message Stream Area -->
      <div id="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-6">
        <!-- Empty State -->
        <div data-empty-state class="h-full flex flex-col items-center justify-center text-center text-slate-400 select-none">
          <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i data-lucide="messages-square" class="w-8 h-8 text-slate-300"></i>
          </div>
          <h4 class="text-sm font-semibold text-slate-600">No conversation selected</h4>
          <p class="text-xs text-slate-400 mt-1 max-w-xs">Choose a customer from the inbox to view the conversation and reply in real time.</p>
        </div>
      </div>

      <!-- Input Compose Box -->
      <div class="p-4 bg-white border-t border-slate-200">
        <form id="saasChatForm" class="flex gap-3 max-w-5xl mx-auto">
          <input 
            type="text" 
            id="saasMessageInput" 
            placeholder="Type your message..." 
            class="flex-1 bg-slate-50 border border-slate-200 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none transition"
            required
          >
          <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-medium px-6 py-3 rounded-xl text-sm transition flex items-center gap-2 shadow-sm shadow-brand-blue/20">
            <span>Send</span>
            <i data-lucide="send" class="w-4 h-4"></i>
          </button>
        </form>
      </div>

    </section>
  </div>

</main>

<script>
  lucide.createIcons();

  const USER_ROLE = "<?php echo $user_role; ?>";
  const CURRENT_USER_ID = "<?php echo $current_user_id; ?>";
  const API_BASE_URL = "http://127.0.0.1:8000/api/v1/chat";

  let activeCustomerId = USER_ROLE === 'customer' ? CURRENT_USER_ID : null;

  document.addEventListener("DOMContentLoaded", () => {
    if (USER_ROLE === 'sales_agent') {
      loadCustomerList();
    } else {
      fetchChatHistory(CURRENT_USER_ID);
    }
  });

  function appendSaasBubble(text, role) {
    const container = document.getElementById('messagesContainer');
    const empty = container.querySelector('[data-empty-state]');
    if (empty) empty.remove();

    const wrapper = document.createElement('div');
    let labelHtml = '';
    let styleClasses = '';
    let alignment = 'justify-start';

    if (role === 'user') {
      labelHtml = `<span class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-1.5 block">Customer</span>`;
      styleClasses = 'bg-slate-100 text-slate-800 border border-slate-200 rounded-2xl rounded-tl-sm p-4 max-w-xl shadow-sm';
    } else if (role === 'ai') {
      labelHtml = `<span class="text-[10px] font-bold tracking-wider text-brand uppercase mb-1.5 block flex items-center gap-1">
                      <i data-lucide="sparkles" class="w-3 h-3"></i> AI Agent
                   </span>`;
      styleClasses = 'bg-purple-50 border border-purple-200 text-purple-900 rounded-2xl rounded-tl-sm p-4 max-w-xl shadow-sm';
    } else {
      alignment = 'justify-end';
      labelHtml = `<span class="text-[10px] font-bold tracking-wider text-brand-blue uppercase mb-1.5 block text-right">Sales Agent</span>`;
      styleClasses = 'bg-brand-blue text-white rounded-2xl rounded-tr-sm p-4 max-w-xl shadow-md shadow-brand-blue/20';
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

  async function loadCustomerList() {
    if (USER_ROLE !== 'sales_agent') return;

    try {
      const res = await fetch(`${API_BASE_URL}/active-conversations`);
      const customers = await res.json();
      const listEl = document.getElementById('customerList');
      listEl.innerHTML = '';

      customers.forEach(cust => {
        const item = document.createElement('div');
        item.className = "p-4 hover:bg-slate-50 cursor-pointer flex items-center gap-3.5 transition group border-l-2 border-transparent hover:border-brand-blue border-b border-slate-100";
        const initials = cust.customer_id.substring(0, 2).toUpperCase();
        item.innerHTML = `
          <div class="w-10 h-10 rounded-xl bg-brand-blue/10 border border-brand-blue/20 text-brand-blue font-bold flex items-center justify-center text-xs shrink-0">
            ${initials}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-baseline mb-1">
              <h4 class="text-sm font-semibold text-slate-800 truncate group-hover:text-brand-blue transition">${cust.customer_id}</h4>
              <span class="text-[10px] text-slate-400 shrink-0 ml-2">9:14 AM</span>
            </div>
            <p class="text-xs text-slate-500 truncate">${cust.last_message}</p>
          </div>
        `;
        item.onclick = () => selectCustomer(cust.customer_id);
        listEl.appendChild(item);
      });
    } catch (err) {
      console.error("Error loading conversations list:", err);
    }
  }

  function selectCustomer(customerId) {
    activeCustomerId = customerId;
    document.getElementById('chatHeaderTitle').innerText = customerId;
    document.getElementById('headerAvatar').innerText = customerId.substring(0, 2).toUpperCase();
    document.getElementById('ticketBadge').classList.remove('hidden');
    document.getElementById('handoverBtn').classList.remove('hidden');
    fetchChatHistory(customerId);
  }

  async function handoverToAI() {
    if (!activeCustomerId) return;
    await fetch(`${API_BASE_URL}/handover/${activeCustomerId}`, { method: 'POST' });
    alert(`Successfully reassigned ${activeCustomerId} back to AI Agent.`);
  }

  async function clearChatHistory() {
    if (confirm("Are you sure you want to clear this conversation history?")) {
      await fetch(`${API_BASE_URL}/history/${CURRENT_USER_ID}`, { method: 'DELETE' });
      document.getElementById('messagesContainer').innerHTML = '';
    }
  }
</script>


<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

