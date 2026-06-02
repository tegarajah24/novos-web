<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'Super Admin';
    case Manager = 'Manager';
    case Admin = 'Admin';
    case Design = 'Design';
    case Produksi = 'Produksi';
    case Customer = 'Customer';
}