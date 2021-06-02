<?php

class AccountInfo extends BasicPostGW
{

    private $r_accountNo;
    private $r_accountType;
    private $r_code;
    private $r_info;
    private $r_requestId;

    public function AccountInfo()
    {
        $this->setUrl();
    }

    public function setXmlRequest($account, $option = "")
    {
        $xml_request = "<AccountInfoByAccount> 
                            <AccountNo>$account</AccountNo> 
                        </AccountInfoByAccount>";
        $this->number = $account;
        $this->xml_request = $xml_request;
        parent::setXmlRequest($xml_request);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

    public function call($xml = "")
    {
        parent::call(parent::getXmlRequest());
    }

    public function getR_accountNo()
    {
        return $this->r_accountNo;
    }

    public function getResponse()
    {
        $result = array();
        $result['Code'] = (string) $this->xml_response->Code[0];
        $result['Info'] = (string) $this->xml_response->Info[0];
        $result['AccountName'] = (string) $this->xml_response->AccountName[0];
        $result['AccountNo'] = (string) $this->xml_response->AccountNo[0];

        return $result;
    }

    public function getClassName()
    {
        return "AccountInfo";
    }

}

?>