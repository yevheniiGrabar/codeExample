<?php

namespace App\Exports;

use App\Models\InventoryStockMovement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransfersExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $transfers;

    /**
     * @param $stockTransfers
     */
    public function __construct($stockTransfers)
    {
        $this->transfers = $stockTransfers;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->transfers;
    }

    public function map($row): array
    {
        return [
            $row->product->id,
            $row->product->name,
            $row->locationFrom->name,
            $row->sectionFrom?->section_name,
            $row->locationTo->name,
            $row->sectionTo?->section_name,
            $row->quantity,
            $row->user->name,
            $row->remarks,
            $row->date
        ];
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product name',
            'Location from',
            'Section from',
            'Location to',
            'Section to',
            'Transferred quantity',
            'User',
            'Remarks',
            'Date',
        ];
    }
}
