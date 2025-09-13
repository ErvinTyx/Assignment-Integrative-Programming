<?php

// app/Strategies/UserRedirect.php
namespace App\Strategies;

use App\Models\User;

class DefaultRedirect implements LoginRedirectStrategy
{
    public function redirect(User $user): string
    {
        return route('dashboard', absolute: false);
    }
}
