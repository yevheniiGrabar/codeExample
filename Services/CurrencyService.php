<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Currency;
use App\Traits\CurrentCompany;
use Illuminate\Support\Facades\Auth;
use AshAllenDesign\LaravelExchangeRates\Facades\ExchangeRate;

class CurrencyService
{
    public function getCurrencies()
    {
        $currentCompany = CurrentCompany::getDefaultCompany();
        $company = Company::query()->where('id', $currentCompany->company_id)->firstOrFail();
        $baseCurrency = $company->currency;

        // $date = Carbon::today();
        // ExchangeRate::currencies();
    }

    /**
     * @param array $data
     * @return Currency
     */
    public function createCurrency(array $data): Currency
    {
        $currency = new Currency();

        $currency->name = $data['name'];
        $currency->code = $data['code'];
        $currency->currency_rate = $data['currency_rate'];
        $currency->fixed_exchange_rate = $data['fixed_exchange_rate'] ? 1 : 0;
        $currency->symbol = $data['symbol'];

        $currency->save();

        $currency->users()->attach(Auth::id(), ['is_base_currency' => false]);

        return $currency;
    }

    /**
     * @param Currency $currency
     * @param array $data
     * @return Currency
     */
    public function updateCurrency(Currency $currency, array $data): Currency
    {
        $currency->update(
            [
                'name' => $data['name'] ?? $currency->name,
                'code' => $data['code'] ?? $currency->code,
                'currency_rate' => $data['currency_rate'] ?? $currency->currency_rate,
                'fixed_exchange_rate' => $data['fixed_exchange_rate'] ? 1 : 0,
                'symbol' => $data['symbol'] ?? $currency->symbol,
            ]
        );

        $currency->save();

        return $currency;
    }
}
