<?php

class MxCharge extends Basic_Mx
{

    const SOURCE_NUMBER = '95300135';
    const SOURCE_NUMBER_PIN = '1065';

    public function MxCharge()
    {
        $url = parent::getUrl() . '/ProductService-web/rest/service';
        parent::setUrl($url);
    }

    public function setXml($inNumber, $amount, $desc)
    {
        $xml_request = "<Request>
                            <Service>MX.MMCHARGE.STATEBANKTELLERTOMX</Service>
                            <Principal>99000000</Principal>
                            <DestPrincipal>$inNumber</DestPrincipal>
                            <DestPocketType>DE</DestPocketType>
                            <Amount>$amount</Amount>
                            <Description>$desc</Description>
                          </Request>";
        parent::setXmlRequest($xml_request);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

    public function getClassName()
    {
        return "MxCharge";
    }

}

?>