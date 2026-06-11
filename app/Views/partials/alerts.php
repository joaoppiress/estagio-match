<?php if ($message = flash('success')): ?>
    <div class="toast show success" role="status" tabindex="0">
        <span class="toast-dot"></span>
        <span><?= e($message) ?></span>
        <button class="toast-close" type="button" aria-label="Fechar aviso">&times;</button>
    </div>
<?php endif; ?>

<?php if ($message = flash('error')): ?>
    <div class="toast show error" role="alert" tabindex="0">
        <span class="toast-dot"></span>
        <span><?= e($message) ?></span>
        <button class="toast-close" type="button" aria-label="Fechar aviso">&times;</button>
    </div>
<?php endif; ?>
