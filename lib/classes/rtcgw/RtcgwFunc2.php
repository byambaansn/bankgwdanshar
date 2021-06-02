<?php

class RtcgwFunc2 extends Basic_Rtcgw
{

    public function StgwFunc2()
    {
        $this->setUrl();
    }

    public function setXmlRequest($number = 0)
    {
        $xmlRequest = "<Func2> 
                            <Phone>976$number</Phone> 
                        </Func2>";
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