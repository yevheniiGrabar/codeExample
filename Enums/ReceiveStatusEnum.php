<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReceiveStatusEnum extends Enum
{
    const NEW = 0;
    const PARTIALLY_COMPLETED = 1;
    const RECEIVED = 2;
}
