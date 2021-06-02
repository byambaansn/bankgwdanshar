<?php

class SmallUnit extends Basic_SmallUnit
{

    const sourcePhone = 6;

    public function SmallUnit()
    {
        $this->setUrl();
    }

    public function setXmlRequest($number, $amount, $type)
    {
//        $chargerId = BaseSms::getChargeIdByVendor($type);
        $xmlRequest = "/?isdn=$number&amount=$amount&system=$type&chargetype=none&sendsms=yes&description=$type";
        $this->number = $number;
        $this->xml_request = $xmlRequest;
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

    public function call($xml = "")
    {
        parent::call(parent::getXmlRequest());
    }

}

?>