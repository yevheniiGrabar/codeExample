<?php

namespace App\Services;

use App\Models\LocationProduct;
use App\Models\StockCount;
use App\Models\StockCountProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StockCountService
{

    public function __construct()
    {
    }

    public function getStockCounts(Request $request)
    {
        $parsedRequest = $this->parseRequest($request);

        return StockCount::filter(
            $parsedRequest['search'],
            $parsedRequest['location'],
            $parsedRequest['section'],
            $parsedRequest['date'],
            $parsedRequest['worker'],
        )->with(['user', 'location', 'stockCountProduct'])
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->get();
    }

    public function createStockCounts(array $data): StockCount
    {
        $productsExist = $this->checkProductsExist($data['store_id'], $data['counts']);

        if (!$productsExist) {
            throw (new ModelNotFoundException)->setModel('LocationProduct');
        }

        $stockCount = new StockCount();

        $stockCount->user_id = Auth::id();
        $stockCount->location_id = $data['store_id'];
        $stockCount->date = Carbon::now()->toDateString();

        $stockCount->save();

        if (isset($data['counts'])) {
            foreach ($data['counts'] as $count) {
                $stockCountProduct = new StockCountProduct();

                $stockCountProduct->stock_count_id = $stockCount->id;
                $stockCountProduct->product_id = $count['product_id'];
                $stockCountProduct->sub_location_id = $count['section_id'] ?? null;
                $stockCountProduct->counted_quantity = $count['counted_quantity'];

                $stockCountProduct->save();
            }
        }
        return $stockCount;
    }

    public function updateStockCounts(array $data, StockCount $stockCount): StockCount
    {
        $stockCount->update([
            'status' => $data['status'],
            'declination_comment' => $data['declination_comment'] ?? $stockCount->declination_comment
        ]);

        $stockCount->save();

        return $stockCount;
    }

    public function exportStockCounts(Request $request): Collection
    {
        if ($request->input('is_all') === true) {
            return StockCount::all();
        }
        $selected = $request->input('selected');
        return StockCount::query()->find($selected);
    }

    public function mergeStockCounts(Request $request): Collection
    {
        $reports = $request->input('reports');
        $stockCounts = StockCount::query()->whereIn('id', $reports)->get();

        $groupedStockCounts = $stockCounts->groupBy('location_id');

        $mergedStockCounts = [];

        foreach ($groupedStockCounts as $locationId => $stockCounts) {
            $mergedStockCountProducts = StockCountProduct::query()->whereIn('stock_count_id', $stockCounts->pluck('id'))
                ->groupBy(['sub_location_id', 'product_id'])
                ->selectRaw('sub_location_id, product_id, SUM(counted_quantity) as total_counted_quantity')
                ->get();

            $mergedStockCount = new StockCount([
                'user_id' => Auth::id(),
                'location_id' => $locationId,
                'date' => Carbon::now()->toDateTime(),
            ]);
            $mergedStockCount->save();

            foreach ($mergedStockCountProducts as $stockCountProduct) {
                $mergedStockCount->stockCountProduct()->create([
                    'product_id' => $stockCountProduct['product_id'],
                    'sub_location_id' => $stockCountProduct['sub_location_id'],
                    'counted_quantity' => $stockCountProduct['total_counted_quantity'],
                ]);
            }

            $mergedStockCounts[] = $mergedStockCount;
        }
        return collect($mergedStockCounts);

    }

    public function parseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? null;
            $location = $parsedData->search ?? null;
            $section = $parsedData->search ?? null;
            $date = $parsedData->search ?? null;
            $worker = $parsedData->search ?? null;
        }

        return [
            'search' => $search ?? null,
            'location' => $location ?? null,
            'section' => $section ?? null,
            'date' => $date ?? null,
            'worker' => $worker ?? null,
        ];
    }

    private function checkProductsExist($locationId, $counts): bool
    {
        foreach ($counts as $count) {
            $productId = $count['product_id'];
            $subLocationId = $count['section_id'] ?? null;

            $exists = LocationProduct::query()->where('location_id', $locationId)
                ->where('product_id', $productId)
                ->where('sub_location_id', $subLocationId)
                ->exists();

            if (!$exists) {
                return false;
            }
        }

        return true;
    }
}
