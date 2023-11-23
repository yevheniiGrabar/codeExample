<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CurrentCompany
{
    /**
     * @return mixed
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function getDefaultCompany(): mixed
    {
        try{
            return Auth::user()->companies()
                ->newPivotStatement()
                ->where('is_default', true)
                ->where('user_id', Auth::id())
                ->first();
        }catch(\Throwable $e) {
            return null;
        }
    }
}
