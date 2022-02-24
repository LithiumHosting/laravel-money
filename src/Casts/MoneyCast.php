<?php

namespace Cknow\Money\Casts;

use Cknow\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

abstract class MoneyCast implements CastsAttributes
{
    /**
     * The currency code or the model attribute holding the currency code.
     *
     * @var string|null
     */
    protected $amount;

    /**
     * The currency code or the model attribute holding the currency code.
     *
     * @var string|null
     */
    protected $currency;

    /**
     * Instantiate the class.
     *
     * @param  string|null  $currency
     */
    public function __construct(string $amount = null, string $currency = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Get formatter.
     *
     * @param  \Cknow\Money\Money  $money
     * @return mixed
     */
    abstract protected function getFormatter(Money $money);

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Cknow\Money\Money|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return Money::parse($attributes[$this->amount], $this->getCurrency($attributes));
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // $value is instance of \Money\Money

            return [
                $this->amount => (int) $value->getAmount(),
                $this->currency => (string) $value->getCurrency()
            ];
    }

    /**
     * Get currency.
     *
     * @param  array  $attributes
     * @return \Money\Currency
     */
    protected function getCurrency(array $attributes)
    {
        $defaultCode = Money::getDefaultCurrency();

        if ($this->currency === null) {
            return Money::parseCurrency($defaultCode);
        }

        $currency = Money::parseCurrency($this->currency);
        $currencies = Money::getCurrencies();

        if ($currencies->contains($currency)) {
            return $currency;
        }

        $code = $attributes[$this->currency] ?? $defaultCode;

        return Money::parseCurrency($code);
    }
}
