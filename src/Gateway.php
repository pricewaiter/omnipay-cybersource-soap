<?php

namespace Omnipay\CybersourceSoap;

use Omnipay\Common\AbstractGateway;
use Omnipay\CybersourceSoap\Message\AuthorizeRequest;

/**
 * CyberSource SOAP Gateway
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Cybersource Soap';
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\CybersourceSoap\Message\AuthorizeRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\CybersourceSoap\Message\PurchaseRequest', $parameters);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }
}
