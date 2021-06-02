<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VatSenderReturnBill
 *
 * @author khishigdelger.b
 */
class VatSenderReturnBill extends Basic_VatSender 
{
    public function setXmlRequest($isdn, $billId, $date, $description, $amount, $lottery) {
        $detail = array();
        $detail['amount']=$amount;
        $detail['lottery']=$lottery;
        
        $data = array();
        $data["isdn"] = $isdn;
        $data["returnBillId"] = $billId;
        $data["date"] = $date;
        $data["description"] = $description;
        $data["channel"] = $this->appName;
        $data["sendmail"] = true;
        $data["sendsms"] = true;
        $data["billDetailList"] = $detail;

        $this->xmlRequest = json_encode($data);
        $this->url .= '/rest/return';
        $this->customRequest = 'POST';
        $this->customHeader = true;
        $this->defaultResponse = true;
        $this->number = $isdn;
        parent::setNumber($this->number);
    }

    public function setAttr() {
        $xml = parent::getXmlResponse();
    }
}
