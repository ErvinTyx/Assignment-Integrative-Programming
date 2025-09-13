<?php

// app/Strategies/LoginRedirectStrategy.php
namespace App\Strategies;

use App\Models\User;

interface LoginRedirectStrategy
{
    public function redirect(User $user): string;
}
