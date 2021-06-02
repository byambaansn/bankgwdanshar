<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SapcGateway
 *
 * @author Belbayar
 */
class WifiOnlineGateway
{

    /**
     * Wifi Online  цэнэглэлт
     * @param String $number утасны дугаар
     * @param String $card profile code
     * @return array Цэнэглэлт орсон тухай
     */
    public static function chargeCard($number, $card, $userId = 0)
    {
        if (!$number || !$card) {
            return null;
        }

        $caller = new CreateCard();
        $caller->setUserId($userId);
        $caller->setXmlRequest($number, "BANKGW", 'False', $card);
        $caller->call();
        $caller->setAttr();

        $xmlStr = $caller->getXmlResponseRaw();

//        $xmlObject = simplexml_load_string($xmlStr);
        $result = array();
        $response = explode("-", $xmlStr);
        $result['Code'] = (int) trim($response[0]);
        $result['Info'] = trim($response[1]);
        return $result;
    }

}
