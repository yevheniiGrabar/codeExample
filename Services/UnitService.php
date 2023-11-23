<?php

namespace App\Services;

use App\Models\Unit;

class UnitService
{
    /**
     * @param array $data
     * @return Unit
     */
    public function createUnit(array $data): Unit
    {
        $unit = new Unit;

        $unit->name = $data['name'];
        $unit->barcode = $data['barcode'] ?? null;

        $unit->save();

        return $unit;
    }

    /**
     * @param Unit $unit
     * @param array $data
     * @return Unit
     */
    public function updateUnit(Unit $unit, array $data): Unit
    {
        $unit->update([
            'name' => $data['name'] ?? $unit->name,
            'barcode' => $data['barcode'] ?? $unit->barcode,
        ]);

        $unit->save();

        return $unit;
    }
}
