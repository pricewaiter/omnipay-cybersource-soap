<?php

namespace Omnipay\CybersourceSoap;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('merchant_b');
        $this->gateway->setTransactionKey('trans_key');

        $this->options = array(
            'amount' => '10.00',
            'card' => $this->getValidCard(),
            'transactionId' => 'abc123',
        );
        $this->options['card']['email'] = 'buyer@example.com';
    }

    public function testProperties()
    {
        $this->assertEquals('merchant_b', $this->gateway->getMerchantId());
        $this->assertEquals('trans_key', $this->gateway->getTransactionKey());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseResponseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('4116875965340176195995', $response->getTransactionReference());
    }

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AuthorizeResponseSuccess.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('4116875978770176195842', $response->getTransactionReference());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseResponseFailure.txt');

        $options = $this->options;
        $options['card']['number'] = '4111111111111112';

        $response = $this->gateway->purchase($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        // $this->assertEquals('4116875965340176195995', $response->getTransactionReference());
    }

    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('AuthorizeResponseFailure.txt');

        $options = $this->options;
        $options['card']['number'] = '4111111111111112';

        $response = $this->gateway->authorize($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        // $this->assertEquals('4116875978770176195842', $response->getTransactionReference());
    }
}
