<?php

class CapitronCorpgw
{
    public static function getTransactions($date)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        if ($date) {
            $url = $yml['all']['corpgwUrl'] . '/statement/capitron/' . BankCapitronAccountTable::ACCOUNT_CALLPAYMENT . "?date=" . $date;
        } else {
            $url = $yml['all']['corpgwUrl'] . '/statement/capitron/' . BankCapitronAccountTable::ACCOUNT_CALLPAYMENT;
        }

        $response = self::sendGet($url);
        LogTools::setLogCapitronInit($url, $response, null, null, null, null, null);
        return json_decode($response);
    }

    public static function sendGet($url) {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//        curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1');
//        curl_setopt($ch, CURLOPT_PROXYPORT, '8080');

        // Request
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);
        // Close it
        curl_close($ch);
        return $result;
    }
}