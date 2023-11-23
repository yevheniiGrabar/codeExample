<?php

namespace App\Support\PowerOffice;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PowerOfficeAuth;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Traits\CurrentCompany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PowerOffice {
    const POWER_OFFICE_URL_PROD = 'https://api.poweroffice.net/';
    const POWER_OFFICE_URL_DEV = 'https://api-demo.poweroffice.net/';

    private string $baseUrl;

    private string $token;

    public function __construct()
    {
        if(config('app.env') === 'local'){
            $this->baseUrl = self::POWER_OFFICE_URL_DEV;
        }else{
            $this->baseUrl = self::POWER_OFFICE_URL_PROD;
        }
    }

    private function setToken($app = null, $client = null): void
    {
        if(!$app && !$client) {
            $powerOfficeAuth = PowerOfficeAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)
                ->firstOrFail();

            $response = Http::withBasicAuth($powerOfficeAuth->app_key, $powerOfficeAuth->client_key)
                ->asForm()
                ->post($this->baseUrl . 'OAuth/Token', [
                    'grant_type' => 'client_credentials'
                ]);
        }else{
            $response = Http::withBasicAuth($app, $client)
                ->asForm()
                ->post($this->baseUrl . 'OAuth/Token', [
                    'grant_type' => 'client_credentials'
                ]);
        }

