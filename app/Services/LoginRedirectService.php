<?php

namespace App\Services;

use App\Strategies\LoginRedirectStrategy;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Strategies\AdminVendorRedirect;
use App\Strategies\DefaultRedirect;

class LoginRedirectService
{
    public function resolve(User $user): LoginRedirectStrategy
    {
        if ($user->hasAnyRole([RolesEnum::Admin, RolesEnum::Vendor])) {
            return new AdminVendorRedirect();
        }

        return new DefaultRedirect();
    }
}
