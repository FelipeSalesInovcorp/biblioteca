<?php

namespace App\Console\Commands;

use App\Actions\Carrinho\NotificarCarrinhosAbandonados as Action;
use Illuminate\Console\Command;

class NotificarCarrinhosAbandonados extends Command
{
    protected $signature = 'carrinho:notificar-abandonados {--hours=1}';
    protected $description = 'Envia email a cidadãos com carrinho abandonado após X horas';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $total = app(Action::class)->execute($hours);

        $this->info("Notificações enviadas: {$total}");

        return self::SUCCESS;
    }
}

