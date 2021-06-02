<?php

/**
 * Description of Payment
 *
 * @author enkhsaikhan.da
 */
class MobinetHBBPayment extends BasicMobinetGW
{

    public function setXmlRequest($contractNumber, $paymentWayId, $paid, $account) {
        
        $data = array();
        $data["contractNumber"] = $contractNumber;
        $data["paymentWayId"] = $paymentWayId;
        $data["paid"] = $paid;
        $data["bankAccountId"] = $account;
        $data["createdUser"] = "BANKGW";

        $this->xmlRequest = json_encode($data);
        $this->url .= '/api/payments';
        $this->customRequest = 'POST';
        $this->customHeader = true;
        $this->defaultResponse = true;
    }

    public function getXmlRequest()
    {
        return $this->xmlRequest;
    }

    public function setAttr() {
        $xml = parent::getXmlResponse();
    }

}

?>