<?php
/* ==========================================================================
   RATING MODAL  —  New_dash/components/rating_modal.php
   --------------------------------------------------------------------------
   Star rating + feedback dialog for completed deliveries. Built from the
   .crm-overlay / .crm-modal / .crm-btn layer in assets/css/theme.css so it
   matches the logout modal in src/includes/footer.php.

   js/dashboard.js owns the behaviour: it opens this dialog from a
   "Rate delivery" button, tracks the hovered/selected star, and stores the
   submitted rating in localStorage. Nothing is sent to the backend API.
   -------------------------------------------------------------------------- */
?>
<div id="ratingOverlay" class="crm-overlay opacity-0 pointer-events-none transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="ratingModalTitle">
  <div class="crm-modal w-full max-w-md !rounded-2xl !shadow-lift" id="ratingModal">

    <!-- Head -->
    <div class="crm-modal-head">
      <div class="min-w-0">
        <h3 id="ratingModalTitle" class="crm-panel-title">Rate this delivery</h3>
        <span class="crm-panel-sub">
          Delivery <span id="ratingWaybill" class="font-semibold text-navy-700 dark:text-slate-200">&mdash;</span>
          &bull; <span id="ratingRoute">&mdash;</span>
        </span>
      </div>
      <button type="button" data-rating-close class="crm-icon-btn !h-8 !w-8 shrink-0" aria-label="Close">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <!-- Body -->
    <div class="crm-modal-body space-y-5">

      <!-- Stars -->
      <div>
        <span class="crm-label">Your rating</span>
        <div id="ratingStars" class="flex items-center gap-1.5" role="group" aria-label="Star rating">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <button type="button"
                    class="dlv-star-btn"
                    data-star="<?= $i ?>"
                    role="radio"
                    aria-checked="false"
                    aria-label="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">
              <i class="fa-solid fa-star" aria-hidden="true"></i>
            </button>
          <?php endfor; ?>
          <span id="ratingWord" class="ml-2 text-xs font-bold text-navy-500 dark:text-slate-400">Select a score</span>
        </div>
        <p id="ratingError" class="hidden mt-2 text-xs font-semibold text-rose-600 dark:text-rose-400">
          Pick at least one star before submitting.
        </p>
      </div>

      <!-- Quick tags -->
      <div>
        <span class="crm-label">What stood out? (optional)</span>
        <div id="ratingTags" class="flex flex-wrap gap-2">
          <?php foreach (['On time', 'Careful handling', 'Friendly courier', 'Tracking was accurate', 'Photo POD provided'] as $tag): ?>
            <button type="button" class="crm-pill" data-tag="<?= htmlspecialchars($tag, ENT_QUOTES) ?>" aria-pressed="false">
              <?= htmlspecialchars($tag) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Comment -->
      <div>
        <label class="crm-label" for="ratingComment">Comments (optional)</label>
        <textarea id="ratingComment" class="crm-textarea !min-h-[84px] resize-y" rows="3"
                  maxlength="400"
                  placeholder="Tell the courier desk how the handover went..."></textarea>
        <p class="mt-1.5 text-[11px] text-slate-400 dark:text-slate-500">
          Feedback is saved in this browser only &mdash; the delivery API is not connected yet.
        </p>
      </div>
    </div>

    <!-- Foot -->
    <div class="crm-modal-foot">
      <button type="button" data-rating-close class="crm-btn crm-btn-ghost !h-9 !text-xs">Cancel</button>
      <button type="button" id="ratingSubmitBtn" class="crm-btn crm-btn-primary !h-9 !text-xs">
        <i class="fa-solid fa-paper-plane text-[10px]"></i>
        Submit feedback
      </button>
    </div>
  </div>
</div>
