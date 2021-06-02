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
class VatReturnBill
{
    public static function returnBill($isdn, $billId, $date, $description, $amount, $lottery)
    {
        $caller = new VatSenderReturnBill();
        $caller->setXmlRequest($isdn, $billId, $date, $description, $amount, $lottery);
        $caller->call();
        $caller->setAttr();
        $result = $caller->getXmlResponse();
        
        return json_decode($result, TRUE);
    }
}
