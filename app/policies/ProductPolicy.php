<?php

namespace App\policies;

use App\Enums\RolesEnum;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function create(User $user): bool
    {
        // vendors and admins can create
        return $user->hasAnyRole([RolesEnum::Vendor, RolesEnum::Admin]);
    }

    public function update(User $user, Product $product): bool
    {
        // owner (created_by) or admin can update
        return $user->id === $product->created_by || $user->hasRole(RolesEnum::Admin);
    }

    public function delete(User $user, Product $product): bool
    {
        // owner (created_by) or admin can delete
        return $user->id === $product->created_by || $user->hasRole(RolesEnum::Admin);
    }

    public function view(User $user, Product $product): bool
    {
        /* owner/admin can view anything; others only published & vendor-approved
        if ($user->id === $product->created_by || $user->hasRole(RolesEnum::Admin)) {
            return true;
        }
        return $product->status->value === 'published'; // adjust if enum instance*/
        return $user->id === $product->created_by || $user->hasRole(RolesEnum::Admin);
    }
}
