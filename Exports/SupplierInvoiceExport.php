<?php

namespace App\Exports;

use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class SupplierInvoiceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SupplierInvoice::all();
    }

    public function actions(Request $request)
    {
        return [
            (new SupplierInvoiceExport)->withHeadings(),
        ];
    }

    private function withHeadings()
    {
    }
}
