<?php

namespace app\models;

class OrderToken
{
    private string $token;
    private string $paymentId;
    private ?string $redirectUrl;

    /**
     * Order token object
     *
     * @param string $token - The token
     * @param string $paymentId - The payment ID
     * @param string|null $redirectUrl - The redirect URL (nullable)
     */
    public function __construct(string $token, string $paymentId, ?string $redirectUrl) {
        $this->token = $token;
        $this->paymentId = $paymentId;
        $this->redirectUrl = $redirectUrl;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function getPaymentId(): string {
        return $this->paymentId;
    }

    public function getRedirectUrl(): ?string {
        return $this->redirectUrl;
    }
}