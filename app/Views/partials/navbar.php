<?php

use App\Core\Auth;
use App\Models\User;

$active = $active ?? current_route();
$authUser = Auth::user();
$initials = $authUser ? (new User())->initials($authUser) : 'EM';

$isActive = static fn (string $route): string => $active === $route ? 'active' : '';
$ariaCurrent = static fn (string $route): string => $active === $route ? ' aria-current="page"' : '';
?>
<nav class="topnav" aria-label="Navegação principal">
    <a class="logo" href="<?= e(route_url('home')) ?>" aria-label="EstágioMatch — página inicial">
        <span class="logo-icon" aria-hidden="true">EM</span>
        Estágio<span>Match</span>
    </a>

    <button class="nav-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="nav-links">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <div class="nav-links" id="nav-links">
        <?php if ($authUser): ?>
            <a class="nav-link <?= e($isActive('dashboard')) ?>"<?= $ariaCurrent('dashboard') ?> href="<?= e(route_url('dashboard')) ?>">Dashboard</a>
            <a class="nav-link <?= e($isActive('vagas')) ?>"<?= $ariaCurrent('vagas') ?> href="<?= e(route_url('vagas')) ?>">Vagas</a>
            <?php if (Auth::isStudent()): ?>
                <a class="nav-link <?= e($isActive('perfil')) ?>"<?= $ariaCurrent('perfil') ?> href="<?= e(route_url('perfil')) ?>">Meu Perfil</a>
            <?php endif; ?>
            <?php if (Auth::isCompany()): ?>
                <a class="nav-link <?= e($isActive('empresa/vagas/nova')) ?>"<?= $ariaCurrent('empresa/vagas/nova') ?> href="<?= e(route_url('empresa/vagas/nova')) ?>">Publicar vaga</a>
            <?php endif; ?>
        <?php else: ?>
            <a class="nav-link <?= e($isActive('vagas')) ?>"<?= $ariaCurrent('vagas') ?> href="<?= e(route_url('vagas')) ?>">Vagas</a>
            <a class="nav-link" href="<?= e(route_url('home')) ?>#como-funciona">Como funciona</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if ($authUser): ?>
            <span class="avatar" title="<?= e($authUser['name']) ?>" aria-label="Conectado como <?= e($authUser['name']) ?>"><?= e($initials) ?></span>
            <form method="post" action="<?= e(action_url()) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="acao" value="logout">
                <button class="btn btn-outline" type="submit">Sair</button>
            </form>
        <?php else: ?>
            <a class="btn btn-outline" href="<?= e(route_url('login')) ?>">Entrar</a>
            <a class="btn btn-primary" href="<?= e(route_url('login')) ?>#cadastro">Cadastrar grátis</a>
        <?php endif; ?>
    </div>
</nav>
