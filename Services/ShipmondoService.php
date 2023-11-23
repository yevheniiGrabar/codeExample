<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\DeliveryTerms;
use App\Models\Shipment;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use ShipmondoException;
use Symfony\Component\HttpFoundation\Response;
use Shipmondo;

class ShipmondoService
{
    /**
     * @var Shipmondo
     */
    public Shipmondo $shipmondoClient;

    /**
     * @var SaleOrderService
     */
    public SaleOrderService $saleOrderService;

    public function __construct(SaleOrderService $saleOrderService)
    {
        $this->shipmondoClient = new Shipmondo(env('SHIPMONDO_API_USER'), env('SHIPMONDO_API_KEY'));
        $this->saleOrderService = $saleOrderService;
    }

    /**
     * @return JsonResponse
     */
    public function getAllShipments(): JsonResponse
    {
        try {
            return new JsonResponse(['payload' => $this->shipmondoClient->getShipments()], Response::HTTP_OK);
        }
        catch (ShipmondoException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @param int $shipmentId
     * @return JsonResponse
     */
    public function getShipmentById(int $shipmentId): JsonResponse
    {
        try {
            return new JsonResponse(['payload' => $this->shipmondoClient->getShipment($shipmentId)], Response::HTTP_OK);
        }
        catch (ShipmondoException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @param string $senderCountryCode
     * @param string $receiverCountryCode
     * @return JsonResponse
     */
    public function getCarriers(string $senderCountryCode, string $receiverCountryCode): JsonResponse
    {
        try {
            $paramsForCarrier = [
                'sender_country_code' => $senderCountryCode,
                'receiver_country_code' => $receiverCountryCode,
            ];

            return new JsonResponse(['payload' => $this->shipmondoClient->getCarriers($paramsForCarrier)], Response::HTTP_OK);
        }
        catch (ShipmondoException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @param string $senderCountryCode
     * @param string $receiverCountryCode
     * @param string $carrierCode
     * @return JsonResponse
     */
    public function getAvailableProducts(string $senderCountryCode, string $receiverCountryCode, string $carrierCode): JsonResponse
    {
        try {
            $paramsForProducts = [
                'carrier_code' => $carrierCode,
                'sender_country_code' => $senderCountryCode,
                'receiver_country_code' => $receiverCountryCode,
            ];

            return new JsonResponse(['payload' => $this->shipmondoClient->getProducts($paramsForProducts)], Response::HTTP_OK);
        }
        catch (ShipmondoException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @param array $shipmentData
     * @return JsonResponse
     */
    public function createNewShipment(array $shipmentData): JsonResponse
    {
        try {
            // companyData == sender information
            $companyData = $this->getCurrentCompanyData();

            // receiver information
            $receiverData = $this->getReceiverData($shipmentData['customer_id']);
            $receiverShippingAddress = $this->getReceiverShippingAddress($receiverData->shipping_address_id);

            // this product_code is sent from the front end for selected product (service) offered by the selected carrier
            $productCode = $shipmentData['product_code'];

            // total weight of the shipment
            $shipmentTotalWeight = $this->saleOrderService->getProductsTotalWeight($shipmentData['customer_id']);

            $shipmentParams = [
                'test_mode' => true,
                'own_agreement' => false,
                'label_format' => 'a4_pdf',
                'product_code' => $productCode,
                'service_codes' => 'EMAIL_NT,SMS_NT',
                'reference' => 'Order 10001',
                'automatic_select_service_point' => true,
                'sender' => [
                    'name' => $companyData->company_name,
                    'address1' => $companyData->street,
                    'address2' => null,
                    'zipcode' => $companyData->zipcode,
                    'city' => $companyData->city,
                    'country_code' => $companyData->country_code,
                    'email' => $companyData->email,
                    'mobile' => $companyData->phone_number,
                ],
                'receiver' => [
                    'name' => $receiverData->customer_name,
                    'address1' => $receiverShippingAddress->street,
                    'address2' => $receiverShippingAddress->street_2,
                    'zipcode' => $receiverShippingAddress->zipcode,
                    'city' => $receiverShippingAddress->city,
                    'country_code' => $receiverShippingAddress->country_code,
                    'email' => $receiverData->contact_email,
                    'mobile' => $receiverData->contact_phone,
                ],
                'parcels' => [
                    [
                        'weight' => $shipmentTotalWeight,
                    ]
                ],
            ];

            return new JsonResponse(['payload' => $this->shipmondoClient->createShipment($shipmentParams)], Response::HTTP_OK);
        }
        catch (ShipmondoException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * @return Model|Collection|Builder|array|null
     */
    public function getCurrentCompanyData(): Model|Collection|Builder|array|null
    {
        // Sender info
        $userCompany = Auth::user()->companies()
            ->newPivotStatement()
            ->where('is_default', true)
            ->first();

        return Company::query()->find($userCompany->company_id);
    }

    /**
     * @param $customerId
     * @return Model|Collection|Builder|array|null
     */
    public function getReceiverData($customerId): Model|Collection|Builder|array|null
    {
        return Customer::query()->find($customerId);
    }

    /**
     * @param $shippingAddressId
     * @return Model|Collection|Builder|array|null
     */
    public function getReceiverShippingAddress($shippingAddressId): Model|Collection|Builder|array|null
    {
        return ShippingAddress::query()->find($shippingAddressId);
    }
}
