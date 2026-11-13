<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;

class InscricaoPolicy
{
    public function view(User $user, Inscricao $inscricao): bool
    {
        return $user->id === $inscricao->user_id;
    }

    public function delete(User $user, Inscricao $inscricao): bool
    {
        return $user->id === $inscricao->user_id;
    }
}
