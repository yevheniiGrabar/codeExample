<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockCountExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var Collection
     */
    protected Collection $stockCounts;

    public function __construct($stockCounts)
    {
        $this->stockCounts = $stockCounts;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->stockCounts;
    }

    public function map($row): array
    {
        $status = match ($row->status) {
            1 => "New",
            2 => "Approved",
            default => "Declined",
        };

        $exportRows = [];

        foreach ($row->stockCountProduct as $stockCountProduct) {
            $exportRow = [
                'Stock Count ID' => $row->id,
                'Worker name' => $row->user->name,
                'Worker lastname' => $row->user->last_name,
                'Location' => $row->location->name,
                'Status' => $status,
                'Declination comment' => $row->declination_comment ?? '',
                'Date' => $row->date,
                'Product ID' => $stockCountProduct->product->id,
                'Product name' => $stockCountProduct->product->name,
                'Section' => $stockCountProduct->subLocation->section_name ?? '',
                'Counted quantity' => $stockCountProduct->counted_quantity,
                'System quantity' => $stockCountProduct->getInStock(),
            ];

            $exportRows[] = $exportRow;
        }

        return $exportRows;
    }

    public function headings(): array
    {
        return [
            'Stock Count ID',
            'Worker name',
            'Worker lastname',
            'Location',
            'Status',
            'Declination comment',
            'Date',
            'Product ID',
            'Product name',
            'Section',
            'Counted quantity',
            'System quantity',
        ];
    }
}
