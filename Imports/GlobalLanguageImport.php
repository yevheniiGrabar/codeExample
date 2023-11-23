<?php

namespace App\Imports;

use App\Models\GlobLanguage;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class GlobalLanguageImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row): ?Model
    {
        return GlobLanguage::query()->create([
            'translated_field_en' => $row[0],
            'translated_field_no' => $row[1],
        ]);
    }


}
