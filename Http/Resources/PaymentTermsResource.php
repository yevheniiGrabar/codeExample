<?php

namespace App\Http\Resources;

use App\Models\PaymentTerms;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed language
 */
class PaymentTermsResource extends JsonResource
{
    /**
     * @var string|PaymentTerms
     */
    public $resource = PaymentTerms::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'type' => $this->resource->type,
            'days' => $this->resource->days,
        ];
    }
}
