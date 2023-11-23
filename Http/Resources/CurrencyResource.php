<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use JsonSerializable;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed users
 */
class CurrencyResource extends JsonResource
{
    /** @var Currency|string */
    public $resource = Currency::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        $user = Auth::user();

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'code' => $this->resource->code,
            'symbol' => $this->resource->symbol,
            'currency_rate' => $this->resource->currency_rate,
            'fixed_exchange_rate' => $this->resource->fixed_exchange_rate,
        ];
    }
}
