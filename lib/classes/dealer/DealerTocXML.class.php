<?php

class DealerTocXML
{

    const URL = 'http://192.168.210.211:2850/TOCGW';
    const USERNAME = 'headdealer';
    const PASSWORD = 'smspr09ram';

    /**
     * Check Dealer Type
     * @param int $mobile
     * @return mixed
     */
    public static function getHeadDealer($mobile)
    {
        $result = null;
        if (!$mobile) {
            return $result;
        }
        $dealer = self::dealerList($mobile);
        if ($dealer && count($dealer) && isset($dealer['wlist_id'])) {
            $whitList = self::dealerHeadList($dealer['wlist_id']);
            if ($whitList && count($whitList) && isset($whitList['res_ri'])) {
                $display = self::dealerDisplay($whitList['res_ri']);
                if ($display && count($display) && isset($display['msisdn'])) {
                    $result = $display['msisdn'];
                }
            }
        }
        return $result;
    }

    /**
     * Dealer list
     * @param int $mobile
     * @return mixed
     */
    public static function dealerList($msisdn)
    {
        #ppsrdbs.reseller.list,MSISDN="(#msisdn)",NBSTART="0",BITMAP="NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNY";
        $xmlRequest = "<ppsrdbs.reseller.list><MSISDN>$msisdn</MSISDN><NBSTART>0</NBSTART><BITMAP>NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNY</BITMAP></ppsrdbs.reseller.list>";
        $result = self::getCurlOpen(self::URL, $xmlRequest, 'dealerList', TRUE, $msisdn);
        return $result;
    }

    /**
     * Dealer Display
     * @param int $mobile
     * @return mixed
     */
    public static function dealerDisplay($resId)
    {
        #ppsrdbs.reseller.display,RES_ID="(#dealer_#msisdn)",SERVRET="mobicom";
        $xmlRequest = "<ppsrdbs.reseller.display><RES_ID>$resId</RES_ID><SERVRET>mobicom</SERVRET></ppsrdbs.reseller.display>";
        $result = self::getCurlOpen(self::URL, $xmlRequest, 'dealerDisplay', TRUE, $resId);
        return $result;
    }

    /**
     * Dealer head dealer list
     * @param int $mobile
     * @return mixed
     */
    public static function dealerHeadList($whiteId)
    {
        # ppsrdbs.wlist.list,F_ENABLE="",F_MAIN="",LEVL_LST="",RES_RI="",SERVRET="mobicom",WLIST_ID="(#WLIST_ID)",NBSTART="0",SQLADDCRIT="",BITMAP="YYYYYY"
        $xmlRequest = "<ppsrdbs.wlist.list><F_ENABLE></F_ENABLE><F_MAIN></F_MAIN><LEVL_LST></LEVL_LST><SERVRET>mobicom</SERVRET><WLIST_ID>$whiteId</WLIST_ID><NBSTART>0</NBSTART><SQLADDCRIT></SQLADDCRIT><BITMAP>YYYYYY</BITMAP></ppsrdbs.wlist.list>";
        $result = self::getCurlOpen(self::URL, $xmlRequest, 'dealerHeadList', TRUE, $whiteId);
//        print_r($result);
        return $result;
    }

    /**
     * curl -аар нээх
     * 
     * @param int $contract
     * @return Array() 
     */
    public static function getCurlOpen($url, $postData, $type, $xml = false, $number = 0)
    {
        $logId = LogTools::setLogGatewayTocGW($type, $postData, $number);
        $header = array();
        $header[] = "username:" . self::USERNAME;
        $header[] = "password:" . self::PASSWORD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            $info = curl_getinfo($ch);
            LogTools::setLogGatewayTocGwUpdate($logId, print_r($info, TRUE));
        }
        curl_close($ch);
        if ($xml) {
            $responseXml = simplexml_load_string($res, 'SimpleXMLElement');
            $res = json_encode($responseXml);
        }
        $result = json_decode($res, true);
        if (count($result)) {
            LogTools::setLogGatewayTocGwUpdate($logId, print_r($result, TRUE));
            return $result;
        } else {
            return null;
        }
    }

}
