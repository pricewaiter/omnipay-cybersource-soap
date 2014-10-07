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

    public function getCode()
    {
        return isset($this->data['reasonCode']) ? $this->data['reasonCode'] : null;
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return 'Success';
        }

        return $this->generateErrorMessage($this->getCode());
    }

    private function generateErrorMessage($code)
    {
        $responseCodes = array(
            '100' => "Successful transaction",
            '101' => "Request is missing one or more required fields" ,
            '102' => "One or more fields contains invalid data",
            '150' => "General failure",
            '151' => "The request was received but a server time-out occurred",
            '152' => "The request was received, but a service timed out",
            '200' => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the AVS check",
            '201' => "The issuing bank has questions about the request",
            '202' => "Expired card",
            '203' => "General decline of the card",
            '204' => "Insufficient funds in the account",
            '205' => "Stolen or lost card",
            '207' => "Issuing bank unavailable",
            '208' => "Inactive card or card not authorized for card-not-present transactions",
            '209' => "American Express Card Identifiction Digits (CID) did not match",
            '210' => "The card has reached the credit limit",
            '211' => "Invalid card verification number",
            '221' => "The customer matched an entry on the processor's negative file",
            '230' => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the card verification check",
            '231' => "Invalid account number",
            '232' => "The card type is not accepted by the payment processor",
            '233' => "General decline by the processor",
            '234' => "A problem exists with your CyberSource merchant configuration",
            '235' => "The requested amount exceeds the originally authorized amount",
            '236' => "Processor failure",
            '237' => "The authorization has already been reversed",
            '238' => "The authorization has already been captured",
            '239' => "The requested transaction amount must match the previous transaction amount",
            '240' => "The card type sent is invalid or does not correlate with the credit card number",
            '241' => "The request ID is invalid",
            '242' => "You requested a capture, but there is no corresponding, unused authorization record.",
            '243' => "The transaction has already been settled or reversed",
            '244' => "The bank account number failed the validation check",
            '246' => "The capture or credit is not voidable because the capture or credit information has already been submitted to your processor",
            '247' => "You requested a credit for a capture that was previously voided",
            '250' => "The request was received, but a time-out occurred with the payment processor",
            '254' => "Your CyberSource account is prohibited from processing stand-alone refunds",
            '255' => "Your CyberSource account is not configured to process the service in the country you specified"
        );

        if (isset($responseCodes[$code])) {
            return $responseCodes[$code];
        }

        return 'Failure';
    }
}
