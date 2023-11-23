<?php

namespace App\Services;

use App\Http\Requests\Customer\UpdateRequest;
use App\Models\BillingAddress;
use App\Models\Customer;
use App\Models\CustomerContacts;
use App\Models\DeliveryAddress;
use App\Traits\CurrentCompany;
use Exception;
use Illuminate\Http\Request;

class CustomerService
{
    /**
     * @param Request $request
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function customerParseRequest(Request $request): array
    {
        if ($request->has('filters')) {
            $parsedData = json_decode($request->get('filters'));

            $search = $parsedData->search ?? '';
        }

        return [
            'search' => $search ?? '',
        ];
    }

    /**
     * @param array $contactsData
     * @param int $position
     * @return CustomerContacts
     */
    public function getCustomerContact(array $contactsData, int $position): CustomerContacts
    {
        $customerContact = new CustomerContacts();

        $customerContact->contact_name = $contactsData['contacts'][$position]['name'];
        $customerContact->contact_phone = $contactsData['contacts'][$position]['phone'];
        $customerContact->contact_email = $contactsData['contacts'][$position]['email'];
        $customerContact->customer_id = $contactsData['customer_id'];
        return $customerContact;
    }

    /**
     * @param array $contactsData
     * @return array
     */
    public function addContacts(array $contactsData): array
    {
        $contactsId = [];

        $numberOfContacts = count($contactsData['contacts']);
        for ($position = 0; $position < $numberOfContacts; $position++) {
            $customerContact = $this->getCustomerContact($contactsData, $position);

            if ($customerContact->save()) {
                $contactsId[] = $customerContact->id;
            }
        }

        // Returning the IDs of the contacts that have been added
        // need to implement a checking here if every contact has been added
        return $contactsId;
    }

    /**
     * @param $deliveriesData
     * @return DeliveryAddress
     */
    public function getDeliveryAddress($deliveriesData): DeliveryAddress
    {
        $deliveryAddress = new DeliveryAddress();

        $deliveryAddress->name = $deliveriesData['name'];
        $deliveryAddress->street = $deliveriesData['street'] ?? null;
        $deliveryAddress->street_2 = $deliveriesData['street_2'] ?? null;
        $deliveryAddress->email = $deliveriesData['email'] ?? null;
        $deliveryAddress->phone = $deliveriesData['phone'] ?? null;
        $deliveryAddress->postal = $deliveriesData['zipcode'] ?? null;
        $deliveryAddress->city = $deliveriesData['city'] ?? null;
        $deliveryAddress->country = $deliveriesData['country'] ?? null;
        $deliveryAddress->contact_person = $deliveriesData['contact_person'] ?? null;
        $deliveryAddress->is_primary = $deliveriesData['is_primary'] ? 1 : 0;
        return $deliveryAddress;
    }

    /**
     * @param array $deliveriesData
     * @return array
     */
    public function addDeliveries(array $deliveriesData): array
    {
        $deliveriesId = [];

        $numberOfDeliveries = count($deliveriesData['deliveries']);

        for ($position = 0; $position < $numberOfDeliveries; $position++) {
            $deliveryAddress = $this->getDeliveryAddress($deliveriesData['deliveries'][$position]);
            $deliveryAddress->customer_id = $deliveriesData['customer_id'];

            if ($deliveryAddress->save()) {
                $deliveriesId[] = $deliveryAddress->id;
            }
        }

        return $deliveriesId;
    }

    /**
     * @param $customerData
     * @param $customer
     * @return Customer
     */
    public function createPerson($customerData, $customer): Customer
    {
        $customer->first_name = $customerData['first_name'];
        $customer->last_name = $customerData['last_name'];
        $customer->national_id_number = $customerData['national_id_number'];
        $customer->date_of_birth = $customerData['date_of_birth'];
        $customer->gender = $customerData['gender'];
        $customer->save();

        return $customer;
    }


    /**
     * @param $data
     * @return Customer
     * @throws Exception
     */
    public function createCustomer(array $data): Customer
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $customer = new Customer();

