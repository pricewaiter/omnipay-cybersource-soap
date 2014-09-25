<?php

namespace Omnipay\CybersourceSoap\Message;

use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getProperties());
    }

    protected function getProperties()
    {
        return array(
            'amount' => '10.00',
            'card' => $this->getValidCard(),
            'transactionId' => 'abc123',
            'merchantId' => 'merchant_a',
            'transactionKey' => 'tx_key',
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertContains('<ns1:merchantID>merchant_a</ns1:merchantID>', $data);
        $this->assertContains('<ns1:merchantReferenceCode>abc123</ns1:merchantReferenceCode>', $data);
        $this->assertContains('<ns1:currency>USD</ns1:currency>', $data);
        $this->assertContains('<ns1:grandTotalAmount>10.00</ns1:grandTotalAmount>', $data);
        $this->assertContains('<ns1:accountNumber>4111111111111111</ns1:accountNumber>', $data);
        $this->assertContains('<ns1:ccAuthService run="true"/>', $data);

        $this->assertNotContains('<ns1:ccCaptureService run="true"/>', $data);
    }
}
