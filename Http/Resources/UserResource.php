<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed companies
 */
class UserResource extends JsonResource
{
    /** @var User|string */
    public $resource = User::class;

    /**
     * Keeps track of the current mode.
     * @var string
     */
    public static $mode = 'collection';

    /**
     * Set the current mode for this resource.
     * @param $mode
     * @return string
     */
    public static function setMode($mode): string
    {
        self::$mode = $mode;
        return __CLASS__;
    }

    /**
     * @param null $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request = null): array|JsonSerializable|Arrayable
    {
        $companies = $this->resource->companies()->first();

        if (self::$mode === 'single') {
            return [
                'id' => $this->resource->id,
                'first_name' => $this->resource->name,
                'last_name' => $this->resource->last_name,
                'email' => $this->resource->email,
                'selected_currency' => $this->whenLoaded('currencies', CurrencyResource::collection($this->currencies)),
            ];
        } else {
            return [
                'id' => $this->resource->id,
                'first_name' => $this->resource->name,
                'last_name' => $this->resource->last_name,
                'email' => $this->resource->email,
                'selected_currency' => $this->whenLoaded('currencies', CurrencyResource::collection($this->currencies)),
                'default_company' => [
                    'id' => $companies->id,
                    'company_name' => $companies->company_name,
                ],
            ];
        }
    }
}