        if (isset($data['is_person']) && (!empty($data['is_person']) || $data['is_person'] == 1)) {
            $this->createPerson($data, $customer);
        } else {
            // Check, if exist customer_code in $customerData array
            if (!empty($data['code'])) {
                $usedCodes = $this->getCustomerCodes($currentCompany->company_id);
                $code = intval($data['code']);

                if (in_array($code, $usedCodes)) {
                    throw new Exception('Code is not unique for this company.');
                }

                // Use existing code;
                $customer->customer_code = $code;
            } else {
                // Generate unique customer_code, if  customer_field is empty.
                $uniqueCode = $this->generateUniqueCode($currentCompany->company_id);
                $customer->customer_code = $uniqueCode;
            }

            $customer->customer_name = $data['name'];
            $customer->vat_number = $data['vat'] ?? null;
            $customer->discount = $data['discount'] ?? null;
            $customer->tax_id = $data['tax_rate'] ?? null;
            $customer->company_id = $currentCompany->company_id;
            $customer->save();

            if (!empty($data['billing'])) {
                $this->createCustomerBillingAddress($data['billing'], $customer);
            }

            if (!empty($data['contacts']) && sizeof($data['contacts']) > 0) {
                $this->createCustomerContacts($data['contacts'], $customer);
            }

            if (!empty($data['deliveries'])) {
                $this->createCustomerDeliveryAddress($data['deliveries'], $customer);
            }


        }

