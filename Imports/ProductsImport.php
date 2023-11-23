<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;


class ProductsImport implements ToCollection, WithHeadingRow
{
    protected mixed $mapping;

    public function __construct($mapping = null)
    {
        $this->mapping = $mapping;
    }

    public function collection(Collection $collection): void
    {
        foreach ($collection as $coll) {
            $product = new Product;

            if ($this->mapping) {
                $mapping = json_decode($this->mapping, true);
                foreach ($mapping as $col => $field) {
                    $column = $this->getField($col);
                    $value = $coll[$column];
                    $product->$field = $value;
                }
            }
            $product->save();
        }
    }

    private function getField($col): array|string
    {
        return str_replace(' ', '_', strtolower($col));
    }

    //    /**
    //     * @param array $row
    //     * @return array|Model|Builder|null
    //     */
    //    public function model(array $row): array|Model|Builder|null
    //    {
    //        $user = Auth::user()->companies()
    //            ->newPivotStatement()
    //            ->where('is_default', true)
    //            ->where('user_id', Auth::id())
    //            ->first();
    //
    //        return Product::query()->create(
    //            [
    //                'company_id' => $row['company_id'] ?? $user->company_id,
    //                'component_id' => $row['component_id'] ?? null,
    //                'name' => $row['name'],
    //                'product_code' => $row['product_code'],
    //                'barcode' => $row['barcode'] ?? null,
    //                'has_rfid' => $row['has_rfid'] ?? 0,
    //                'has_batch_number' => $row['has_batch_number'] ?? 0,
    //                'has_serial_number' => $row['has_serial_number'] ?? 0,
    //                'has_component' => $row['has_component'] ?? 0,
    //                'has_variant' => $row['has_variant'] ?? 0,
    //                'variant_option_1' => $row['variant_option_1'] ?? 0,
    //                'variant_option_2' => $row['variant_option_2'] ?? 0,
    //                'variant_option_3' => $row['variant_option_3'] ?? 0,
    //                'variant_value_1' => $row['variant_value_1'] ?? 0,
    //                'variant_value_2' => $row['variant_value_2'] ?? 0,
    //                'variant_value_3' => $row['variant_value_3'] ?? 0,
    //                'description' => $row['description'] ?? null,
    //                'cost_price' => $row['cost_price'] ?? null,
    //                'sales_price' => $row['sales_price'] ?? null,
    //                'has_price_list' => $row['has_price_list'] ?? 0,
    //                'weight' => $row['weight'] ?? null,
    //                'CBM' => $row['CBM'] ?? null,
    //                'width' => $row['width'] ?? null,
    //                'height' => $row['height'] ?? null,
    //                'length' => $row['length'] ?? null,
    //                'has_package_unit' => $row['has_package_unit'] ?? 0,
    //                'sku' => $row['sku'] ?? null,
    //                'category_id' => $row['category_id'] ?? null,
    //                'unit_id' => $row['unit_id'] ?? null,
    //                'supplier_id' => $row['supplier_id'] ?? null,
    //                'tax_id' => $row['tax_id'] ?? null,
    //                'collection_id' => $row['collection_id'] ?? null,
    //                'template_id' => $row['template_id'] ?? null
    //            ]
    //        );
    //    }
}
