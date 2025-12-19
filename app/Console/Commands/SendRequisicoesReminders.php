<?php

namespace App\Console\Commands;

use App\Mail\RequisicaoReminderCidadao;
use App\Models\Requisicao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendRequisicoesReminders extends Command
{
    protected $signature = 'requisicoes:send-reminders {--date= : Data de referência YYYY-MM-DD (para testes)}';
    protected $description = 'Envia reminder de entrega (dia anterior) para requisições ativas';

    public function handle(): int
    {
        $ref = $this->option('date')
            /*? now()->parse($this->option('date'))*/
            ? Carbon::parse($this->option('date'))
            : now();

        $amanha = $ref->copy()->addDay()->toDateString();

        $requisicoes = Requisicao::with(['user', 'livro'])
            ->whereNull('data_entrega_real')
            ->whereDate('data_prevista_fim', $amanha)
            ->whereNull('reminder_enviado_em')
            ->get();

        $count = 0;

        foreach ($requisicoes as $r) {
            Mail::to($r->user->email)->queue(new RequisicaoReminderCidadao($r));
            $r->update(['reminder_enviado_em' => now()]);
            $count++;
        }

        $this->info("Reminders enviados: {$count} (data alvo: {$amanha})");

        return self::SUCCESS;
    }
}
