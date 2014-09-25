<?php

namespace Omnipay\CybersourceSoap\Message;

use DomDocument;

/**
 * Cybersource Authorize Request
 */
class PurchaseRequest extends AuthorizeRequest
{
    protected function addRequestProperties(&$request, $data)
    {
        $ccAuthService = $request->appendChild($data->createElement('ns1:ccAuthService'));
        $ccAuthService->setAttribute('run', 'true');
        $ccCaptureService = $request->appendChild($data->createElement('ns1:ccCaptureService'));
        $ccCaptureService->setAttribute('run', 'true');
    }
}
