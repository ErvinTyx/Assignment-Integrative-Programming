<?php

namespace App\Enums;

enum VendorStatusEnum : string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Pending = 'Pending';
}
