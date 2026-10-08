<?php

namespace App\Enums;

enum PosPaymentMethod: string
{
    case Cash = 'cash';
    case VietQr = 'vietqr';
}
