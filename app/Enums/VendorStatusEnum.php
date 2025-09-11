<?php

namespace App\Enums;

enum VendorStatusEnum: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
        };
    }

    public static function colors(): array
    {
        return [
            'gray' => self::Pending->value,
            'success' => self::Approved->value,
            'danger' => self::Rejected->value,
        ];
    }
}