        return $customer;
    }


    /**
     * @param $contacts
     * @param $customer
     */
    public function createCustomerContacts($contacts, $customer)
    {
        foreach ($contacts as $contact) {
            $customerContact = new CustomerContacts();
            $customerContact->contact_name = $contact['name'];
            $customerContact->contact_email = $contact['email'];
            $customerContact->contact_phone = $contact['phone'];
            $customerContact->customer_id = $customer->id ?? null;
            $customerContact->save();
        }
    }

    /**
     * @param $billing
     * @param $customer
     * @return BillingAddress
     */
    public function createCustomerBillingAddress($billing, $customer): BillingAddress
    {
        $billingAddress = new BillingAddress();
        $billingAddress->name = $billing['name'] ?? null;
        $billingAddress->street = $billing['street'] ?? null;
        $billingAddress->street_2 = $billing['street_2'] ?? null;
        $billingAddress->email = $billing['email'] ?? null;
        $billingAddress->zipcode = $billing['zipcode'] ?? null;
        $billingAddress->city = $billing['city'] ?? null;
        $billingAddress->country_id = $billing['country'] ?? null;
        $billingAddress->phone = $billing['phone'] ?? null;
        $billingAddress->is_used_for_shipping = $billing['is_used_for_shipping'] ? 1 : 0;
        $billingAddress->customer_id = $customer->id ?? null;
        $billingAddress->save();

        return $billingAddress;
    }


    /**
     * @param $deliveries
     * @param $customer
     */
    public function createCustomerDeliveryAddress($deliveries, $customer): DeliveryAddress
    {
        if (!empty($deliveries) && sizeof($deliveries) > 0) {
            foreach ($deliveries as $delivery) {
                $deliveryAddress = new DeliveryAddress();

                $deliveryAddress->name = $delivery['name'];
                $deliveryAddress->street = $delivery['street'] ?? null;
                $deliveryAddress->street_2 = $delivery['street_2'] ?? null;
                $deliveryAddress->email = $delivery['email'] ?? null;
                $deliveryAddress->phone = $delivery['phone'] ?? null;
                $deliveryAddress->postal = $delivery['zipcode'] ?? null;
                $deliveryAddress->city = $delivery['city'] ?? null;
                $deliveryAddress->country_id = $delivery['country'] ?? null;
                $deliveryAddress->contact_person = $delivery['contact_person'] ?? null;
                $deliveryAddress->is_primary = $delivery['is_primary'] ? 1 : 0;
                $deliveryAddress->customer_id = $customer->id ?? null;
                $deliveryAddress->save();
            }
        }

        return $deliveryAddress;
    }

    /**
     * @param Customer $customer
     * @param array $customerData
     * @param array $billingData
     * @return Customer
     */
    public function updateCustomer(Customer $customer, array $customerData, array $billingData): Customer
    {
        $customer->update(
            [
                'customer_name' => $customerData['name'] ?? $customer->customer_name ?? null,
                'customer_code' => $customerData['code'] ?? $customer->customer_code ?? null,
                'vat_number' => $customerData['vat'] ?? $customer->vat_number ?? null,
                'discount' => $customerData['discount'] ?? $customer->discount ?? null,
                'tax_id' => $customerData['tax_rate'] ?? $customer->tax_id ?? null,
                'name' => $billingData['billing']['name'] ?? $customer->name ?? null,
                'street' => $billingData['billing']['street'] ?? $customer->street ?? null,
                'street_2' => $billingData['billing']['street_2'] ?? $customer->street_2 ?? null,
                'email' => $billingData['billing']['email'] ?? $customer->email ?? null,
                'zipcode' => $billingData['billing']['zipcode'] ?? $customer->zipcode ?? null,
                'city' => $billingData['billing']['city'] ?? $customer->city ?? null,
                'country' => $billingData['billing']['country'] ?? $customer->country ?? null,
                'phone' => $billingData['billing']['phone'] ?? $customer->phone ?? null,
                'is_used_for_shipping' => $billingData['billing']['is_used_for_shipping'] ?? $customer->is_used_for_shipping ?? null,
            ]
        );
        $customer->billingAddress()->update(
            [
                'name' => $billingData['billing']['name'] ?? $customer->name ?? null,
                'street' => $billingData['billing']['street'] ?? $customer->street ?? null,
                'street_2' => $billingData['billing']['street_2'] ?? $customer->street_2 ?? null,
                'email' => $billingData['billing']['email'] ?? $customer->email ?? null,
                'zipcode' => $billingData['billing']['zipcode'] ?? $customer->zipcode ?? null,
                'city' => $billingData['billing']['city'] ?? $customer->city ?? null,
                'country_id' => $billingData['billing']['country'] ?? $customer->country ?? null,
                'phone' => $billingData['billing']['phone'] ?? $customer->phone ?? null,
                'is_used_for_shipping' => $billingData['billing']['is_used_for_shipping'] ? 1 : 0,
            ]
        );

        return $customer;
    }

    /**
     * @param array $contactsData
     * @return void
     */
    public function updateContacts(array $contactsData): void
    {
        $numberOfContacts = count($contactsData['contacts']);

        for ($position = 0; $position < $numberOfContacts; $position++) {
            if (isset($contactsData['contacts'][$position]['id'])) {
                $contact = CustomerContacts::find($contactsData['contacts'][$position]['id']);

                if ($contact) {
                    $contact->contact_name = $contactsData['contacts'][$position]['name'] ?? $contact->contact_name;
                    $contact->contact_phone = $contactsData['contacts'][$position]['phone'] ?? $contact->contact_phone;
                    $contact->contact_email = $contactsData['contacts'][$position]['email'] ?? $contact->contact_email;
                    $contact->save();
                }
            } else {
                $customerContact = $this->getCustomerContact($contactsData, $position);
                $customerContact->save();
            }
        }
    }

    public function updateDeliveries(array $deliveriesData)
    {
        $numberOfDeliveries = count($deliveriesData['deliveries']);

        for ($position = 0; $position < $numberOfDeliveries; $position++) {
            if (isset($deliveriesData['deliveries'][$position]['id'])) {
                $deliveryAddress = DeliveryAddress::find($deliveriesData['deliveries'][$position]['id']);

                if ($deliveryAddress) {
                    $deliveryAddress->name = $deliveriesData['deliveries'][$position]['name'] ?? $deliveryAddress->name;
                    $deliveryAddress->street = $deliveriesData['deliveries'][$position]['street'] ?? $deliveryAddress->street;
                    $deliveryAddress->street_2 = $deliveriesData['deliveries'][$position]['street_2'] ?? $deliveryAddress->street_2;
                    $deliveryAddress->email = $deliveriesData['deliveries'][$position]['email'] ?? $deliveryAddress->email;
                    $deliveryAddress->phone = $deliveriesData['deliveries'][$position]['phone'] ?? $deliveryAddress->phone;
                    $deliveryAddress->postal = $deliveriesData['deliveries'][$position]['zipcode'] ?? $deliveryAddress->postal;
                    $deliveryAddress->city = $deliveriesData['deliveries'][$position]['city'] ?? $deliveryAddress->city;
                    $deliveryAddress->country_id = $deliveriesData['deliveries'][$position]['country'] ?? $deliveryAddress->country;
                    $deliveryAddress->contact_person = $deliveriesData['deliveries'][$position]['contact_person'] ?? $deliveryAddress->contact_person;
                    $deliveryAddress->is_primary = $deliveriesData['deliveries'][$position]['is_primary'] ?? $deliveryAddress->is_primary;
                    $deliveryAddress->save();
                }
            } else {
                $newDeliveryAddress = $this->getDeliveryAddress($deliveriesData['deliveries'][$position]);
                $newDeliveryAddress->save();
            }
        }
    }

    /**
     * @param array $deleteContactsIds
     * @return void
     */
    public function deleteContacts(array $deleteContactsIds): void
    {
        foreach ($deleteContactsIds as $contact) {
            CustomerContacts::destroy($contact);
        }
    }

    public function deleteDeliveries(array $deletedDeliveriesIds)
    {
        foreach ($deletedDeliveriesIds as $delivery) {
            DeliveryAddress::destroy($delivery);
        }
    }

    /**
     * @param Customer $customer
     * @param UpdateRequest $request
     * @return Customer
     */
    public function updateCustomerData(Customer $customer, UpdateRequest $request): Customer
    {
        $customerData = $request->only(['name', 'code', 'vat', 'group', 'tax_rate', 'discount']);
        $contactsData = $request->only(['contacts']);
        $billingData = $request->only(['billing']);
        $deliveriesData = $request->only(['deliveries']);
        $deleteContactsIds = $request->only(['delete_contacts']);
        $deletedDeliveriesIds = $request->only(['deleted_deliveries']);

        // Storing the ID's we get from other methods in $customerData array
//        $customerData['billing'] = $this->addBilling($billingData);

        // Update customer
        $customer = $this->updateCustomer($customer, $customerData, $billingData);

        // Update contacts or create if it doesn't exist
        $contactsData['customer_id'] = $customer['id'];
        $this->updateContacts($contactsData);

//        // Update billing
//        $billingData['customer_id'] = $customer['id'];
//        $this->updateBilling($billingData);

        // Update deliveries
        $this->updateDeliveries($deliveriesData);

        // Deleting contacts if their IDs are provided
        if (!empty($deleteContactsIds)) {
            $this->deleteContacts($deleteContactsIds);
        }

        // Deleting delivery addresses if their IDs are provided
        if (!empty($deletedDeliveriesIds)) {
            $this->deleteDeliveries($deletedDeliveriesIds);
        }

        return $customer;
    }

    /**
     * @param int $companyId
     * @return array
     */
    public function getCustomerCodes(int $companyId): array
    {
        return Customer::query()
            ->where('company_id', $companyId)
            ->pluck('customer_code')
            ->toArray();
    }

    public function generateUniqueCode($companyId): int
    {
        $usedCodes = $this->getCustomerCodes($companyId);

        $minNumber = 10000;
        $maxNumber = 19999;
        $attempts = 0;

        do {
            $randomNumber = mt_rand($minNumber, $maxNumber);

            $uniqueNumber = intval($companyId . str_pad($randomNumber, strlen($maxNumber), '0', STR_PAD_LEFT));

            $attempts++;
        } while (in_array($uniqueNumber, $usedCodes) && $attempts < ($maxNumber - $minNumber + 1));

        $usedCodes[] = $uniqueNumber;

        return $uniqueNumber;
    }
}
