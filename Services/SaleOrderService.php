<?php

namespace App\Services;

use App\Http\Resources\SaleOrderResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SaleOrderLine;
use App\Traits\CurrentCompany;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SaleOrderService
{

    /**
     * Loads additional data for a sales order object.
     *
     * @param $saleOrder
     * @return SaleOrder
     */
    public function loadAdditionalData($saleOrder): SaleOrder
    {
        $saleOrder->loadMissing(
            [
                'customer' => fn($q) => $q->select(
                    ['id', 'customer_name', 'is_person', 'customer_code', 'first_name', 'last_name']
                ),
                'currency' => fn($q) => $q->select(['id', 'name', 'code']),
                'theirReference' => fn($q) => $q->select(['id', 'contact_person as name', 'code']),
                'paymentTerm' => fn($q) => $q->select(['*']),
                'deliveryTerm' => fn($q) => $q->select(['*']),
                'lines' => fn($q) => $q->select(
                    ['id', 'sale_order_id', 'tax_id', 'product_id', 'quantity', 'unit_price', 'discount'] //'tax_id',
                ),
                'lines.product' => fn($q) => $q->select(['id', 'name', 'product_code as code']),
                'lines.tax' => fn($q) => $q->select(['id', 'rate']),
            ]
        );

        return $saleOrder;
    }

    /**
     * Creates a new sales order based on the data provided.
     *
     * @param array $data
     * @return SaleOrder
     */
    public function createSaleOrder(array $data): SaleOrder
    {
        $saleOrder = new SaleOrder();

        $saleOrder->customer_id = $data['customer'];
        $saleOrder->order_date = $data['order_date'];
        $saleOrder->preferred_delivery_date = $data['preferred_delivery_date'] ?? null;
        $saleOrder->our_reference_id = $data['our_reference'] ?? null;
        $saleOrder->their_reference_id = $data['their_reference'] ?? null;
        $saleOrder->payment_term_id = $data['payment_terms'] ?? null;
        $saleOrder->delivery_term_id = $data['delivery_terms'] ?? null;
        $saleOrder->company_id = CurrentCompany::getDefaultCompany()->company_id;
        $saleOrder->currency_id = $data['currency'];
        $saleOrder->billing_address_id = Customer::find($data['customer'])->billingAddress->id;
        $saleOrder->delivery_address_id = $data['delivery_address'];
        $saleOrder->save();


        $customer = Customer::query()->find($data['customer']);
        $customer->update(['has_powerOffice' => 1]);
        $customer->save();

        if (isset($data['orders']) && sizeof($data['orders']) > 0) {
            $this->addOrderLines($data['orders'], $saleOrder);
        }

        return $saleOrder;
    }

    /**
     * Sends sales order data to PowerOffice.
     *
     * @param $saleOrder
     * @return JsonResponse
     * @throws GuzzleException
     * @noinspection PhpUndefinedMethodInspection
     */
    public function sendDataToPowerOffice($saleOrder): JsonResponse
    {
        $id = Auth::id();
        $url = "http://localhost:5000/place_order/{$id}";
        $httpClient = new Client();

        try {
            // Send a request to the specified URL using Guzzle HTTP client
            $response = $httpClient->post(
                $url,
                [
                    // You can include any data you want to send in the request here
                    SaleOrderResource::setMode('details')::make($saleOrder),
                ]
            );

            if ($response->getStatusCode() === 200) {
                return new JsonResponse(['message' => 'The new sales order was successfully placed']);
            } else {
                // Handle the case where the request was not successful
                $responseBody = $response->getBody()->getContents();
                return new JsonResponse(['error' => $responseBody]);
            }
        } catch (Exception $e) {
            // Handle any exceptions that occurred during the HTTP request
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update information about SaleOrder
     *
     * @param array $data
     * @param SaleOrder $saleOrder
     * @return SaleOrder
     */
    public function updateSaleOrder(array $data, SaleOrder $saleOrder): SaleOrder
    {
        $saleOrder->update(
            [
                'customer_id' => $data['customer'] ?? null,
                'order_date' => $data['order_date'],
                'preferred_delivery_date' => $data['preferred_delivery_date'] ?? null,
                'our_reference_id' => $data['our_reference'] ?? null,
                'their_reference_id' => $data['their_reference'] ?? null,
                'payment_term_id' => $data['payment_terms'] ?? null,
                'delivery_term_id' => $data['delivery_terms'] ?? null,
                'currency_id' => $data['currency'] ?? null,
                'delivery_address_id' => $data['delivery_address'] ?? null,
                'billing_address_id' => $data['billing_address'] ?? null
            ]
        );

        $saleOrdersData = [];

        if ($data['orders']) {
            foreach ($data['orders'] as $saleOrderLine) {
                $orderLineData = [
                    'id' => $saleOrderLine['id'] ?? null,
                    'product_id' => $saleOrderLine['product'] ?? null,
                    'quantity' => $saleOrderLine['quantity'] ?? null,
                    'unit_price' => $saleOrderLine['unit_price'] ?? null,
                    'discount' => $saleOrderLine['discount'] ?? null,
                    'tax_id' => $saleOrderLine['tax'] ?? null,
                ];
                $product = Product::query()->find($saleOrderLine['product']);

                $product->update(
                    [
                        'cost_price' => $saleOrderLine['unit_price'],
                    ]
                );

                $saleOrdersData[] = $orderLineData;
            }
        }

        if (isset($data['deleted_orders']) && is_array($data['deleted_orders'])) {
            foreach ($data['deleted_orders'] as $id) {
                SaleOrderLine::destroy($id);
            }
        }

        foreach ($saleOrdersData as $line) {
            $lineID = $line['id'] ?? null;

            if ($lineID) {
                $saleOrderLine = SaleOrderLine::query()->findOrFail($lineID);
                $saleOrderLine->update($line);
            } else {
                $saleOrder->lines()->create($line);
            }
        }

        return $saleOrder;
    }

    public function addOrderLines(array $orders, $saleOrder)
    {
        foreach ($orders as $order) {
            $saleOrderLine = new SaleOrderLine();
            $saleOrderLine->product_id = $order['product'] ?? null;
            $saleOrderLine->quantity = $order['quantity'] ?? null;
            $saleOrderLine->unit_price = $order['unit_price'] ?? null;
            $saleOrderLine->discount = $order['discount'] ?? null;
            $saleOrderLine->tax_id = $order['tax'] ?? null;
            $saleOrderLine->saleOrder()->associate($saleOrder);
            $product = Product::query()->find($order['product']);

            $product->update(
                [
                    'cost_price' => $order['unit_price'],
                ]
            );

            $saleOrderLine->save();
        }
    }

    /**
     * @param int $customerId
     * @return Collection|array
     */
    public function getSaleOrders(int $customerId): Collection|array
    {
        return SaleOrder::query()->where('customer_id', '=', $customerId)->get();
    }

    /**
     * @param array $saleOrderIds
     * @return Collection|array
     */
    public function getSaleOrderLines(array $saleOrderIds): Collection|array
    {
        return SaleOrderLine::query()->whereIn('sale_order_id', $saleOrderIds)->get();
    }

    /**
     * @param $saleOrderLines
     * @return mixed
     */
    public function getProductsIds($saleOrderLines): mixed
    {
        return $saleOrderLines->pluck('product_id')->all();
    }

    /**
     * @param int $customerId
     * @return array
     */
    public function getProducts(int $customerId): array
    {
        // SaleOrders - we get all the ID's of SaleOrders for requested $customerId
        $saleOrders = $this->getSaleOrders($customerId);
        $saleOrdersIds = $saleOrders->pluck('id')->all();

        // SaleOrderLines - we get all the SaleOrderLines for all the $saleOrdersIds that relate to $customerId
        $saleOrderLines = $this->getSaleOrderLines($saleOrdersIds);

        // We get the products IDs
        $productsId = $this->getProductsIds($saleOrderLines);

        // We return an array of the products here
        return Product::query()->whereIn('id', $productsId)->get()->all();
    }

    /**
     * @param int $customerId
     * @return float|int
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function getProductsTotalWeight(int $customerId): float|int
    {
        // SaleOrders - we get all the ID's of SaleOrders for requested $customerId
        $saleOrders = $this->getSaleOrders($customerId);
        $saleOrdersIds = $saleOrders->pluck('id')->all();

        // SaleOrderLines - we get all the SaleOrderLines for all the $saleOrdersIds that relate to $customerId
        $saleOrderLines = $this->getSaleOrderLines($saleOrdersIds);

        // We get the products IDs
        $productsId = $this->getProductsIds($saleOrderLines);

        $totalWeight = 0;
        foreach ($saleOrderLines as $saleOrderLineRecord) {
            $productIdPosition = array_search($saleOrderLineRecord['product_id'], $productsId);

            if ($productIdPosition !== false) {
                $product = Product::query()->findOrFail($productsId[$productIdPosition]);
                $totalWeight += $saleOrderLineRecord['quantity'] * $product->weight;
            }
        }

        return $totalWeight;
    }
}
