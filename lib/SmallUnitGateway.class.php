<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SmallUnitGateway
 *
 * @author enkhsaikhan.da
 */
class SmallUnitGateway
{

    /**
     * Small unit charge цэнэглэлт
     * @param String $number утасны дугаар
     * @param Integer $amount төлөх дүн
     * @param integer $userId
     * @param $type
     * @return array Цэнэгэлэлт орсон тухай
     */
    public static function chargeUnit($number, $amount, $userId = 0, $type)
    {
        if (!$number || !$amount || !$type) {
            return null;
        }
        $caller = new SmallUnit();
        $caller->setUserId($userId);
        $caller->setXmlRequest($number, floor($amount), $type);
        $caller->call();
        $caller->setAttr();

        $xmlStr = $caller->getXmlResponseRaw();
        $xmlObject = simplexml_load_string($xmlStr);
        if (!isset($xmlObject->Code[0])) {
            return null;
        }

        $result = array();
        $result['Code'] = (int) $xmlObject->Code[0];
        $result['Info'] = (string) $xmlObject->returnmsg[0];
        return $result;
    }

}
