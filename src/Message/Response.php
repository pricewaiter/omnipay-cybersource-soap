<?php

namespace Omnipay\CybersourceSoap\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Cybersource Response
 */
class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['decision']) && $this->data['decision'] === 'ACCEPT';
    }

    public function getTransactionReference()
    {
        return isset($this->data['requestId']) ? $this->data['requestId'] : null;
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return 'Success';
        }

        return 'Failure';
    }
}
