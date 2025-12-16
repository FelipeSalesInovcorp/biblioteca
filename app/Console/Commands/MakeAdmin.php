<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'user:make-admin {email : Email do utilizador a promover}';
    protected $description = 'Promove um utilizador existente para Admin';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Nenhum utilizador encontrado com o email: {$email}");
            return self::FAILURE;
        }

        if ($user->role === 'admin') {
            $this->info("O utilizador {$email} já é admin.");
            return self::SUCCESS;
        }

        $user->role = 'admin';
        $user->save();

        $this->info("Utilizador {$email} promovido para admin com sucesso.");
        return self::SUCCESS;
    }
}

