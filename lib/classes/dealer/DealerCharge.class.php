<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DealerCharge
 *
 * @author duurenbayar
 */
class DealerCharge
{

    const DIRECT_DEALER_PERCENT = 4;
    const CHARGE_URL_TEST_IN = 'http://192.168.40.7:3335/mdealercharger';
    const CHARGE_URL = 'http://10.12.16.53:3334/mdealercharger';
    const CHARGE_URL_CANDY = 'http://10.12.16.53:3344/mdealercharger';

    public static function charge($mobile, $amount, $checkLimit = 1)
    {
        set_time_limit(1200);

        // цэнэглэх дугаар
        $mobile = trim($mobile);
        // цэнэглэх дүн
        $amount = (double) $amount;
        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::CHARGE_URL . '?mdealer=' . $mobile . '&amount=' . $amount,
            'transferred' => '0',
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );

        if ($checkLimit) {
            if ($amount > BankSavingsTable::MAX_AMOUNT_LIMIT) {
                $return['log_response'] = 'Цэнэглэсэн үнийн дүн ' . BankSavingsTable::MAX_AMOUNT_LIMIT . '-с хэтэрсэн.';
                $return['error_code'] = BankSavingsTable::STAT_FAILED_MAX_AMOUNT;
                return $return;
            }
            if ($amount < BankSavingsTable::MIN_AMOUNT_LIMIT) {
                $return['log_response'] = 'Цэнэглэсэн үнийн дүн ' . BankSavingsTable::MIN_AMOUNT_LIMIT . '-с бага.';
                $return['error_code'] = BankSavingsTable::STAT_FAILED_MIN_AMOUNT;
                return $return;
            }
        }

        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);
        $responseText = (string) $b->getResponseText();

//        $responseText = "<ResCharger><Code>0</Code><Info>Амжилттай</Info><Transferred>48001</Transferred><Received>4</Received><Percent>4</Percent></ResCharger>";
        $xml = simplexml_load_string($responseText);

        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/DealerCharge.log'));
        $logger->log($mobile . '--$responseText--=' . $responseText, sfFileLogger::INFO);
        // үр дүн
        $responseArr = json_decode(json_encode($xml), TRUE);

        if ((string) $xml->Code[0] === "0") {
            $logger->log($mobile . '--Code YES--=' . $xml->Code[0], sfFileLogger::INFO);
            $return['success'] = TRUE;
        } else {
            $logger->log($mobile . '--Code No--=' . $xml->Code[0], sfFileLogger::INFO);
            if ($xml->Code[0] == 2) {
                $return['error_code'] = BankSavingsTable::STAT_FAILED_MIN_AMOUNT;
            }
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        #
        $percent = (double) $xml->Percent[0];
        $return['transferred'] = $amount / (100 - $percent) * 100;
        $return['percent'] = $percent;
        $return['log_response'] = $responseText;
        return $return;
    }

    public static function chargeByCandy($mobile, $amount)
    {
        set_time_limit(1200);

        // цэнэглэх дугаар
        $mobile = trim($mobile);
        // цэнэглэх дүн
        $amount = (double) $amount;
        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::CHARGE_URL_CANDY . '?mdealer=' . $mobile . '&amount=' . $amount,
            'transferred' => '0',
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );

        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);
        $responseText = (string) $b->getResponseText();

//        $responseText = "<ResCharger><Code>0</Code><Info>Амжилттай</Info><Transferred>48001</Transferred><Received>4</Received><Percent>4</Percent></ResCharger>";
        $xml = simplexml_load_string($responseText);

        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }

        // үр дүн
        $responseArr = json_decode(json_encode($xml), TRUE);

        if ($responseArr['Code'] == 0) {
            $return['success'] = TRUE;
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        #
        $percent = (double) $responseArr['Percent'];
        $return['transferred'] = $amount / (100 - $percent) * 100;
        $return['percent'] = $percent;
        $return['log_response'] = $responseText;
        return $return;
    }

    public static function chargeTestIN($mobile, $amount)
    {
        set_time_limit(1200);

        // цэнэглэх дугаар
        $mobile = trim($mobile);
        // цэнэглэх дүн
        $amount = (double) $amount;
        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::CHARGE_URL_TEST_IN . '?mdealer=' . $mobile . '&amount=' . $amount,
            'transferred' => '0',
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );

        if ($amount > BankSavingsTable::MAX_AMOUNT_LIMIT) {
            $return['log_response'] = 'Цэнэглэсэн үнийн дүн ' . BankSavingsTable::MAX_AMOUNT_LIMIT . '-с хэтэрсэн.';
            $return['error_code'] = BankSavingsTable::STAT_FAILED_MAX_AMOUNT;
            return $return;
        }
        if ($amount < BankSavingsTable::MIN_AMOUNT_LIMIT) {
            $return['log_response'] = 'Цэнэглэсэн үнийн дүн ' . BankSavingsTable::MIN_AMOUNT_LIMIT . '-с бага.';
            $return['error_code'] = BankSavingsTable::STAT_FAILED_MIN_AMOUNT;
            return $return;
        }

        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);
        $responseText = (string) $b->getResponseText();

