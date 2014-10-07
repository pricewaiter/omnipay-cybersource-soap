<?php

namespace Omnipay\CybersourceSoap\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = new Response(
            $this->getMockRequest(),
            array(
                'requestId' => 'abc123',
                'decision' => 'ACCEPT',
            )
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('abc123', $response->getTransactionReference());
        $this->assertSame('Success', $response->getMessage());
    }

    public function testFailure()
    {
        $response = new Response(
            $this->getMockRequest(),
            array(
                'requestId' => 'abc123',
                'decision' => 'REJECT',
                'reasonCode' => '232',
            )
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('abc123', $response->getTransactionReference());
        $this->assertSame('232', $response->getCode());
        $this->assertSame('The card type is not accepted by the payment processor', $response->getMessage());
    }


    public function testUnknownFailure()
    {
        $response = new Response(
            $this->getMockRequest(),
            array(
                'requestId' => 'abc123',
                'decision' => 'REJECT',
                'reasonCode' => '666',
            )
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('abc123', $response->getTransactionReference());
        $this->assertSame('666', $response->getCode());
        $this->assertSame('Failure', $response->getMessage());
    }
}
