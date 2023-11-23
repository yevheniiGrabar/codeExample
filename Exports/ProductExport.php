<?php

namespace App\Exports;

use App\Models\LocationProduct;
use App\Models\Product;
use App\Traits\CurrentCompany;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Product::all();
    }

    public function map($row): array
    {
        $data = [];

        $defaultCompany = CurrentCompany::getDefaultCompany();
        $defaultLocation = LocationProduct::query()
            ->where('product_id', $row->id)
            ->where('company_id', $defaultCompany->company_id)
            ->get();

        $locationName = $defaultLocation?->mapWithKeys(function ($locations) {
            return [
                'name' => $locations->first()->locations->name,
            ];
        })->get('name');

        if (empty($this->columns)) {
            $data = [
                $row->name,
                $row->product_code,
                $row->barcode,
                $row->unit?->name ?? '-',
                $row->category?->name ?? '-',
                $locationName,
                $row->supplier?->company_name ?? '-',
                $row->tax?->amount ?? '-',
                $row->cost_price,
                $row->selling_price,
            ];
        } else {
            if (in_array('name', $this->columns)) {
                $data[] = $row->name;
            }

            if (in_array('product_code', $this->columns)) {
                $data[] = $row->product_code;
            }

            if (in_array('barcode', $this->columns)) {
                $data[] = $row->barcode;
            }

            if (in_array('unit', $this->columns)) {
                $data[] = $row->unit->name;
            }

            if (in_array('group', $this->columns)) {
                $data[] = $row->category->name;
            }

            if (in_array('location', $this->columns)) {
                $data[] = $locationName;
            }

            if (in_array('supplier', $this->columns)) {
                $data[] = $row->supplier->company_name;
            }

            if (in_array('tax', $this->columns)) {
                $data[] = $row->tax->amount;
            }

            if (in_array('purchase_price', $this->columns)) {
                $data[] = $row->cost_price;
            }

            if (in_array('selling_price', $this->columns)) {
                $data[] = $row->selling_price;
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Product name',
            'Product code',
            'Product barcode',
            'Unit',
            'Group',
            'Default location',
            'Primary supplier',
            'Default tax',
            'Purchase price',
            'Selling price',
        ];

        if (!empty($this->columns)) {
            $filtered_headings = [];
            foreach ($headings as $heading) {
                if (in_array(strtolower(str_replace(' ', '_', $heading)), $this->columns)) {
                    $filtered_headings[] = $heading;
                } else {
                    $filtered_headings[] = '';
                }
            }
            return $filtered_headings;
        }

        return $headings;
    }
}
