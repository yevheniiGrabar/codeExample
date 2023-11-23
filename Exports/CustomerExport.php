<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithMapping, WithHeadings
{
    protected array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Customer::all();
    }

    public function map($row): array
    {
        $data = [];

        if (empty($this->columns)) {
            $data = [
                $row->customerContacts?->customer_name,
                $row->customerContacts?->contact_email,
                $row->customerContacts?->contact_phone,
                $row->billingAddress?->country,
                $row->shippingAddress?->country,
                $row->vat_number,
                $row->contact_name,
                $row->billingAddress?->street,
                $row->shippingAddress?->street,
                $row->customerGroup?->name,
            ];
        } else {
            if (in_array('company_name', $this->columns) && $row->customerContacts) {
                $data[] = $row->customerContacts->customer_name;
            }

            if (in_array('email', $this->columns) && $row->customerContacts) {
                $data[] = $row->customerContacts->contact_email;
            }

            if (in_array('phone', $this->columns) && $row->customerContacts) {
                $data[] = $row->customerContacts->contact_phone;
            }

            if (in_array('country', $this->columns) && ($row->billingAddress || $row->shippingAddress)) {
                $data[] = $row->billingAddress->country;
                $data[] = $row->shippingAddress->country;
            }

            if (in_array('vat', $this->columns)) {
                $data[] = $row->vat_number;
            }

            if (in_array('contact_person', $this->columns)) {
                $data[] = $row->contact_name;
            }

            if (in_array('address', $this->columns) && ($row->billingAddress || $row->shippingAddress)) {
                $data[] = $row->billingAddress->street;
                $data[] = $row->shippingAddress->street;
            }

            if (in_array('group', $this->columns) && $row->customerGroup) {
                $data[] = $row->customerGroup->name;
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Company name',
            'Email',
            'Phone',
            'Billing Country',
            'Shipping Country',
            'VAT No.',
            'Contact person',
            'Billing Address',
            'Shipping Address',
            'Group',
        ];

        if (!empty($this->columns)) {
            $filtered_headings = [];
            foreach ($headings as $heading) {
                if (in_array(strtolower(str_replace(' ', '_', $heading)), $this->columns)) {
                    $filtered_headings[] = $heading;
                } else {
                    $filtered_headings[] = '';
                }
            }
            return $filtered_headings;
        }

        return $headings;
    }
}
