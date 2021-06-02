<?php

//Request: 2.3
class MobinetPayment extends Mobinet
{

    public function MobinetPayment()
    {
        $this->Basic();
        $this->setUrl();
    }

    public function setXml($requestId, $contract, $bundle, $speed, $month, $paid, $fullName, $address, $staff)
    {
        $xml_request = "<paymentRequest>
                            <requestId>$requestId</requestId>
                            <contract>$contract</contract>
                            <bundle>$bundle</bundle>
                            <speed>$speed</speed>
                            <month>$month</month>
                            <paid>$paid</paid>
                            <orderId></orderId>
                            <fullName>$fullName</fullName>
                            <address>$address</address>
                            <staff>$staff</staff>
                         </paymentRequest>
                            ";
        $this->number = $contract;
        parent::setNumber($contract);
        parent::setXmlRequest($xml_request);
    }

    public function setUrl()
    {
        $this->url = parent::getUrl() . '/mobinet-task/tedy/payment';
    }

    public function getClassName()
    {
        return "MobinetPayment";
    }

}

?>