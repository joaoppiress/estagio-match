<?php

use App\Core\Auth;
use App\Models\User;

$active = $active ?? current_route();
$authUser = Auth::user();
$initials = $authUser ? (new User())->initials($authUser) : 'EM';

$isActive = static fn (string $route): string => $active === $route ? 'active' : '';
?>
<nav class="topnav">
    <a class="logo" href="<?= e(route_url('home')) ?>" aria-label="EstágioMatch">
        <span class="logo-icon">EM</span>
        Estágio<span>Match</span>
    </a>

    <div class="nav-links">
        <?php if ($authUser): ?>
            <a class="nav-link <?= e($isActive('dashboard')) ?>" href="<?= e(route_url('dashboard')) ?>">Dashboard</a>
            <a class="nav-link <?= e($isActive('vagas')) ?>" href="<?= e(route_url('vagas')) ?>">Vagas</a>
            <?php if (Auth::isStudent()): ?>
                <a class="nav-link <?= e($isActive('perfil')) ?>" href="<?= e(route_url('perfil')) ?>">Meu Perfil</a>
            <?php endif; ?>
            <?php if (Auth::isCompany()): ?>
                <a class="nav-link <?= e($isActive('empresa/vagas/nova')) ?>" href="<?= e(route_url('empresa/vagas/nova')) ?>">Publicar vaga</a>
            <?php endif; ?>
        <?php else: ?>
            <a class="nav-link <?= e($isActive('vagas')) ?>" href="<?= e(route_url('vagas')) ?>">Vagas</a>
            <a class="nav-link" href="<?= e(route_url('home')) ?>#como-funciona">Como funciona</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if ($authUser): ?>
            <span class="avatar" title="<?= e($authUser['name']) ?>"><?= e($initials) ?></span>
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

