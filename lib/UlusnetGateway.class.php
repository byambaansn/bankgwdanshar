<?php

class UlusnetGateway
{

    const URL = 'http://bankgw/ulusnet/';

    public static function userInfo($number)
    {
        $url = self::URL . "UlusnetUserInfo.php";
        $postData = array('username' => $number);

        $response = self::curlOpen($postData, $url, TRUE);

        if ($response) {
            if (isset($response['Code']) && $response['Code'] == 0) {
                return $response;
            }
        }
        return 0;
    }

    public static function chargePayment($number, $code, $userId)
    {
        if (!$number || !$code || !$userId) {
            return 0;
        }
        $url = self::URL . "UlusnetCharge.php";
        $postData = array('username' => $number, 'prodid' => $code, 'transby' => $userId, 'reason' => '');

        return self::curlOpen($postData, $url, TRUE);
    }

    /**
     * curl -аар нээх
     * 
     * @param int $contract
     * @return Array() 
     */
    public static function curlOpen($postData, $url = null, $xml = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
        $res = curl_exec($ch);
        curl_close($ch);
        if ($xml) {
            $responseXml = simplexml_load_string($res, 'SimpleXMLElement');
            $res = json_encode($responseXml);
        }
        $result = json_decode($res, true);
        if (count($result)) {
            return $result;
        } else {
            return null;
        }
    }

}
