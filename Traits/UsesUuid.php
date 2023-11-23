<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UsesUuid
{
    public static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (!$model->getKey() && !$model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = Str::uuid();
            }
        });
    }

    /**
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
