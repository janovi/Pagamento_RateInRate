<?php
namespace app\client;
use app\client\AxerveError;
use app\models\OrderToken;
use app\models\OrderDetail;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class axerveClient
{

    private const PRODUCTION_URI = 'https://ecomms2s.sella.it/api/v1';
    private const SANDBOX_URI = 'https://sandbox.gestpay.net/api/v1';

    private string $apiKey;
    private string $shopLogin;
    private bool $sandbox;
   private  static $httpClient;

    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    private function getHttpClient(): GuzzleClient
    {
        if (!self::$httpClient) {
            self::$httpClient = new GuzzleClient();
        }
        return self::$httpClient;
    }

    public function __construct(string $apiKey, string $shopLogin, bool $sandbox = false)
    {
        $this->apiKey = $apiKey;
        $this->shopLogin = $shopLogin;
        $this->sandbox = $sandbox;
    }

    public function createOrder(OrderDetail $orderDetail): OrderToken
    {
        try {
            $body = [
                'shopLogin' => $this->shopLogin,
                'currency' => $orderDetail->getCurrency(),
                 'amount' => $orderDetail->getAmount(),
                'languageId' => "1",
                // 'paymentType' => array('CONSEL'),

                'shopTransactionID' => $orderDetail->getReference(),

            ];

            //$body = array_merge($body, (array)$orderDetail);

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'apikey ' . $this->apiKey,
                'Accept' => 'application/json',
            ];

            $url = $this->sandbox === true ? self::SANDBOX_URI : self::PRODUCTION_URI;

            // Utilizza il tuo client Guzzle per eseguire la richiesta
            $response = $this->getHttpClient()->post($url . '/payment/create/', [
                'headers' => $headers,
                'body' => json_encode($body),
            ]);

            $res = json_decode($response->getBody()->getContents(), true);

            if (intval($res['error']['code']) !== 0) {
                throw new AxerveError($res['error']);
            }

            $userRedirectHref = isset($res['payload']['userRedirect']['href']) ? $res['payload']['userRedirect']['href'] : null;

            $token = new OrderToken($res['payload']['paymentToken'], $res['payload']['paymentID'], $userRedirectHref);

            return $token;
        } catch (RequestException $err) {
            // Gestisci le eccezioni di richiesta di Guzzle
            $response = $err->getResponse();

            if ($response && $response->getBody()) {
                $res = json_decode($response->getBody()->getContents(), true);

                if (isset($res['error'])) {
                    throw new AxerveError($res['error']);
                }
            }

            throw $err;
        } catch (\Exception $err) {
            throw $err;
        }
    }
}