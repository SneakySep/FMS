<?php
/**
 * Shared brand logo.
 * Renders the Priority Handling Logistics logo (Source/image/logo.png).
 * Override per-call with: $logoPath, $logoClass, $logoAlt
 */
$logoPath  = $logoPath  ?? '../Source/image/logo.png';
$logoClass = $logoClass ?? 'h-10 w-10 object-contain rounded-xl shadow-lg shadow-blue-500/30';
$logoAlt   = $logoAlt   ?? 'Priority Handling Logistics';
?>
<img src="<?php echo htmlspecialchars($logoPath); ?>"
     alt="<?php echo htmlspecialchars($logoAlt); ?>"
     class="<?php echo htmlspecialchars($logoClass); ?>">
