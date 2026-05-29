<?php if ($message = flash('success')): ?>
    <div class="toast show success" role="status">
        <span class="toast-dot"></span>
        <span><?= e($message) ?></span>
    </div>
<?php endif; ?>

<?php if ($message = flash('error')): ?>
    <div class="toast show error" role="alert">
        <span class="toast-dot"></span>
        <span><?= e($message) ?></span>
    </div>
<?php endif; ?>

