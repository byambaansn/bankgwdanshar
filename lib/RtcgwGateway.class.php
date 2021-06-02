<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RtcgwGateway
 *
 * @author Belbayar
 */
class RtcgwGateway
{

    /**
     * Topup цэнэглэлт
     * @param String $number утасны дугаар
     * @param String $card profile code
     * @param integer $userId төлөх дүн
     * @param integer $chargerId төлөлт хийж буй огноо
     * @return array Цэнэгэлэлт орсон тухай
     */
    public static function chargeTopup($number, $card, $userId = 0)
    {
        if (!$number || !$card) {
            return null;
        }
        $caller = new RtcgwFunc7();
        $caller->setUserId($userId);
        $caller->setXmlRequest($number, $card, $userId);
        $caller->call();
        $caller->setAttr();

        $xmlStr = $caller->getXmlResponseRaw();

        $xmlObject = simplexml_load_string($xmlStr);

        if (!isset($xmlObject->CODE[0])) {
            return null;
        }

        $result = array();
        $result['Code'] = (int) $xmlObject->CODE[0];
        $result['Info'] = (string) $xmlObject->returnmsg[0];

        return $result;
    }

    /**
     * CheckTopup
     * @param String $number утасны дугаар
     * @param String $card  profile code
     * @return array дугаарын мэдээлэл ирнэ
     */
    public static function checkTopup($number, $userId = 0)
    {
        if (!$number) {
            return null;
        }
        $caller = new StgwFunc2();
        $caller->setUserId($userId);
        $caller->setXmlRequest($number);
        $caller->call();
        $caller->setAttr();
        $arrRes = AppTools::xmlToArray($caller->getXmlResponseRaw());

        return $arrRes;
    }

}
