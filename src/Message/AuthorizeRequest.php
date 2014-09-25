<?php

namespace Omnipay\CybersourceSoap\Message;

use DomDocument;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Cybersource Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor';
    protected $testEndpoint = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor';

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    public function getData()
    {
        $user = $this->getParameter('merchantId');
        $password = $this->getParameter('transactionKey');

        $currency = $this->getCurrency() ? $this->getCurrency() : 'USD';

        $type = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText";
        $soapHeader = <<<END
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ns1="urn:schemas-cybersource-com:transaction-data-1.26">
    <SOAP-ENV:Header xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ns1="urn:schemas-cybersource-com:transaction-data-1.26"
    xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <wsse:Security SOAP-ENV:mustUnderstand="1">
            <wsse:UsernameToken>
                <wsse:Username>$user</wsse:Username>
                <wsse:Password Type="$type">$password</wsse:Password>
            </wsse:UsernameToken>
        </wsse:Security>
    </SOAP-ENV:Header>
<SOAP-ENV:Body/>
</SOAP-ENV:Envelope>
END;

        $data = new DomDocument('1.0');
        $data->loadXml($soapHeader);

        $node = $data->getElementsByTagNameNS('http://schemas.xmlsoap.org/soap/envelope/', 'Body');

        $request = $data->createElement('ns1:requestMessage');

        $node->item(0)->appendChild($request);

        $request->appendChild($data->createElement('ns1:merchantID', $user));
        $request->appendChild($data->createElement('ns1:merchantReferenceCode', $this->getTransactionId()));

        $billTo = $request->appendChild($data->createElement('ns1:billTo'));
        $billTo->appendChild($data->createElement('ns1:firstName', $this->getCard()->getFirstName()));
        $billTo->appendChild($data->createElement('ns1:lastName', $this->getCard()->getLastName()));
        $billTo->appendChild($data->createElement('ns1:street1', $this->getCard()->getAddress1()));
        $billTo->appendChild($data->createElement('ns1:city', $this->getCard()->getCity()));
        $billTo->appendChild($data->createElement('ns1:state', $this->getCard()->getState()));
        $billTo->appendChild($data->createElement('ns1:postalCode', $this->getCard()->getPostcode()));
        $billTo->appendChild($data->createElement('ns1:country', $this->getCard()->getCountry()));
        $billTo->appendChild($data->createElement('ns1:email', $this->getCard()->getEmail()));

        $item = $request->appendChild($data->createElement('ns1:item'));
        $item->setAttribute('id', 1);
        $item->appendChild($data->createElement('ns1:unitPrice', $this->getAmount()));
        $item->appendChild($data->createElement('ns1:quantity', 1));

        $purchaseTotals = $request->appendChild($data->createElement('ns1:purchaseTotals'));
        $purchaseTotals->appendChild($data->createElement('ns1:currency', $currency));
        $purchaseTotals->appendChild($data->createElement('ns1:grandTotalAmount', $this->getAmount()));

        $card = $request->appendChild($data->createElement('ns1:card'));
        $card->appendChild($data->createElement('ns1:accountNumber', $this->getCard()->getNumber()));
        $card->appendChild($data->createElement('ns1:expirationMonth', $this->getCard()->getExpiryDate('m')));
        $card->appendChild($data->createElement('ns1:expirationYear', $this->getCard()->getExpiryDate('Y')));
        $card->appendChild($data->createElement('ns1:cvNumber', $this->getCard()->getCvv()));

        $this->addRequestProperties($request, $data);

        $data->formatOutput = true;
        return $data->saveXml();
    }

    protected function addRequestProperties(&$request, $data)
    {
        $ccAuthService = $request->appendChild($data->createElement('ns1:ccAuthService'));
        $ccAuthService->setAttribute('run', 'true');
    }

    public function sendData($data)
    {
        $headers = array(
            'Content-Type' => 'text/xml; charset=utf-8',
        );

        $endpoint = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

        $httpResponse = $this->httpClient->post($endpoint, $headers, $data)->send();

        $dom = new DomDocument('1.0');
        $dom->loadXml($httpResponse->getBody());

        $response = array(
            'decision' => $dom->getElementsByTagName('decision')->item(0)->nodeValue,
            'reasonCode' => $dom->getElementsByTagName('reasonCode')->item(0)->nodeValue,
            'requestId' => $dom->getElementsByTagName('requestID')->item(0)->nodeValue,
            'requestToken' => $dom->getElementsByTagName('requestToken')->item(0)->nodeValue,
        );

        return $this->response = new Response($this, $response);
    }
}
