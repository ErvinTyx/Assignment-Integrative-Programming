<?php

// app/Strategies/AdminRedirect.php
namespace App\Strategies;

use App\Models\User;

class AdminVendorRedirect implements LoginRedirectStrategy
{
    public function redirect(User $user): string
    {
        return '/admin';
    }
}