//        $responseText = "<ResCharger><Code>0</Code><Info>Амжилттай</Info><Transferred>48001</Transferred><Received>4</Received><Percent>4</Percent></ResCharger>";
        $xml = simplexml_load_string($responseText);

        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }

        // үр дүн
        $responseArr = json_decode(json_encode($xml), TRUE);

        if ($responseArr['Code'] == 0) {
            $return['success'] = TRUE;
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        #
        $percent = (double) $responseArr['Percent'];
        $return['transferred'] = $amount / (100 - $percent) * 100;
        $return['percent'] = $percent;
        $return['log_response'] = $responseText;
        return $return;
    }

    /**
     * 
     * @param int $mobile
     * @return mixed
     */
    public static function getDealer($mobile)
    {
        if (!$mobile) {
            return false;
        }
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/savingsBank.log'));

        $link = mysql_connect('172.27.30.60', 'bankgw', 'B4H3sAWyDvsjC382');
        if ($link === FALSE) {
            $logger->log('[ERROR] ' . mysql_error(), sfFileLogger::INFO);
            return FALSE;
        }

        if (mysql_select_db('dealer', $link) == FALSE) {
            $logger->log('[ERROR] ' . mysql_error(), sfFileLogger::INFO);
            return FALSE;
        }

        mysql_query('SET NAMES utf8', $link);

        $sql = "SELECT user_id, mobile, sale_percent_mtopup, code, alias
                        FROM dealer
                        WHERE mobile = '" . (int) $mobile . "' AND is_active = 1
                        LIMIT 1";
        $result = mysql_query($sql, $link);

        if ($result == FALSE) {
            $logger->log('[ERROR] ' . mysql_error(), sfFileLogger::INFO);
            return FALSE;
        }

        $dealer = mysql_fetch_assoc($result);

        mysql_close($link);

        return $dealer;
    }

    /**
     * Check Dealer Type
     * @param int $mobile
     * @return mixed
     */
    public static function getHeadDealer($mobile)
    {
        $username = "headdealer"; //sfConfig::get('app_dealer_tocXmlGw_username');
        $password = "smspr09ram"; //sfConfig::get('app_dealer_tocXmlGw_password');

        $header = array();
        $header[] = "username:$username";
        $header[] = "password:$password";
        //ppsrdbs.reseller.display,RES_ID="(#dealer_#msisdn)",SERVRET="mobicom";
        //echo  $xmlRequest = "<ppsrdbs><reseller><display><RES_ID>ddealer_$mobile</RES_ID><SERVRET>mobicom</SERVRET></display></reseller></ppsrdbs>";
        echo $xmlRequest = "<ppsrdbs.reseller.display><RES_ID>mdealer_99516449</RES_ID><SERVRET>mobicom</SERVRET></ppsrdbs.reseller.display>";
        //echo $xmlRequest = "<ppsrdbs.reseller.display><RES_ID>mdealer_$mobile</RES_ID><SERVRET>mobicom</SERVRET></display.reseller.ppsrdbs>";
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($ch, CURLOPT_USERPWD, $this->userPwd);
        //       curl_setopt($ch, CURLOPT_HTTPHEADER, false);
        curl_setopt($ch, CURLOPT_URL, "http://192.168.210.211:2850/TOCGW");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
        }
        // Close it
        curl_close($ch);
        $responseXml = simplexml_load_string($result, 'SimpleXMLElement');
        $res = json_encode($responseXml);
        $result = json_decode($res, true);
        print_r($result);

        return $result;
    }

    /**
      message to Dealer
     * @param int $mobile
     * @return mixed
     */
    public static function sentMessage($mobile, $message)
    {
        $url = "http://192.168.202.84:8099/sms_send?s=1&username=poodii&password=poodii&from=482&to=976" . $mobile . "&text=$message";
        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($url);
        $responseText = (string) $b->getResponseText();
        LogTools::setMessageDealer($url, $responseText, 0);
    }

}

?>
