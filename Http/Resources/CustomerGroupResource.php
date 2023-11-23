<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\CustomerGroup;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerGroupResource extends JsonResource
{

    /** @var string|CustomerGroup */
    public $resource = CustomerGroup::class;

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->resource->id,
            'number' => $this->resource->number,
            'name' => $this->resource->name,
            'discount' => $this->resource->discount,
            'update_discount' => $this->resource->update_discount,
        ];
    }
}
