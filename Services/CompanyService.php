<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyBilling;
use App\Models\DeliveryAddress;
use App\Traits\CurrentCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyService
{
    /**
     * @param $company
     * @return Company
     */
    public function loadAdditionalData(Company $company): Company
    {
        $company->loadMissing(
            [
                'country',
                'companyBilling',
                'companyBilling.country',
                'companyDelivery',
                'companyDelivery.country',
                'currency',
                'language',
            ]
        );

        return $company;
    }

    /**
     * @param $company
     * @return Company
     */
    public function getCompany($company): Company
    {
        $company = auth()->user()->companies()->wherePivot('company_id', $company->id)->select(
            ['companies.*', 'is_default']
        )->first();

        return $company;
    }

    //@todo
    public function getCompanyById($company)
    {
        $company = auth()->user()->companies()
            ->wherePivot('company_id', $company->id)
            ->select('companies.id', 'companies.company_name', 'is_default')->first();

        return $company;
    }

    /**
     * @param $data
     * @return Company
     */
    public function createCompany($data): Company
    {
        $user = Auth::user();

        $company = new Company();
        $company->company_name = $data['company_name'];
        $company->industry_id = $data['industry'] ?? null;
        $company->country_id = $data['country'] ?? null;
        $company->street = $data['street'];
        $company->street_2 = $data['street_2'] ?? null;
        $company->city = $data['city'];
        $company->zipcode = $data['zipcode'];
        $company->phone_number = $data['phone_number'];
        $company->email = $data['email'];
        $company->website = $data['website'] ?? null;
        $company->currency_id = $data['currency'] ?? null;
        $company->save();

        if (isset($data['billing_address'])) {
            $company->company_billing_id = $this->createCompanyBillingAddress($data['billing_address']);
            $company->save();
        }

        if (isset($data['deliveries']) && sizeof($data['deliveries']) > 0) {
            $this->createCompanyDeliveriesAddress($data['deliveries'], $company);
        }

        if (isset($data['logo'])) {
            $company->saveLogo($data['logo']);
        }

        $user->companies()->attach($company->id, ['created_at' => Carbon::now(), 'is_default' => '1']);

        return $company;
    }

    /**
     * @param array $companyBillingAddress
     * @return int
     */
    public function createCompanyBillingAddress(array $companyBillingAddress): int
    {
        $companyBilling = new CompanyBilling();
        $companyBilling->name = $companyBillingAddress['name'] ?? null;
        $companyBilling->country_id = $companyBillingAddress['country'] ?? null;
        $companyBilling->billing_street = $companyBillingAddress['street'] ?? null;
        $companyBilling->billing_street_2 = $companyBillingAddress['street_2'] ?? null;
        $companyBilling->billing_postal = $companyBillingAddress['zipcode'] ?? null;
        $companyBilling->billing_city = $companyBillingAddress['city'] ?? null;
        $companyBilling->billing_email = $companyBillingAddress['email'] ?? null;
        $companyBilling->billing_phone = $companyBillingAddress['phone'] ?? null;
        $companyBilling->contact_name = $companyBillingAddress['contact_name'] ?? null;
        $companyBilling->is_used_for_delivery = $companyBillingAddress['is_used_for_delivery']  ? 1 : 0;
        $companyBilling->save();

        return $companyBilling->id;
    }

    /**
     * @param array $deliveries
     * @param $company
     * @return DeliveryAddress|string
     * @noinspection PhpUndefinedVariableInspection
     */
    public function createCompanyDeliveriesAddress(array $deliveries, $company): DeliveryAddress|string
    {
        foreach ($deliveries as $delivery) {
            $deliveryAddress = new DeliveryAddress();
            $deliveryAddress->name = $delivery['name'] ?? null;
            $deliveryAddress->country_id = $delivery['country'] ?? null;
            $deliveryAddress->street = $delivery['street'] ?? null;
            $deliveryAddress->street_2 = $delivery['street_2'] ?? null;
            $deliveryAddress->postal = $delivery['zipcode'] ?? null;
            $deliveryAddress->city = $delivery['city'] ?? null;
            $deliveryAddress->email = $delivery['email'] ?? null;
            $deliveryAddress->phone = $delivery['phone'] ?? null;
            $deliveryAddress->contact_person = $delivery['contact_person'] ?? null;
            $deliveryAddress->company()->associate($company);
            $deliveryAddress->save();
        }

        return $deliveryAddress;
    }


    public function updateCompany($company, array $data): Company
    {
        try{
            $company->update(
                [
                    'company_name' => $data['name'] ?? $company->company_name,
                    'industry_id' => $data['industry'] ?? $company->industry_id,
                    'country_id' => $data['country'] ?? $company->country_id,
                    'street' => $data['street'] ?? $company->street,
                    'street_2' => $data['street_2'] ?? $company->street_2,
                    'city' => $data['city'] ?? $company->city,
                    'zipcode' => $data['zipcode'] ?? $company->zipcode,
                    'phone_number' => $data['phone'] ?? $company->phone_number,
                    'email' => $data['email'] ?? $company->email,
                    'website' => $data['website'] ?? $company->website,
                    'currency_id' => $data['currency'] ?? $company->currency_id,
                ]
            );

            if (isset($data['billing_address'])) {
                $company->companyBilling()->associate(
                    $this->updateCompanyBillingAddress($company, $data['billing_address'])
                );
            }

            $deliveriesData = [];

            if (isset($data['deliveries'])) {
                foreach ($data['deliveries'] as $delivery) {
                    $deliveryData = [
                        'name' => $delivery['name'],
                        'country_id' => $delivery['country'],
                        'street' => $delivery['street'],
                        'street_2' => $delivery['street_2'] ?? null,
                        'postal' => $delivery['zipcode'] ?? null,
                        'city' => $delivery['city'],
                        'email' => $delivery['email'],
                        'phone' => $delivery['phone'],
                        'contact_person' => $delivery['contact_person'],
                    ];
                    $deliveriesData[] = $deliveryData;
                }
            }

            foreach ($data['deliveries'] as $key => $delivery) {
                $deliveries = DeliveryAddress::query()->find($delivery['id']);

                if($deliveries){
                    $deliveries->update($deliveriesData[$key]);
                } else {
                    CurrentCompany::getDefaultCompany()->companyDelivery()->create($deliveriesData[$key]);
                }
            }

            if (isset($data['deleted_deliveries']) && is_array($data['deleted_deliveries'])) {
                foreach ($data['deleted_deliveries'] as $id) {
                    DeliveryAddress::destroy($id);
                }
            }

            return $company;
        }catch(\Throwable $e) {
            dd($e);
        }
    }

    /**
     * @param $company
     * @param $data
     * @return Model|Collection|Builder|array|null
     */
    public function updateCompanyBillingAddress($company, $data): Model|Collection|Builder|array|null
    {
        $companyBilling = CompanyBilling::query()->find($company->company_billing_id);

        $companyBilling->update(
            [
                'name' => $data['name'] ?? null,
                'country_id' => $data['country'] ?? null,
                'billing_street' => $data['street'] ?? null,
                'billing_street_2' => $data['street_2'] ?? null,
                'billing_postal' => $data['zipcode'] ?? null,
                'billing_city' => $data['city'] ?? null,
                'billing_email' => $data['email'] ?? null,
                'billing_phone' => $data['phone'] ?? null,
                'contact_name' => $data['contact_person'] ?? null,
                'is_used_for_delivery' => $data['is_used_for_delivery']  ? 1 : 0,
            ]
        );

        return $companyBilling;
    }

    /**
     * @param $company
     */
    public function updateCompanyDeliveryAddress($company)
    {
        $deliveries = DeliveryAddress::query()->where('company_id', $company->id)->get();

        foreach ($deliveries as $delivery) {
            $delivery->update(
                [
                    'name' => $delivery['name'],
                    'country' => $delivery['country'],
                    'street' => $delivery['street'],
                    'street_2' => $delivery['street_2'] ?? null,
                    'postal' => $delivery['zipcode'],
                    'city' => $delivery['city'],
                    'email' => $delivery['email'],
                    'phone' => $delivery['phone'],
                    'contact_person' => $delivery['contact_person'],
                ]
            );
        }
    }
}
