<?php

namespace App\Services;

use App\Models\InventoryAdjustment;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\SubLocation;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentService
{
    /**
     * @param Request $request
     * @return InventoryAdjustment
     */
    public function saveNewRecord(Request $request): InventoryAdjustment
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $product_id = $request->get('product_id');
        $data = $request->get('location');
        $location_id = $data['store_id'];
        $sub_location_id = $data['section_id'] ?? null;
        $adjustmentType = $request->get('adjustment_type');

        if (!empty($sub_location_id)) {
            $adjustment = $this->saveSubLocationAdjustment(
                $sub_location_id,
                $location_id,
                $product_id,
                $adjustmentType,
                $request,
                $currentCompany
            );
        } else {
            $adjustment = $this->saveLocationAdjustment(
                $location_id,
                $product_id,
                $adjustmentType,
                $request,
                $currentCompany
            );
        }

        return $adjustment;
    }

    /**
     * @param $sub_location_id
     * @param $location_id
     * @param $product_id
     * @param $adjustmentType
     * @param $request
     * @param $currentCompany
     * @return InventoryAdjustment
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function saveSubLocationAdjustment(
        $sub_location_id,
        $location_id,
        $product_id,
        $adjustmentType,
        $request,
        $currentCompany
    ): InventoryAdjustment {
        $subLocation = SubLocation::query()->find($sub_location_id);
        $oldQuantity = $subLocation->quantity;
        $newQuantity = $request->get('changed_value');

        $adjustment = $this->createAdjustment($adjustmentType, $request, $currentCompany);
        if ($adjustmentType != 1) {
            $adjustment->actual_quantity = $newQuantity;
            $subLocation->update(['quantity' => $newQuantity]);
        }

        $this->updateProductCostPrice($product_id, $adjustmentType, $request);

        $this->setAdjustmentData($adjustment, $request, $product_id, $location_id, $sub_location_id, $oldQuantity);

        $adjustment->save();

        return $adjustment;
    }

    /**
     * @param $location_id
     * @param $product_id
     * @param $adjustmentType
     * @param $request
     * @param $currentCompany
     * @return InventoryAdjustment
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function saveLocationAdjustment(
        $location_id,
        $product_id,
        $adjustmentType,
        $request,
        $currentCompany
    ): InventoryAdjustment {
        $location = LocationProduct::query()
            ->where('company_id', $currentCompany->company_id)
            ->where('product_id', $product_id)
            ->where('location_id', $location_id)
            ->where('sub_location_id', null)
            ->first();
        $oldQuantity = $location->in_stock;
        $newQuantity = $request->get('changed_value');

        $adjustment = $this->createAdjustment($adjustmentType, $request, $currentCompany);
        if ($adjustmentType != 1) {
            $adjustment->actual_quantity = $newQuantity;
            $location->update(['total_quantity' => $newQuantity]);
        }

        $this->updateProductCostPrice($product_id, $adjustmentType, $request);

        $this->setAdjustmentData($adjustment, $request, $product_id, $location_id, null, $oldQuantity);

        $adjustment->save();

        return $adjustment;
    }

    /**
     * @param $adjustmentType
     * @param $request
     * @param $currentCompany
     * @return InventoryAdjustment
     */
    private function createAdjustment($adjustmentType, $request, $currentCompany): InventoryAdjustment
    {
        $adjustment = new InventoryAdjustment();
        $adjustment->adjustment_type = $adjustmentType;
        $adjustment->date = $request->date;
        $adjustment->user_id = Auth::id();
        $adjustment->remarks = $request->get('remarks');
        $adjustment->company_id = $currentCompany->company_id;

        return $adjustment;
    }

    /**
     * @param $product_id
     * @param $adjustmentType
     * @param $request
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function updateProductCostPrice($product_id, $adjustmentType, $request)
    {
        $product = Product::query()->find($product_id);
        $oldCostPrice = $product->cost_price;
        $newCostPrice = $request->get('changed_value');

        if ($adjustmentType != 0) {
            $product->update(['cost_price' => $newCostPrice]);
        }
    }

    /**
     * @param $adjustment
     * @param $request
     * @param $product_id
     * @param $location_id
     * @param $sub_location_id
     * @param $oldQuantity
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function setAdjustmentData($adjustment, $request, $product_id, $location_id, $sub_location_id, $oldQuantity)
    {
        $request['date'] = Carbon::now()->format('Y-m-d');
        $product = Product::query()->find($product_id);
        $adjustment->old_quantity = $oldQuantity;
        $adjustment->old_cost_price = $product->cost_price;
        $adjustment->product_id = $product_id;
        $adjustment->location_id = $location_id;
        $adjustment->sub_location_id = $sub_location_id ?? null;
        $adjustment->date = $request->date;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getLocationData(Request $request): array
    {
        $location = [];
        if ($request->has('location')) {
            $location = $request->get('location');
        }
        return $location;
    }

    /**
     * @param Request $request
     * @param InventoryAdjustment $adjustment
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function updateLocationAdjustment(Request $request, InventoryAdjustment $adjustment): void
    {
        $location = $this->getLocationData($request);
        $locationId = $location['store_id'];
        $remarks = $request->get('remarks');
        $adjustmentType = $request->get('adjustment_type');

        $locationModel = Location::query()->find($locationId);
        $oldQuantity = $locationModel->total_quantity;
        $newQuantity = $request->get('changed_value');

        if ($locationModel->has_sub_location == 0) {
            $locationModel->update(['total_quantity' => $newQuantity]);
        }

        $adjustment->update(
            [
                'product_id' => $request->get('product_id'),
                'location_id' => $locationId,
                'remarks' => $remarks,
                'old_quantity' => $oldQuantity,
                'actual_quantity' => $newQuantity,
                'adjustment_type' => $adjustmentType,
                'user_id' => Auth::user()->id,
            ]
        );
    }

    /**
     * @param Request $request
     * @param InventoryAdjustment $adjustment
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function updateSubLocationAdjustment(Request $request, InventoryAdjustment $adjustment): void
    {
        $location = $this->getLocationData($request);
        $subLocationId = $location['section_id'];
        $remarks = $request->get('remarks');
        $adjustmentType = $request->get('adjustment_type');

        $subLocation = SubLocation::query()->find($subLocationId);
        $oldQuantity = $subLocation->quantity;
        $newQuantity = $request->get('changed_value');

        $subLocation->update(['quantity' => $newQuantity]);

        $adjustment->update(
            [
                'product_id' => $request->get('product_id'),
                'location_id' => $location['store_id'],
                'sub_location_id' => $subLocationId,
                'remarks' => $remarks,
                'old_quantity' => $oldQuantity,
                'actual_quantity' => $newQuantity,
                'adjustment_type' => $adjustmentType,
                'user_id' => Auth::user()->id,
            ]
        );
    }

    /**
     * @param Request $request
     * @param InventoryAdjustment $adjustment
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function updateCostPriceAdjustment(Request $request, InventoryAdjustment $adjustment): void
    {
        $product = Product::query()->find($request->get('product_id'));
        $newCostPrice = $request->get('changed_value');
        $location = $this->getLocationData($request);
        $product->update(['cost_price' => $newCostPrice]);

        $adjustment->update(
            [
                'product_id' => $request->get('product_id'),
                'location_id' => $location['store_id'],
                'remarks' => $request->get('remarks'),
                'old_cost_price' => $product->cost_price,
                'actual_cost_price' => $newCostPrice,
                'adjustment_type' => $request->get('adjustment_type'),
                'user_id' => Auth::user()->id,
            ]
        );
    }

    /**
     * @param Request $request
     * @param InventoryAdjustment $adjustment
     * @return InventoryAdjustment
     */
    public function updateExistingRecord(Request $request, InventoryAdjustment $adjustment): InventoryAdjustment
    {
        $adjustmentType = $request->get('adjustment_type');

        if ($adjustmentType != 1) {
            if (isset($location['section_id'])) {
                $this->updateSubLocationAdjustment($request, $adjustment);
            } else {
                $this->updateLocationAdjustment($request, $adjustment);
            }
        } else {
            $this->updateCostPriceAdjustment($request, $adjustment);
        }

        return $adjustment;
    }


    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    public function deleteAdjustment(int $id): JsonResponse
    {
        $adjustment = InventoryAdjustment::query()->find($id);
        $product = Product::query()->find($adjustment->product_id);
        $adjustmentType = $adjustment->adjustment_type;
        $location = Location::query()->find($adjustment->location_id);
        $subLocation = SubLocation::query()->find($adjustment->sub_location_id)->where(
            'location_id',
            $location->id
        )->first();
        $quantityOld = $adjustment->old_quantity;
        $costPriceOld = $adjustment->old_cost_price;

        if ($adjustmentType != 1) {
            $subLocation->update(['quantity' => $quantityOld]);
        }

        //condition by price
        if ($adjustmentType != 0) {
            $product->update(['cost_price' => $costPriceOld]);
        }

        $adjustment->delete();

        return new JsonResponse(['message' => 'Adjustment deleted successfully']);
    }
}
