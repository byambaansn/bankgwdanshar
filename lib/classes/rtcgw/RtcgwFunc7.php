<?php

class RtcgwFunc7 extends Basic_Rtcgw
{

    const sourcePhone = 6;

    public function __construct()
    {
        parent::__construct();
    }

    public function setXmlRequest($number, $cardId, $sourcePhone = 0)
    {
        $xmlRequest = '<Func7>
                        <PHONE2>976' . $number . '</PHONE2>
                        <PHONE1>' . $sourcePhone . '</PHONE1>
                        <PID>' . $cardId . '</PID>
                       </Func7>';
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
