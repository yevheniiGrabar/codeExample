<?php

namespace App\Services;

use App\Models\InventoryStockMovement;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\SubLocation;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class StockTransferService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getTransferDataFromRequest(Request $request): array
    {
        $product_id = $request->get('product');
        $locationFrom = $request->get('location_from');
        $locationTo = $request->get('location_to');
        $to_location_id = $locationTo['store'];
        $from_location_id = $locationFrom['store'];
        $number = $request->get('number');
        $remarks = $request->get('remarks');
        $request['date'] = Carbon::now()->format('Y-m-d');
        $transferQuantity = $request->get('quantity');

        $old_location = Location::query()->find($from_location_id);
        $new_location = Location::query()->find($to_location_id);


        return [
            'product_id' => $product_id,
            'location_from' => $locationFrom,
            'location_to' => $locationTo,
            'to_location_id' => $to_location_id,
            'from_location_id' => $from_location_id,
            'number' => $number,
            'remarks' => $remarks,
            'user_id' => Auth::id(),
            'date' => $request['date'],
            'transfer_quantity' => $transferQuantity,
            'old_location' => $old_location,
            'new_location' => $new_location
        ];
    }

    /**
     * @param $locationFrom
     * @return Model|Collection|Builder|array|null
     */
    public function getOldSubLocation($locationFrom): Model|Collection|Builder|array|null
    {
        if (array_key_exists('section', $locationFrom)) {
            $from_section_id = $locationFrom['section'];
            return SubLocation::query()->find($from_section_id);
        }
        return null;
    }

    /**
     * @param $locationTo
     * @return Model|Collection|Builder|array|null
     */
    public function getNewSubLocation($locationTo): Model|Collection|Builder|array|null
    {
        if (array_key_exists('section', $locationTo)) {
            $to_section_id = $locationTo['section'];
            return SubLocation::query()->find($to_section_id);
        }
        return null;
    }

    /**
     * @param $product_id
     * @param $from_location_id
     * @return Builder[]|Collection
     */
    private function getOldLocationProducts($product_id, $from_location_id): Collection|array
    {
        return LocationProduct::query()
            ->where('location_id', $from_location_id)
            ->where('product_id', $product_id)
            ->get();
    }

    /**
     * @param $to_location_id
     * @return Collection|array
     */
    private function getNewLocationProducts($to_location_id): Collection|array
    {
        return LocationProduct::query()->where('location_id', $to_location_id)->get();
    }


    /**
     * @param $oldLocationProducts
     * @param $to_location_id
     * @param $product_id
     * @param $transferQuantity
     * @return array|false
     * @noinspection PhpUnusedLocalVariableInspection
     * @noinspection PhpUndefinedVariableInspection
     */
    public function updateLocationProducts(
        $oldLocationProducts,
        $to_location_id,
        $product_id,
        $transferQuantity
    ): bool|array
    {
        try {
            foreach ($oldLocationProducts as $oldLocationProduct) {
                $newLocationProduct = LocationProduct::query()->where('location_id', $to_location_id)->first();
                $quantityFromOldLocation = $oldLocationProduct->in_stock;

                if (!$newLocationProduct) {
                    $newLocationProduct = new LocationProduct(
                        [
                            'location_id' => $to_location_id,
                            'product_id' => $product_id,
                            'in_stock' => $transferQuantity
                        ]
                    );
                }
                // Calculate the new in_stock quantity for both the old and new location products
                $oldLocationProductNewQuantity = max($oldLocationProduct->in_stock - $transferQuantity, 0);
                $newLocationProductNewQuantity = $newLocationProduct->in_stock + $transferQuantity;

                // Update the old and new location product records
                $oldLocationProduct->in_stock = $oldLocationProductNewQuantity;
                $oldLocationProduct->save();
                $newLocationProduct->in_stock = $newLocationProductNewQuantity;
                $newLocationProduct->save();
            }
            return [
                'old_location_product_quantity' => $quantityFromOldLocation,
                'new_location_product_quantity' => $newLocationProductNewQuantity,
            ];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $to_location_id
     * @param $product_id
     * @param $transferQuantity
     * @return LocationProduct
     */
    private function createNewLocationProduct($to_location_id, $product_id, $transferQuantity): LocationProduct
    {
        return new LocationProduct(
            [
                'location_id' => $to_location_id,
                'product_id' => $product_id,
                'in_stock' => $transferQuantity
            ]
        );
    }


    /**
     * @param Request $request
     * @return InventoryStockMovement|JsonResponse
     * @noinspection PhpUndefinedVariableInspection
     * @noinspection PhpUnusedLocalVariableInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function prepareDataToSaveNewInventoryRecord(Request $request): InventoryStockMovement|JsonResponse
    {


        $transferData = $this->getTransferDataFromRequest($request);

        if ($transferData['old_location']->has_sub_location != 0) {
            $oldSubLocation = $this->getOldSubLocation($transferData['location_from']);

            $newSubLocation = $this->getNewSubLocation($transferData['location_to']);

            if ($oldSubLocation->quantity < $transferData['transfer_quantity']) {
                return new JsonResponse(['message' => 'Not enough products to move to a new location or section']);
            }


            $oldSubLocation->decrement('quantity', $transferData['transfer_quantity']);
            $newSubLocation->increment('quantity', $transferData['transfer_quantity']);

            $quantityFromOldSection = $oldSubLocation->getQuantity();
            $newQuantityInNewSection = $newSubLocation->getQuantity();

            $inventoryStockMovements = $this->createNewRecord(
                $quantityFromOldSection,
                $newQuantityInNewSection,
                $transferData['product_id'],
                $transferData['from_location_id'],
                $oldSubLocation->id,
                $transferData['to_location_id'],
                $newSubLocation->id,
                $transferData['remarks'],
                $transferData['transfer_quantity'],
                $transferData['date'],
            );

            $saveNewRecordInPivotTable = $this->createNewLocationProduct(
                $transferData['to_location_id'],
                $transferData['product_id'],
                $transferData['transfer_quantity']
            );
        } else {
            $oldLocationProducts = $this->getOldLocationProducts(
                $transferData['product_id'],
                $transferData['from_location_id']
            );

            $getQuanties = $this->updateLocationProducts(
                $oldLocationProducts,
                $transferData['to_location_id'],
                $transferData['product_id'],
                $transferData['transfer_quantity']
            );


            $inventoryStockMovements = $this->createNewRecord(
                $getQuanties['old_location_product_quantity'],
                $getQuanties['new_location_product_quantity'],
                $transferData['product_id'],
                $transferData['from_location_id'],
                null,
                $transferData['to_location_id'],
                null,
                $transferData['remarks'],
                $transferData['transfer_quantity'],
                $transferData['date'],
            );

            $saveNewRecordInPivotTable = $this->createNewLocationProduct(
                $transferData['to_location_id'],
                $transferData['product_id'],
                $transferData['transfer_quantity']
            );
        }
        return $inventoryStockMovements;
    }

    /**
     * @param $id
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection PhpUnusedLocalVariableInspection
     */
    public function deleteInventoryStockTransferRecord($id): JsonResponse
    {
        //@todo delete not working when product located in location without sections
        $inventoryStockMovement = InventoryStockMovement::query()->find($id);

        $product_id = $inventoryStockMovement->product_id;
        $locationProduct = LocationProduct::query()
            ->where('location_id', $inventoryStockMovement->location_id)
            ->where('product_id', $inventoryStockMovement->product_id)
            ->get();
        //old location & section
        $from_section_id = $inventoryStockMovement->from_section_id;
        $from_location_id = $inventoryStockMovement->from_location_id;

        //new location & section
        $to_section_id = $inventoryStockMovement->to_section_id;
        $to_location_id = $inventoryStockMovement->to_location_id;

        //quantity
        $transferQuantity = $inventoryStockMovement->quantity; //100

        $currentSubLocation = SubLocation::query()->find($to_section_id);
        $oldSubLocation = SubLocation::query()->find($from_section_id);

        $currentSubLocation->decrement('quantity', $transferQuantity);
        $oldSubLocation->increment('quantity', $transferQuantity);

        $inventoryStockMovement->delete();

        return new JsonResponse(['message' => 'Deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $oldQuantity
     * @param $newQuantity
     * @param $product_id
     * @param $from_location
     * @param $from_section
     * @param $to_location
     * @param $to_section
     * @param $remarks
     * @param $transferQuantity
     * @param $date
     * @param null $number
     * @return InventoryStockMovement
     */
    public function createNewRecord(
        $oldQuantity,
        $newQuantity,
        $product_id,
        $from_location,
        $from_section,
        $to_location,
        $to_section,
        $remarks,
        $transferQuantity,
        $date,
        $number = null
    ): InventoryStockMovement
    {

        $currentCompany = CurrentCompany::getDefaultCompany();

        $inventoryStockMovements = new InventoryStockMovement();
        $inventoryStockMovements->number = $number;
        $inventoryStockMovements->old_quantity = $oldQuantity;
        $inventoryStockMovements->new_quantity = $newQuantity;
        $inventoryStockMovements->product_id = $product_id;
        $inventoryStockMovements->from_location_id = $from_location;
        $inventoryStockMovements->from_section_id = $from_section ?? null;
        $inventoryStockMovements->to_location_id = $to_location;
        $inventoryStockMovements->to_section_id = $to_section ?? null;
        $inventoryStockMovements->user_id = Auth::id();
        $inventoryStockMovements->remarks = $remarks;
        $inventoryStockMovements->quantity = $transferQuantity;
        $inventoryStockMovements->date = $date;
        $inventoryStockMovements->company_id = $currentCompany->company_id;
        $inventoryStockMovements->save();

        return $inventoryStockMovements;
    }

    public function updateRecord(
        $inventoryStockMovement,
        $number,
        $productId,
        $oldQuantity,
        $newQuantity,
        $fromLocationId,
        $fromSectionId,
        $toLocationId,
        $toSectionId,
        $remarks,
        $quantity,
        $date
    )
    {

        $currentCompany = CurrentCompany::getDefaultCompany();

        $inventoryStockMovement->update(
            [
                'number' => $number ?? null,
                'product_id' => $productId,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'from_location_id' => $fromLocationId,
                'from_section_id' => $fromSectionId,
                'to_location_id' => $toLocationId,
                'to_section_id' => $toSectionId,
                'user_id' => Auth::id(),
                'remarks' => $remarks ?? null,
                'quantity' => $quantity,
                'date' => $date,
                'company_id' => $currentCompany->company_id,
            ]
        );

        return $inventoryStockMovement;
    }

    public function exportTransfers(array $filters): Collection
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();
        $query = InventoryStockMovement::query();

        if (isset($filters)) {
            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where('remarks', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('locationFrom', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('locationTo', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('sectionFrom', fn($q) => $q->where('section_name', 'like', '%' . $search . '%'))
                    ->orWhereHas('sectionTo', fn($q) => $q->where('section_name', 'like', '%' . $search . '%'))
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            }

            if (isset($filters['products']) && is_array($filters['products'])) {
                $query->whereIn('product_id', $filters['products']);
            }

            if (isset($filters['users']) && is_array($filters['users'])) {
                $query->whereIn('user_id', $filters['users']);
            }

            if (isset($filters['location_from'])) {
                $query->where('from_location_id', $filters['location_from']['store']);

                if (isset($filters['location_from']['section'])) {
                    $query->where('from_section_id', $filters['location_from']['section']);
                }
            }

            if (isset($filters['location_to'])) {
                $query->where('to_location_id', $filters['location_to']['store']);

                if (isset($filters['location_to']['section'])) {
                    $query->where('to_section_id', $filters['location_to']['section']);
                }
            }

            if (isset($filters['remarks'])) {
                if ($filters['remarks'] == 1) {
                    $query->whereNotNull('remarks');
                } elseif ($filters['remarks'] == 0) {
                    $query->where(function ($query) {
                        $query->where('remarks', '=', '')
                            ->orWhereNull('remarks');
                    });
                }
            }
            if (isset($filters['date'])) {
                if (isset($filters['date']['from'])) {
                    $query->where('date', '>=', $filters['date']['from']);
                }

                if (isset($filters['date']['to'])) {
                    $query->where('date', '<=', $filters['date']['to']);
                }
            }

        }

        $query->with('product', 'user', 'locationFrom', 'sectionFrom', 'locationTo', 'sectionTo')
            ->where('company_id', $defaultCompany->company_id)
            ->orderBy('id', 'desc');

        return $query->get();
    }
}
