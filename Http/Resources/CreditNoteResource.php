<?php

namespace App\Http\Resources;

use App\Models\CreditNote;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed $customer
 * @property mixed $currency
 */
class CreditNoteResource extends JsonResource
{
    /** @var string|CreditNote */
    public $resource = CreditNote::class;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'customer' => new CustomerResource($this->customer),
            'date' => $this->resource->date,
            'currency' => new CurrencyResource($this->currency),
            'exchange_rate' => $this->resource->exchange_rate,
            'amount' => $this->resource->amount,
            'remaining_credit' => $this->resource->remaining_credit,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at
        ];
    }
}
