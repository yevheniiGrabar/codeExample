<?php /** @noinspection ALL */

namespace App\Http\Resources;

use App\Models\BillingAddress;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class BillingResource extends JsonResource
{
    /** @var BillingAddress|string */
    public $resource = BillingAddress::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'street' => $this->resource->street,
            'postal' => $this->resource->postal,
            'city' => $this->resource->city,
            'country' => $this->resource->country,
        ];
    }
}
