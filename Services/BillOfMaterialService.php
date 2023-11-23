<?php


namespace App\Services;


use App\Models\BillOfMaterial;
use App\Models\Component;
use App\Models\Product;
use App\Models\ReceiveHistory;
use App\Traits\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BillOfMaterialService
{

    private function getReceivedQuantity($productId)
    {
        return ReceiveHistory::where('product_id', $productId)->count();
    }

    /**
     * @param $request
     * @return Collection|array
     */
    public function loadBomRecords($request): Collection|array
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $bomList = BillOfMaterial::query()
            ->with(
                [
                    'product' => function ($q) {
                        $q->select(['id', 'name', 'category_id', 'tax_id', 'cost_price as product_unit_price']);
                    },
                    'product.category' => function ($q) {
                        $q->select(['id', 'name']);
                    },
                    'product.tax' => function ($q) {
                        $q->select(['id', 'name']);
                    },
                    'components' => function ($q) {
                        $q->with(
                            [
                                'product' => function ($q) {
                                    $q->select(['id', 'name', 'cost_price as unit_price']);
                                },
                            ]
                        );
                    },
                ]
            )
            ->select(['id', 'name', 'product_id'])
//            ->addSelect('components.product_id')
            ->where('company_id', $currentCompany->company_id)
            ->orderBy('id', 'desc')
            ->get();

        $bomList->each(
            function ($bom) {
                if ($bom->product_id) {
                    $receivedQuantity = ReceiveHistory::where('product_id', $bom->product_id)
                        ->count();
                    $bom->in_stock = $receivedQuantity;
                }

                $bom->components->each(
                    function ($component) {
                        $componentReceivedQuantity = ReceiveHistory::where('product_id', $component->product_id)
                            ->count();
                        $component->in_stock = $componentReceivedQuantity;
                    }
                );
            }
        );
//        dd($bomList->toArray());
        return $bomList;
    }


    /**
     * @param $request
     * @return Model|Builder
     */
    public function createBOM($request): Model|Builder
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $bom = BillOfMaterial::query()->create(
            [
                'product_id' => $request->get('parent_product'),
                'company_id' => $currentCompany->company_id,
                'name' => $request->get('name'),
            ]
        );
        if (!empty($request->get('components'))) {
            $components = $request->get('components');
            if (sizeof($components) > 0) {
                foreach ($components as $component) {
                    $product = Product::query()->find($component['product']);
                    Component::query()->create(
                        [
                            'product_id' => $component['product'],
                            'quantity' => $component['quantity'],
                            'bill_of_material_id' => $bom->id,
                            'unit_price' => $product->cost_price,
                        ]
                    );
                }
            }
        }

        return $bom;
    }

    /**
     * @param $bom
     * @return Model|Builder|null
     */
    public function loadAdditionalData($bom): Model|Builder|null
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $bom = BillOfMaterial::query()
            ->with(
                [
                    'product' => function ($q) {
                        $q->select(
                            ['id', 'name', 'category_id', 'tax_id', 'cost_price as product_unit_price', 'product_code']
                        );
                    },
                    'product.category' => function ($q) {
                        $q->select(['id', 'name']);
                    },
                    'product.tax' => function ($q) {
                        $q->select(['id', 'name']);
                    },
                    'components' => function ($q) {
                        $q->with(
                            [
                                'product' => function ($q) {
                                    $q->select(['id', 'name', 'cost_price as unit_price']);
                                },
                            ]
                        );
                    },
                ]
            )
            ->select(['id', 'name', 'product_id'])
            ->where('company_id', $currentCompany->company_id)
            ->orderBy('id', 'desc')
            ->first(); // Заменил get() на first()

        if ($bom->product_id) {
            $receivedQuantity = ReceiveHistory::where('product_id', $bom->product_id)
                ->count();
            $bom->in_stock = $receivedQuantity;
        }

        $bom->components->each(
            function ($component) {
                $componentReceivedQuantity = ReceiveHistory::where('product_id', $component->product_id)
                    ->count();
                $component->in_stock = $componentReceivedQuantity;
            }
        );

        return $bom;
    }

    /**
     * @param $request
     * @param $id
     * @return Model|Collection|Builder|array|null
     */
    public function updateBOM($request, $id): Model|Collection|Builder|array|null
    {
        $bom = BillOfMaterial::query()->find($id);
        $bom->update(['name' => $request->get('name')]);

        $components = $request->get('components');

        foreach ($components as $component) {
            $ids[] = $component['id'];

            $productId = $component['product']['id'];
            $quantity = $component['quantity'];

            $existingComponent = Component::query()->find($component['id']);

            $existingComponent->update(
                [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]
            );


            $bom->components()->save($existingComponent);
        }

        Component::query()->where('bill_of_material_id', $id)->whereNotIn('id', $ids)->delete();

        return $bom;
    }
}
