<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierContacts;
use App\Models\SupplierReturn;
use App\Traits\CurrentCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierService
{

    /**
     * @param $supplier
     * @return Supplier
     */
    public function loadAdditionalData(Supplier $supplier): Supplier
    {
        $supplier->loadMissing(
            [
                'contacts' => fn($q) => $q->select(['id', 'supplier_id', 'name', 'phone', 'email']),
                'tax' => fn($q) => $q->select(['id', 'rate']),
                'returns' => fn($q) => $q->select(['*']),
            ]
        );

        return $supplier;
    }

    /**
     * @param array $data
     * @return Supplier|\Exception
     */
    public function createSupplier(array $data): \Exception|Supplier
    {
        try {
            $currentCompany = CurrentCompany::getDefaultCompany();
            DB::beginTransaction();

            try {
                $supplier = new Supplier;

                $supplier->name = $data['name'];
                $supplier->code = $data['code'];
                $supplier->vat = $data['vat'] ?? null;
                $supplier->tax_id = $data['tax_rate'] ?? null;
                $supplier->currency_id = $data['currency'] ?? null;
                $supplier->payment_term_id = $data['payment_terms'] ?? null;
                $supplier->company_id = $currentCompany->company_id;

                if (isset($data['billing'])) {
                    $supplier->billing_name = $data['billing']['name'] ?? null;
                    $supplier->billing_street = $data['billing']['street'] ?? null;
                    $supplier->billing_street_2 = $data['billing']['street_2'] ?? null;
                    $supplier->billing_zipcode = $data['billing']['zipcode'] ?? null;
                    $supplier->billing_city = $data['billing']['city'] ?? null;
                    $supplier->country_id = $data['billing']['country'] ?? null;
                    $supplier->billing_phone = $data['billing']['phone'] ?? null;
                    $supplier->billing_email = $data['billing']['email'] ?? null;
                    $supplier->is_used_for_return = $data['billing']['is_used_for_return'] ?? null;
                };

                $supplier->save();

                $contactsArray = [];
                if (!empty($data['contacts']) && sizeof($data['contacts']) > 0) {
                    foreach ($data['contacts'] as $key => $contactData) {
                        $contact = [
                            "name" => $contactData["name"],
                            "phone" => $contactData["phone"],
                            "email" => $contactData["email"],
                            "supplier_id" => $supplier->id,
                        ];

                        $contactsArray[] = $contact;
                    }
                    SupplierContacts::query()->insert($contactsArray);
                }

                $returnsArray = [];

                if (!empty($data['returns']) && sizeof($data['returns']) > 0) {
                    foreach ($data['returns'] as $key => $returnData) {
                        $return = [
                            'name' => $returnData['name'],
                            'street' => $returnData['street'] ?? null,
                            'street_2' => $returnData['street_2'] ?? null,
                            'zipcode' => $returnData['zipcode'] ?? null,
                            'city' => $returnData['city'] ?? null,
                            'country_id' => $returnData['country'] ?? null,
                            'contact_person' => $returnData['contact_person'] ?? null,
                            'phone' => $returnData['phone'] ?? null,
                            'email' => $returnData['phone'] ?? null,
                            'supplier_id' => $supplier->id,
                        ];
                        $returnsArray[] = $return;
                    }
                    SupplierReturn::query()->insert($returnsArray);
                }

                DB::commit();
            } catch (\Throwable $e) {
                dd($e);
            }
        } catch (\Exception $exception) {
            return $exception;
        }

        return $supplier;
    }

    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $supplier->update(
            [
                'name' => $data['name'] ?? $supplier->name,
                'code' => $data['code'] ?? $supplier->code,
                'vat' => $data['vat'] ?? $supplier->vat,
                'tax_id' => $data['tax_rate'] ?? $supplier->tax_id,
                'currency_id' => $data['currency'] ?? $supplier->currency_id,
                'payment_term_id' => $data['payment_terms'] ?? $supplier->payment_term_id,
                'company_id' => $currentCompany->company_id,
            ]
        );

        $supplier->save();

        if (isset($data['contacts'])) {
            foreach ($data['contacts'] as $contact) {
                $contactId = $contact['id'] ?? null;

                if ($contactId) {
                    $contactRecord = SupplierContacts::query()->findOrFail($contactId);
                    if(isset($contact['country'])) {
                        $contact['country_id'] = $contact['country'];
                        unset($contact['country']);
                    };
                    $contactRecord->update($contact);
                } else {
                    $contactRecord = SupplierContacts::query()->create($contact);
                    $supplier->contacts()->save($contactRecord);
                }
            }
        }
        if (isset($data['returns'])) {
            foreach ($data['returns'] as $return) {
                $returnId = $return['id'] ?? null;

                if ($returnId) {
                    $returnRecord = SupplierReturn::query()->findOrFail($returnId);
                    if (isset($return['country'])) {
                        $return['country_id'] = $return['country'];
                        unset($return['country']);
                    }
                    $returnRecord->update($return);
                } else {
                    $returnRecord = SupplierReturn::query()->create($return);
                    $supplier->returns()->save($returnRecord);
                }
            }
        }


        if (isset($data['deleted_returns']) && is_array($data['deleted_returns'])) {
            foreach ($data['deleted_returns'] as $id) {
                SupplierReturn::destroy($id);
            }
        }

        if (isset($data['deleted_contacts']) && is_array($data['deleted_contacts'])) {
            foreach ($data['deleted_contacts'] as $contact_id) {
                SupplierContacts::destroy($contact_id);
            }
        }

        return $supplier;
    }


    /**
     * @param Request $request
     * @return string[]
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function supplierParseRequest(Request $request): array
    {
        $search = '';
        $orderBy = [];

        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
        }
        if ($request->has('orderBy')) {
            $parsedData = $request->get('orderBy');

            $orderBy = [
                'name' => $parsedData['name'] ?? '',
                'type' => $parsedData['type'] ?? ''
            ];
        }

        return [
            'search' => $search,
            'orderBy' => $orderBy
        ];
    }

    /**
     * @return mixed
     */
    public function getCurrentCompany(): mixed
    {
        return CurrentCompany::getDefaultCompany();
    }

    /**
     * @param array $data
     * @param Supplier $supplier
     * @return array
     */
    public function updateSupplierBilling(array $data, Supplier $supplier)
    {
        if (isset($data['billing'])) {
            $supplier->billing_name = $data['billing']['name'] ?? null;
            $supplier->billing_street = $data['billing']['street'] ?? null;
            $supplier->billing_street_2 = $data['billing']['street_2'] ?? null;
            $supplier->billing_zipcode = $data['billing']['zipcode'] ?? null;
            $supplier->billing_city = $data['billing']['city'] ?? null;
            $supplier->country_id = $data['billing']['country'] ?? null;
            $supplier->billing_phone = $data['billing']['phone'] ?? null;
            $supplier->billing_email = $data['billing']['email'] ?? null;
            $supplier->is_used_for_return = $data['billing']['is_used_for_return'] ? 1 : 0;
        }

        $supplier->save();
    }

    /**
     * @param $company_id
     * @return array
     */
    public function getSupplierCodes($company_id): array
    {
        return Supplier::query()
            ->where('company_id', $company_id)
            ->pluck('code')
            ->toArray();
    }

    /**
     * @param $company_id
     * @return int
     */
    public function generateUniqueSupplierCode($company_id): int
    {
        $usedCodes = $this->getSupplierCodes($company_id);

        $minNumber = 10000;
        $maxNumber = 19999;
        $attempts = 0;

        do {
            $randomNumber = mt_rand($minNumber, $maxNumber);

            $uniqueNumber = intval($company_id . str_pad($randomNumber, strlen($maxNumber), '0', STR_PAD_LEFT));

            $attempts++;
        } while (in_array($uniqueNumber, $usedCodes) && $attempts < ($maxNumber - $minNumber + 1));

        $usedCodes[] = $uniqueNumber;

        return $uniqueNumber;
    }
}
