<?php

class BillInfo extends BasicPostGW
{

    private $r_accessNo;
    private $r_accountNo;
    private $r_accountType;
    private $r_code;
    private $r_info;
    private $r_requestId;

    public function BillInfo()
    {
        $this->setUrl();
    }

    public function setXmlRequest($number = 0, $account = 0, $dater = 0)
    {
        if ($account != 0) {
            $attr1 = "<AccountNo>$account</AccountNo>";
        } else {
            $attr1 = "<AccessNo>$number</AccessNo>";
        }
        if ($dater != 0) {
            $attr1 .= "<BillDt>$dater</BillDt>";
        }

        $xml_request = '<?xml version="1.0" encoding="UTF-8"?>
                <BillInfo>
                  ' . $attr1 . '
                </BillInfo>';

        $this->number = $number;
        parent::setNumber($number);
        parent::setXmlRequest($xml_request);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

    public function call($xml = 0)
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
        $result['AccessNo'] = (string) $this->xml_response->AccessNo[0];
        $result['AccountNo'] = (string) $this->xml_response->AccountNo[0];
        $result['BillCycleCode'] = (string) $this->xml_response->BillCycleCode[0];
        $result['CurrentBalance'] = (string) $this->xml_response->CurrentBalance[0];
        return $result;
    }

    public function getClassName()
    {
        return "BillInfo";
    }

}

?>