<?php

namespace app\models;

class OrderDetail
{
    private string $amount;
    private string $currency;
    private string $shopTransactionID;

    /**
     * The order detail to pass at scalpay
     *
     * @param string $amount - The amount to capture
     * @param string $currency - ISO currency code
     * @param string $reference - The shop transaction ID
     */
    public function __construct(string $amount, string $currency, string $reference)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->shopTransactionID = $reference;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getReference(): string
    {
        return $this->shopTransactionID;
    }
}