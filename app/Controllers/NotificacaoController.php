<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Notification;

final class NotificacaoController extends Controller
{
    public function markRead(): void
    {
        Auth::requireLogin();

        (new Notification())->markRead((int) ($_POST['notificacao_id'] ?? 0), (int) Auth::id());
        redirect(back_url());
    }
}
