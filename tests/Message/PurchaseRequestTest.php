<?php

namespace Omnipay\CybersourceSoap\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends AuthorizeRequestTest
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getProperties());
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertContains('<ns1:ccAuthService run="true"/>', $data);
        $this->assertContains('<ns1:ccCaptureService run="true"/>', $data);
    }
}

