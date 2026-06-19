<?php
// এটা require করার আগে icons.php এবং lang.php require করা থাকতে হবে।
?>
<div id="compatModal" class="modal-overlay" onclick="if(event.target===this) closeModal('compatModal')">
    <div class="modal-box">
        <div class="modal-head">
            <h3><?php echo icon('droplet', 'ic-md'); ?> <?php echo htmlspecialchars(t('compat_modal_title')); ?></h3>
            <button type="button" onclick="closeModal('compatModal')" class="modal-close"><?php echo icon('close', 'ic-sm'); ?></button>
        </div>
        <p class="muted" style="margin:0 0 10px;"><?php echo htmlspecialchars(t('compat_modal_sub')); ?></p>
        <div style="overflow-x:auto;">
        <table class="compat-table">
            <tr><th>↓→</th><th>A+</th><th>A-</th><th>B+</th><th>B-</th><th>AB+</th><th>AB-</th><th>O+</th><th>O-</th></tr>
            <tr><th>A+</th><td>✓</td><td></td><td></td><td></td><td>✓</td><td></td><td></td><td></td></tr>
            <tr><th>A-</th><td>✓</td><td>✓</td><td></td><td></td><td>✓</td><td>✓</td><td></td><td></td></tr>
            <tr><th>B+</th><td></td><td></td><td>✓</td><td></td><td>✓</td><td></td><td></td><td></td></tr>
            <tr><th>B-</th><td></td><td></td><td>✓</td><td>✓</td><td>✓</td><td>✓</td><td></td><td></td></tr>
            <tr><th>AB+</th><td></td><td></td><td></td><td></td><td>✓</td><td></td><td></td><td></td></tr>
            <tr><th>AB-</th><td></td><td></td><td></td><td></td><td>✓</td><td>✓</td><td></td><td></td></tr>
            <tr><th>O+</th><td>✓</td><td></td><td>✓</td><td></td><td>✓</td><td></td><td>✓</td><td></td></tr>
            <tr><th>O-</th><td>✓</td><td>✓</td><td>✓</td><td>✓</td><td>✓</td><td>✓</td><td>✓</td><td>✓</td></tr>
        </table>
        </div>
        <p class="muted" style="margin-top:10px; font-size:12px;">
            <?php echo icon('check-circle', 'ic-xs'); ?> <?php echo htmlspecialchars(t('compat_universal_donor')); ?>
            &nbsp;&middot;&nbsp;
            <?php echo icon('check-circle', 'ic-xs'); ?> <?php echo htmlspecialchars(t('compat_universal_recipient')); ?>
        </p>
    </div>
</div>
