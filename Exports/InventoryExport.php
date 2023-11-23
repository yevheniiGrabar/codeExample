<?php

namespace App\Exports;

use App\Models\Inventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->inventories;
    }

    public function map($row): array
    {
        $result = [];

        $data = $row->toArray();

        if (isset($data['locations'])) {
            foreach ($data['locations'] as $location) {
                if (isset($location['sections'])) {
                    foreach ($location['sections'] as $section) {
                        $result[] = [
                            $row->name,
                            $row->category->name,
                            $row->cost_price,
                            $location['store'],
                            $section['name'],
                            $section['quantity'],
                            $row->created_at,
                            $row->updated_at,
                        ];
                    }
                }
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Purchase Price',
            'Location',
            'Sub Location',
            'Quantity',
        ];
    }
}

