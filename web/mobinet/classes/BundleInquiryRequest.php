<?php

//Request: 2.1
class BundleInquiryRequest extends Mobinet
{

    public function BundleInquiryRequest()
    {
        $this->Basic();
        $this->setUrl();
    }

    public function setXml($requestId, $contract, $staff)
    {
        $xml_request = "<bundleInquiryRequest>
                            <requestId>$requestId</requestId>
                            <contract>" . $contract . "</contract>
                            <staff>$staff</staff> 
                        </bundleInquiryRequest>";
        $this->number = $contract;
        parent::setNumber($contract);
        parent::setXmlRequest($xml_request);
    }

    public function setUrl()
    {
        $this->url = parent::getUrl() . '/mobinet-task/tedy/bundleinquiry';
    }

    public function getClassName()
    {
        return "BundleInquiryRequest";
    }

}

?>