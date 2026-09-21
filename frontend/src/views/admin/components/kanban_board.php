<div class="bg-[#F7F7F2] p-8 rounded-3xl mb-8 font-sans text-[#1A1A1A]">
  <!-- KANBAN BOARD  -->
  <div class="flex gap-6 overflow-x-auto pb-4 custom-scrollbar">

    <?php
    $columns = [
        ['key' => 'new_inquiry', 'title' => 'New Inquiry'],
        ['key' => 'qualifying', 'title' => 'Qualifying'],
        ['key' => 'negotiation', 'title' => 'Negotiation'],
        ['key' => 'quote_sent', 'title' => 'Quote Sent'],
        ['key' => 'closed_won', 'title' => 'Closed Won'],
        ['key' => 'closed_lost', 'title' => 'Closed Lost'],
    ];
    foreach ($columns as $col):
    ?>
      <!-- COLUMN -->
      <div class="flex-none w-80 flex flex-col gap-4">
        
        <!-- COLUMN HEADER -->
        <div class="flex items-center justify-between px-1">
          <h4 class="text-xl font-medium tracking-tight text-[#1A1A1A]"><?= $col['title'] ?></h4>
          <div class="flex items-center gap-2 border border-[#E5E5DF] bg-white px-3 py-1 rounded-xl text-sm font-medium">
            <span id="count-<?= $col['key'] ?>">0</span>
            <svg class="w-3.5 h-3.5 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
          </div>
        </div>

        <!-- CARDS CONTAINER -->
        <div id="col-<?= $col['key'] ?>" data-status="<?= $col['key'] ?>" class="kanban-dropzone flex flex-col gap-4 min-h-[500px]">
          <!-- Dynamic Cards -->
        </div>

      </div>
    <?php endforeach; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.min.js"></script>
<script src="../../../../../assets/js/admin/kanban_board.js"></script>