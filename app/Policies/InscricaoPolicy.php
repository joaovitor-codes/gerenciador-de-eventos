<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;

class InscricaoPolicy
{
    public function delete(User $user, Inscricao $inscricao): bool
    {
        return $user->id === $inscricao->user_id;
    }
}
