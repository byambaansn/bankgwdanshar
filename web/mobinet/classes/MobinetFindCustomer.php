<?php

class MobinetFindCustomer extends Mobinet
{

    public function MobinetFindCustomer()
    {
        $this->setUrl();
    }

    public function setXml($requestId, $param, $staff)
    {
        $xml_request = "<findCustomerRequest>
                            <requesId>$requestId</requesId>
                            <param>$param</param>
                            <staff>$staff</staff>
                        </findCustomerRequest>";
        $this->number = $param;
        parent::setNumber($param);
        parent::setXmlRequest($xml_request);
    }

    public function setUrl()
    {
        $this->url = parent::getUrl() . '/mobinet-task/tedy/findcustomer';
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

    public function getR_accountNo()
    {
        return $this->r_accountNo;
    }

    public function getClassName()
    {
        return "MobinetFindCustomer";
    }

}

?>