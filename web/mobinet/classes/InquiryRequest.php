<?php

//Request: 2.2
class InquiryRequest extends Mobinet
{

    public function InquiryRequest()
    {
        $this->Basic();
        $this->setUrl();
    }

    public function setXml($requestId, $contract, $bundle, $speed, $month, $staff)
    {
        $bundleText = "";
        $speedText = "";
        $monthText = "";
        if ($bundle != "") {
            $bundleText = '<bundle>' . $bundle . '</bundle>';
        }
        if ($speed != "") {
            $speedText = '<speed>' . $speed . '</speed>';
        }
        if ($month != "") {
            $monthText = '<month>' . $month . '</month>';
        }

        $xml_request = "<paymentInquiryRequest>
                            <contract>" . $contract . "</contract>
                            <requestId>$requestId</requestId>
                            <staff>$staff</staff> 
                            " . $bundleText . $speedText . $monthText . "
                        </paymentInquiryRequest>";
        $this->number = $contract;
        parent::setNumber($contract);
        parent::setXmlRequest($xml_request);
    }

    public function setUrl()
    {
        $this->url = parent::getUrl() . '/mobinet-task/tedy/paymentinquiry';
    }

    public function getClassName()
    {
        return "InquiryRequest";
    }

}

?>