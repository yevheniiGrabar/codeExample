<?php

namespace App\Imports;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocationImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Model|Location|null
     */
    public function model(array $row): Model|Location|null
    {
        return new Location(
            [
                'name' => $row['0'],
                'country' => $row['1'],
                'city' => $row['2'],
                'street' => $row['3'],
                'postal' => $row['4'],
            ]
        );
    }
}
