<?php

class BillPayment extends BasicPostGW
{

    private $r_accessNo;
    private $r_accountNo;
    private $r_accountType;
    private $r_code;
    private $r_info;
    private $r_requestId;

    public function BillPayment()
    {
        $this->setUrl();
        if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
            // die('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
        }
    }

    public function setXmlRequest($number = 0, $contract = 0, $amount = 0, $paidDate = 0, $payment_code = "")
    {
        $attr1 = '';
        if ($contract != 0) {
            $attr1 = "<AccountNo>$contract</AccountNo>";
        }

        if (is_numeric($number) && $number != 0) {
            $attr1 .= "<AccessNo>$number</AccessNo>";
        }

        $xml_request = '<?xml version="1.0" encoding="UTF-8"?>
                <Payment>
                  ' . $attr1 . '
                  <Amount>' . $amount . '</Amount>
                  <PaidDate>' . $paidDate . '</PaidDate>
                  <PaymentCode>' . $payment_code . '</PaymentCode>
                </Payment>';
        $this->number = $number;
        parent::setNumber($number);
        $this->xml_request = $xml_request;
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
        $result['PaymentId'] = (string) $this->xml_response->PaymentId[0];

        return $result;
    }

    public function getClassName()
    {
        return "Payment";
    }

}

?>