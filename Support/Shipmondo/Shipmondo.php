<?php

namespace App\Support\Shipmondo;

use App\Models\Company;
use App\Models\SaleOrder;
use App\Models\SaleOrderLine;
use App\Models\ShipmondoAuth;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Shipmondo {
    private \Shipmondo $shipmondo;

    public function __construct($user = null, $key = null)
    {
        if(!$user && !$key) {
            $shipmondoAuth = ShipmondoAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)->first();
            $this->shipmondo = new \Shipmondo($shipmondoAuth->user, $shipmondoAuth->key);
        }else{
            $this->shipmondo = new \Shipmondo($user, $key);
        }
    }

    public function getAccountBalance(): array
    {
        return $this->shipmondo->getAccountBalance();
    }

    public function createSaleOrder(SaleOrder $saleOrder): array
    {
        $saleOrder = SaleOrder::find($saleOrder->id);
        $company = Company::find(CurrentCompany::getDefaultCompany()->company_id);
        $customer = $saleOrder->customer;
        $deliveryAddress = $saleOrder->deliveryAddress;
        $billingAddress = $saleOrder->billingAddress;
        $totalPrice = 0;
        $totalTaxes = 0;
        $orderLines = [];

        $saleOrder->lines->each(function (SaleOrderLine $line) use (&$totalPrice, &$totalTaxes, &$orderLines) {
            $totalTaxes = ($line->unit_price * $line->quantity) * $line->tax->rate;
            $totalPrice += ($line->unit_price * $line->quantity);

            $orderLines[] = [
                'line_type' => 'item',
                'item_name' => $line->product->name,
                'quantity' => $line->quantity,
                'currency_code' => $line->saleOrder->currency->code,
                'unit_price_excluding_vat' => $line->product->selling_price,
                'unit_weight' => $line->product->weight,
                'item_sku' => $line->product->product_code,
            ];
        });

        $params = [
            'order_id' => $saleOrder->id,
            'ordered_at' => Carbon::parse($saleOrder->created_at)->toIso8601String(),
            'source_name' => 'Suppli -> ' . $company->company_name,
            'ship_to' => [
                'name' => $customer->customer_name ?: $customer->first_name . ' ' . $customer->last_name,
                'address1' => $deliveryAddress->street,
                'address2' => $deliveryAddress->street_2,
                'zipcode' => $deliveryAddress->postal,
                'city' => $deliveryAddress->city,
                'country_code' => $deliveryAddress->country->code,
                'email' => $deliveryAddress->email,
                'phone' => $deliveryAddress->phone,
            ],
            'sender' => [
                'name' => $company->company_name,
                'address1' => $company->street,
                'address2' => $company->street_2,
                'zipcode' => $company->zipcode,
                'city' => $company->city,
                'country_code' => $company->country->code,
            ],
            'bill_to' => [
                'name' => $customer->customer_name ?: $customer->first_name . ' ' . $customer->last_name,
                'address1' => $billingAddress->street,
                'address2' => $billingAddress->street_2,
                'zipcode' => $billingAddress->zipcode,
                'city' => $billingAddress->city,
                'country_code' => $billingAddress->country->code,
                'email' => $billingAddress->email,
                'phone' => $billingAddress->phone,
            ],
            'payment_details' => [
                'amount_including_vat' => $totalPrice + $totalTaxes,
                'currency_code' => $saleOrder->currency->code,
                'vat_amount' => $totalTaxes,
            ],
            'order_lines' => $orderLines
        ];

        Log::debug(json_encode($params));

        $response = $this->shipmondo->createSalesOrder($params);

        $saleOrder->update([
            'shipmondo_id' => $response['output']['id']
        ]);

        return $response;
    }

    public function createShipment($id): array
    {
        return $this->shipmondo->createSalesOrderShipment($id);
    }

    public function isIntegrated() {
        return ShipmondoAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)->exists();
    }
}
