<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\PackingDimension;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductPriceHistory;
use App\Models\ProductPriceList;
use App\Models\ProductTranslation;
use App\Models\PurchaseOrder;
use App\Models\SaleOrder;
use App\Models\SubLocation;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ProductService
{

    private ConnectionInterface $connection;

    /**
     * @param array $data
     * @return mixed
     * @throws ValidationException
     * @noinspection PhpUnused
     */

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function createProduct(array $data): ?Product
    {
        try {
            $this->connection->transaction(
                function () use ($data, &$product) {
                    $product = new Product;

                    $product->name = $data['name'];
                    $product->product_code = $data['code'];
                    $product->barcode = $data['barcode'] ?? null;
                    $product->unit_id = $data['unit'] ?? null;
                    $product->category_id = $data['category'] ?? null;
                    $product->supplier_id = $data['supplier'] ?? null;
                    $product->tax_id = $data['tax'] ?? null;
                    $product->has_rfid = $data['is_RFID'] == 'true' ? 1 : 0;
                    $product->has_batch_number = $data['is_batch_number'] == 'true' ? 1 : 0;
                    $product->has_serial_number = $data['is_serial_number'] == 'true' ? 1 : 0;
                    $product->is_component = $data['is_components'] == 'true' ? 1 : 0;
                    $product->weight = $data['weights_and_sizes']['weight'] ?? null;
                    $product->CBM = $data['weights_and_sizes']['CBM'] ?? null;
                    $product->width = $data['weights_and_sizes']['width'] ?? null;
                    $product->height = $data['weights_and_sizes']['height'] ?? null;
                    $product->length = $data['weights_and_sizes']['length'] ?? null;
                    $product->currency_id = $data['prices']['currency'];
                    $product->cost_price = $data['prices']['purchase_price'];
                    $product->selling_price = $data['prices']['selling_price'];
                    //        $product->template_id =  $data['template'];

                    if (isset($data['description'])) {
                        $product->description = $data['description'];
                    } else {
                        $product->description = null;
                    }

                    $product->save();


                    if (isset($data['location']['store'])) {
                        $this->saveAdditionalData(
                            $data['location']['store'],
                            $product,
                            $data['inventory']['in_stock'] ?? 0 ,
                            $data['inventory']['min_inventory_quantity'] ?? 0,
                            $data['inventory']['min_purchase_quantity'] ?? 0,
                            $data['inventory']['min_sale_quantity'] ?? 0,
                            $data['location']['section'] ?? null
                        );
                    }

                    if (isset($data['image'])) {
                        $this->saveProductImage($data['image'], $product);
                    }

                    if (isset($data['serial_numbers'])) {
                        $product->serialNumbers()->createMany($data['serial_numbers']);
                    }

                    if (isset($data['batch_numbers'])) {
                        $product->batchNumbers()->createMany($data['batch_numbers']);
                    }
                }
            );

            return $product;
        } catch (Throwable $e) {
            throw $e;
            return null;
        }
    }

    /**
     * @param $store_id
     * @param $product
     * @param $in_stock
     * @param $min_inventory_quantity
     * @param $min_purchase_quantity
     * @param $min_sale_quantity
     * @param null $section_id
     * @throws ValidationException
     */
    public function saveAdditionalData(
        $store_id,
        $product,
        $in_stock,
        $min_inventory_quantity,
        $min_purchase_quantity,
        $min_sale_quantity,
        $section_id = null,
    ) {
        $location = Location::query()->findOrFail($store_id);
        if ($location) {
            $subLocationId = null;
            if ($section_id) {
                $subLocation = SubLocation::query()->findOrFail($section_id);
                if ($subLocation) {
                    if ($subLocation->location_id !== $location->id) {
                        throw ValidationException::withMessages(
                            ['The selected section does not belong to the selected store.']
                        );
                    }
                    $subLocationId = $subLocation->id;
                }
            }


            $product->locations()->attach(
                $location->id,
                [
                    'company_id' => CurrentCompany::getDefaultCompany()->company_id,
                    'in_stock' => $in_stock,
                    'min_inventory_quantity' => $min_inventory_quantity,
                    'min_purchase_quantity' => $min_purchase_quantity,
                    'min_sale_quantity' => $min_sale_quantity,
                    'created_at' => Carbon::now(),
                ]
            ); //['sub_location_id' => $subLocationId]
        }
    }

    /**
     * @param $images
     * @param $product
     * @return Product
     */
    public function saveProductImage($image, $product): Product
    {
        $product->clearMediaCollection('images');

        $product->addMedia($image)
            ->withCustomProperties(['is_default' => 1])
            ->toMediaCollection('images');

        return $product;
    }

    /**
     * @param $product
     * @param array $data
     * @return mixed
     * @noinspection PhpUnused
     */
    public function updateProduct($product, array $data): Product|Exception
    {
        $product->update(
            [
                'name' => $data['name'] ?? $product->name,
                'product_code' => $data['code'] ?? $product->product_code,
                'barcode' => $data['barcode'] ?? $product->barcode,
                'unit_id' => $data['unit'] ?? $product->unit_id,
                'category_id' => $data['category'] ?? $product->category_id,
                'supplier_id' => $data['supplier'] ?? $product->supplier_id,
                'tax_id' => $data['tax'] ?? $product->tax_id,
                'has_rfid' => $data['is_RFID'] == 'true' ? 1 : 0,
                'has_batch_number' => $data['is_batch_number'] == 'true' ? 1 : 0,
                'has_serial_number' => $data['is_serial_number'] == 'true' ? 1 : 0,
                'is_component' => $data['is_components'] == 'true' ? 1 : 0,
                'description' => $data['description'] ?? $product->description,
                'weight' => $data['weights_and_sizes']['weight'] ?? $product->weight,
                'CBM' => $data['weights_and_sizes']['CBM'] ?? $product->CBM,
                'width' => $data['weights_and_sizes']['width'] ?? $product->width,
                'height' => $data['weights_and_sizes']['height'] ?? $product->height,
                'length' => $data['weights_and_sizes']['length'] ?? $product->length,
            ]
        );

        if (isset($data['prices'])) {
            $this->updateProductPrices($data['prices'], $product);
        }

        // if (isset($data['images'])) {
        //     foreach ($data['images'] as $image) {
        //         $product->addMedia($image['file'])
        //             ->withCustomProperties(['is_default' => $image['is_default'] ?? 0])
        //             ->toMediaCollection('images');
        //     }
        // }

        if (isset($data['image'])) {
            $this->saveProductImage($data['image'], $product);
        }

        if (isset($data['weights_and_sizes']['dimensions']) && is_array($data['weights_and_sizes']['dimensions'])) {
            $this->addPackingDimension($data['weights_and_sizes']['dimensions'], $product);
        }

        return $product;
    }

    public function updateProductPrices(array $prices, Product $product): void
    {
        $product->currency_id = $prices['currency'];
        $product->cost_price = $prices['purchase_price'];
        $product->selling_price = $prices['selling_price'];
        $product->save();
    }

    /**
     * @param Request $request
     * @param $product
     * @return Product|Builder|Builder[]|Collection|Model|null
     */
    public function saveManyComponents(Request $request, $product): Model|Collection|Product|Builder|array|null
    {
        $components = '';
        if ($request->has('component_ids')) {
            $componentIds = explode(',', $request->get('component_ids'));

            if (!empty($componentIds) && sizeof($componentIds) > 0) {
                foreach ($componentIds as $componentId) {
                    $product = Product::query()->find($componentId);
                    $product->component_id = $product->id;
                    $product->save();
                }
            }
        }
        return $product;
    }

    public function getCurrentCompany(): mixed
    {
        return Auth::user()->companies()->newPivotStatement()
            ->where('is_default', true)
            ->where('user_id', Auth::id())->first();
    }

    /**
     * @param $product
     * @return mixed
     */
    public function associateProductWithDefaultCompany($product): mixed
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $product->company_id = $currentCompany->company_id;
        $product->save();

        return $product;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSubLocationsTotalQuantityByProductId(int $id): mixed
    {
        $product = Product::query()->find($id);
        $locations = Location::query()->with('sections')->where('id', $product->id)->get();
        $locations = $locations->toArray();

        $quantityBySubLocation = '';

        foreach ($locations as $location) {
            if ($location['has_sub_location'] != true) {
                $defaultQuantity = $product->in_stock;
            }

            $quantityBySubLocation = SubLocation::query()->where('location_id', $location['id'])
                ->groupBy('location_id')
                ->sum('quantity');
        }

        return $quantityBySubLocation;
    }

    /**
     * @param Request $request
     * @param $product
     * @return Product
     */
    public function addImage(Request $request, $product): Product
    {
        if ($request->hasFile('image')) {
            $product->addMultipleMediaFromRequest(['image'])
                ->each(
                    function ($fileAdder) {
                        $fileAdder->toMediaCollection('images');
                    }
                );
        }

        return $product;
    }

    /**
     * @param Media $image
     * @return void
     */
    public function deleteImage(Media $image): void
    {
        $image->delete();
        Storage::delete($image->getPath());
    }

    //    /**
    //     * @param Request $request
    //     * @param $product
    //     * @return ProductPriceList
    //     */
    //    public function updatePriceList (Request $request, $product): ProductPriceList
    //    {
    //        $productPriceListId = $request->input('id');
    //        $selling_price = $request->input('selling_price');
    //        $priceList = $request->input('price_list_id');
    //
    //        $productPriceList = $product->priceList()->where('id',$productPriceListId)->firstOrFail();
    //
    //        $productPriceList->update([
    //            'selling_price' => $selling_price,
    //            'price_list_id' => $priceList
    //        ]);
    //
    //        return $productPriceList;
    //    }

    /**
     * @param Request $request
     * @param $product
     * @return Product
     */
    public function updateImage(Request $request, $product): Product
    {
        if ($request->hasFile('image')) {
            $product->syncMediaFromRequest(['image'])->toMediaCollection('images');
        }

        return $product;
    }

    /**
     * @param array $prices
     * @param Product $product
     * @return void
     */
    public function addPriceList(array $prices, Product $product): void
    {
        foreach ($prices as $price) {
            $productPriceList = new ProductPriceList;
            $productPriceList->product_id = $product->id;
            $productPriceList->price_list_id = $price['price_list'];
            $productPriceList->selling_price = $price['selling_price'];
            $productPriceList->save();
        }

        $product->has_price_list = true;
        $product->save();
    }

    /**
     * @param Product $product
     * @return Collection
     */
    public function productPurchaseOrders(Product $product): Collection
    {
        $purchaseOrderIds = $product->orderLine->pluck('purchase_order_id')->unique();
        $purchaseOrders = PurchaseOrder::query()->whereIn('id', $purchaseOrderIds)->get();

        return $purchaseOrders;
    }

    public function productSaleOrders(Product $product)
    {
        $saleOrderIds = $product->saleOrderLine->pluck('sale_order_id')->unique();
        $saleOrders = SaleOrder::query()->whereIn('id', $saleOrderIds)->get();

        return $saleOrders;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model|Collection|Builder|array|null
     */
    public function updateInventoryData(int $id, array $data): Model|Collection|Builder|array|null
    {
        $defaultCompany = CurrentCompany::getDefaultCompany();

        $product = Product::query()->where('company_id', $defaultCompany->company_id)->find($id);
        $locations = $data['locations'];

        foreach ($locations as $location) {
            //            if (in_array('section', $location)) {
            $locationsProduct = LocationProduct::query()
                ->where('location_id', '=', $location['store'])
                ->where('product_id', '=', $product->id)
                ->where('company_id', $defaultCompany->company_id)
                ->where('sub_location_id', $location['section'] ?? null)
                ->first();

            $locationProduct->update(
                [
                    'min_inventory_quantity' => $location['inventory']['min_inventory_quantity'],
                    'min_purchase_quantity' => $location['inventory']['min_purchase_quantity'],
                ]
            );
        }

        return $product;
    }

    public function productParseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
            $categories = $parsedData->categories ?? [];
            $price_range = $parsedData->purchase_price_range ?? [];
            $quantity_range = $parsedData->quantity_range ?? [];
            $components = $parsedData->components ?? '';
        }

        return [
            'search' => $search ?? '',
            'categories' => $categories ?? [],
            'purchase_price_range' => $price_range ?? [],
            'quantity_range' => $quantity_range ?? [],
            'components' => $components ?? ''
        ];
    }

    /**
     * @param Request $request
     * @return void
     */
    public function productImport(Request $request): void
    {
        $file = $request->file('file');
        // Import the data from the Excel file
        $data = Excel::toArray([], $file);
        // Get the headers for the first row of data
        $headers = $data[0][0];
        // Get the mapping from the form
        $mapping = $request->input('mapping');

        // Loop through the data and insert into database
        foreach ($data[0] as $row) {
            $product = new Product;

            // Map the data to the database columns using the user-defined mapping
            foreach ($mapping as $key => $value) {
                $product->{$value} = $row[$key];
            }

            // Save the product to the database
            $product->save();
        }
    }

    public function createProductTranslation(array $data): ProductTranslation
    {
        $productTranslation = new ProductTranslation();

        $productTranslation->product_id = $data['product_id'];
        $productTranslation->language_id = $data['language_id'];
        $productTranslation->translated_name = $data['translated_name'];
        $productTranslation->translated_description = $data['translated_description'];

        $productTranslation->save();

        return $productTranslation;
    }

    public function updateProductTranslation(ProductTranslation $productTranslation, array $data): ProductTranslation
    {
        $productTranslation->update([
            'product_id' => $data['product_id'] ?? $productTranslation->product_id,
            'language_id' => $data['language_id'] ?? $productTranslation->language_id,
            'translated_name' => $data['translated_name'] ?? $productTranslation->translated_name,
            'translated_description' => $data['translated_description'] ?? $productTranslation->translated_description
        ]);

        $productTranslation->save();

        return $productTranslation;
    }

    public function getProductPrices(Product $product)
    {
        $currency = $product->currency_id;
        $prices = $product->priceHistory->where('currency_id', $currency);

        return $prices;
    }
}
