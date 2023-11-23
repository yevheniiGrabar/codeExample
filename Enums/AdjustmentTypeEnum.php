<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AdjustmentTypeEnum extends Enum
{
    const QUANTITY = 0;
    const COST_PRICE = 1;
}