        $this->token = json_decode($response->body(), true)['access_token'];
    }

    public function fetchProductGroup(Category $category): Category
    {
        $data = [
            'name' => $category->name,
            'code' => $category->number
        ];

        if($category->power_office_id) {
            $data['id'] = $category->power_office_id;
        }

        $this->setToken();

        $response = Http::withToken($this->token)
            ->post($this->baseUrl. 'ProductGroup/', $data);

        $response = json_decode($response->body(), true);

        if($response['success']) {
            $category->update([
                'has_powerOffice' => 1,
                'power_office_id' => $response['data']['id']
            ]);
        }

        return $category;
    }

    public function fetchProduct(Product $product): Product
    {
        if($product->category) {
            $this->fetchProductGroup($product->category);
        }

        $data = [
            'name' => $product->name,
            'code' => $product->product_code,
            'description' => $product->description,
            'costPrice' => $product->cost_price,
            'salesPrice' => $product->selling_price,
            'isActive' => !$product->is_deleted,
            'productGroupId' => $product->category?->power_office_id,
            'unitOfMeasureCode' => $product->unit?->barcode ? $product->unit->barcode : 'EA',
            'productsOnHand' => $product->in_stock,
        ];

        if($product->power_office_id) {
            $data['id'] = $product->power_office_id;
        }

        $this->setToken();

        $response = Http::withToken($this->token)
            ->post($this->baseUrl. 'Product/', $data);

        $response = json_decode($response->body(), true);

        if($response['success']) {
            Log::debug('AAAAAAAAAAAAAAAAAA' . $response['data']['id']);

            $product->update([
                'has_powerOffice' => 1,
                'power_office_id' => $response['data']['id']
            ]);
        }

        return $product;
    }

    public function fetchCustomer(Customer $customer, $currencyCode): Customer
    {
        $mailAddress = [
            'city' => $customer->billingAddress->city,
            'zipCode' => $customer->billingAddress->zipcode,
            'address1' => $customer->billingAddress->street,
            'address2' => $customer->billingAddress->street_2,
            'countryCode' => $customer->billingAddress?->country?->code,
            'isPrimary' => true,
            'externalCode' => '',
        ];

        if($customer->billingAddress->power_office_id) {
            $mailAddress['id'] = $customer->billingAddress->power_office_id;
        }

        $streetAddresses = $customer->deliveryAddresses->map(function ($deliveryAddress) {
            $data = [
                'city' => $deliveryAddress->city,
                'zipCode' => $deliveryAddress->zipcode,
                'address1' => $deliveryAddress->street,
                'address2' => $deliveryAddress->street_2,
                'countryCode' => $deliveryAddress?->country?->code,
                'isPrimary' => $deliveryAddress->is_primary,
                'externalCode' => '',
            ];

            if($deliveryAddress->power_office_id){
                $data['id'] = $deliveryAddress->power_office_id;
            }

            return $data;
        })->toArray();

        $data = [
            'isVatFree' => !($customer->tax?->rate > 0),
            'discountPercent' => $customer->discount ? $customer->discount : 0,
            'invoiceEmailAddress' => $customer->billingAddress->email,
            'paymentTerms' => 0,
            'deliveryTerm' => '',
            'name' => $customer->is_person ? $customer->first_name . ' ' . $customer->last_name : $customer->customer_name,
            'legalName' => $customer->is_person ? $customer->first_name . ' ' . $customer->last_name : $customer->customer_name,
            'since' => $customer->created_at,
            'isPerson' => $customer->is_person,
            'currencyCode' => $currencyCode,
            'isActive' => true,
            'code' => $customer->customer_code,
            'mailAddress' => $mailAddress,
            'streetAddresses' => $streetAddresses,
            'emailAddress' => $customer->email,
            'phoneNumber' => $customer->phone,
            'isArchived' => false,
            'firstName' => $customer->first_name,
            'lastName' => $customer->last_name,
        ];

        if($customer->power_office_id) {
            $data['id'] = $customer->power_office_id;
        }

        $this->setToken();

        $response = Http::withToken($this->token)
            ->post($this->baseUrl. 'Customer/', $data);

        $response = json_decode($response->body(), true);

        if($response['success']) {
            $customer->update([
                'has_powerOffice' => 1,
                'power_office_id' => $response['data']['id']
            ]);

            $customer->billingAddress()->update([
                'power_office_id' => $response['data']['mailAddress']['id']
            ]);

            foreach($response['data']['streetAddresses'] as $key => $streetAddress) {
                $customer->deliveryAddresses[$key]->update([
                    'power_office_id' => $streetAddress['id']
                ]);
            }
        }

        return $customer;
    }

    public function fetchOutgoingInvoice(SaleOrder $saleOrder): SaleOrder
    {
        $customer = $saleOrder->customer;
        $products = $saleOrder->lines->pluck('product');

        foreach ($products as $product) {
            $product = Product::find($product['id']);

            $this->fetchProduct($product);
        }

        $this->fetchCustomer($customer, $saleOrder->currency->code);



        $lines = $saleOrder->lines->map(function ($line) {
            $data = [
                'quantity' => $line->quantity,
                'discountPercent' => '-' . ($line->discount / 1000),
                'productCode' => Product::find($line->product->id)->product_code,
                'unit_price' => $line->unit_price,
                'isDeleted' => false,
            ];

            if($line->power_office_id) {
                $data['id'] = $line->power_office_id;
            }

            return $data;
        })->toArray();

        $data = [
            'orderDate' => $saleOrder->order_date,
            'currencyCode' => $saleOrder->currency->code,
            'customerCode' => $saleOrder->customer->customer_code,
            'status' => 0,
            'deliveryDate' => $saleOrder->preferred_delivery_date,
            'deliveryAddressId' => $saleOrder->deliveryAddress->power_office_id,
            'outgoingInvoiceLines' => $lines,
        ];

        if($saleOrder->power_office_id) {
            $data['id'] = $saleOrder->power_office_id;
        }

        $this->setToken();

        $response = Http::withToken($this->token)
            ->post($this->baseUrl. 'OutgoingInvoice/', $data);

        $response = json_decode($response->body(), true);

        if($response['success']) {
            $saleOrder->update([
                'power_office_id' => $response['data']['id']
            ]);

            foreach($response['data']['outgoingInvoiceLines'] as $key => $outgoingInvoiceLine) {
                $saleOrder->lines[$key]->update([
                    'power_office_id' => $outgoingInvoiceLine['id']
                ]);
            }
        }

        return $saleOrder;
    }

    public function testConnection($appKey, $clientKey): void
    {
        $this->setToken($appKey, $clientKey);
    }

    public function isIntegrated() {
        return PowerOfficeAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)
            ->exists();
    }
}
