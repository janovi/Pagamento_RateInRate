<?php
use PHPUnit\Framework\TestCase;
use app\client\axerveClient;
use app\client\AxerveError;
use app\models\OrderToken;
use app\models\OrderDetail;
use Http\Mock\Client as MockHttpClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AxerveClientTest extends TestCase
{

    private axerveClient $client;

    public function setUp(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')->willReturn('{"error":{"code":"0","description":"request correctly processed"},"payload":{"paymentToken":"1c3f27af-1997-4761-8673-b94fbe508f31","paymentID":"1081814508","userRedirect":{"href":null}}}');
        $mockResponse->method('getBody')->willReturn($mockStream);

        $mockHttpClient = new MockHttpClient();
        $mockHttpClient->addResponse($mockResponse);

        $this->client = new axerveClient('R0VTUEFZOTYxNTYjI0VzZXJjZW50ZSBUZXN0IGRpIHRlY25pY2EjIzI0LzAxLzIwMjQgMTY6MTc6MTA=', 'GESPAY96156', true);
        $this->client->setHttpClient($mockHttpClient);
    }

    public function testCreateOrder(): void
    {
        $order = new OrderDetail('27".30', 'EUR', 'Test');
        $tokenResponse = $this->client->createOrder($order);

        $this->assertInstanceOf(OrderToken::class, $tokenResponse);
        $this->assertIsString($tokenResponse->getToken());
        $this->assertIsString($tokenResponse->getPaymentId());

       // $this->assertEquals('1c3f27af-1997-4761-8673-b94fbe508f31', $tokenResponse->getToken());
        $this->assertEquals('1081814508', $tokenResponse->getPaymentId());
        $this->assertNull($tokenResponse->getRedirectUrl());
    }
}