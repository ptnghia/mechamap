<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format currency amount
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    public static function format($amount, $currency = null)
    {
        $currency = $currency ?: config('currency.default', 'USD');
        $currencyConfig = config("currency.supported.{$currency}");
        
        if (!$currencyConfig) {
            // Fallback to USD if currency not found
            $currency = 'USD';
            $currencyConfig = config("currency.supported.USD");
        }

        $decimals = $currencyConfig['decimals'] ?? 2;
        $symbol = $currencyConfig['symbol'] ?? '$';
        $format = $currencyConfig['format'] ?? 'symbol_before';

        // Format the number
        $formattedAmount = number_format(
            $amount,
            $decimals,
            config('currency.display.decimal_separator', '.'),
            config('currency.display.thousands_separator', ',')
        );

        // Apply symbol position
        if ($format === 'symbol_after') {
            return $formattedAmount . $symbol;
        } else {
            return $symbol . $formattedAmount;
        }
    }

    /**
     * Format currency with code
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    public static function formatWithCode($amount, $currency = null)
    {
        $currency = $currency ?: config('currency.default', 'USD');
        $formatted = self::format($amount, $currency);
        
        if (config('currency.display.show_code', false)) {
            return $formatted . ' ' . $currency;
        }
        
        return $formatted;
    }

    /**
     * Get currency symbol
     *
     * @param string|null $currency
     * @return string
     */
    public static function getSymbol($currency = null)
    {
        $currency = $currency ?: config('currency.default', 'USD');
        return config("currency.supported.{$currency}.symbol", '$');
    }

    /**
     * Get currency name
     *
     * @param string|null $currency
     * @return string
     */
    public static function getName($currency = null)
    {
        $currency = $currency ?: config('currency.default', 'USD');
        return config("currency.supported.{$currency}.name", 'US Dollar');
    }

    /**
     * Get all supported currencies
     *
     * @return array
     */
    public static function getSupportedCurrencies()
    {
        return config('currency.supported', []);
    }

    /**
     * Convert amount between currencies
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        $fromRate = config("currency.exchange_rates.{$fromCurrency}", 1.0);
        $toRate = config("currency.exchange_rates.{$toCurrency}", 1.0);
        
        // Convert to USD first, then to target currency
        $usdAmount = $amount / $fromRate;
        return $usdAmount * $toRate;
    }

    /**
     * Format amount for display in checkout
     *
     * @param float $amount
     * @param string|null $currency
     * @return string
     */
    public static function formatForCheckout($amount, $currency = null)
    {
        return self::format($amount, $currency);
    }
}
