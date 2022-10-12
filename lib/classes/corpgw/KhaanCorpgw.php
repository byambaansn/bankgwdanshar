<?php

class KhaanCorpgw
{
    public static function getTransactions()
    {
        set_time_limit(3600);
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        $url = $yml['all']['corpgwUrl'] . '/statement/khaan/record';

        $header[] = "Content-Type: application/json";
        $accountsDto = json_encode(self::getAccountListWithRecord());
        $a = self::sendPost($url,$header, $accountsDto);

        LogTools::setLogKhaanInit($accountsDto, $a, null, null, null, null, null);

        $sortedResponse = json_decode($a);
        usort($sortedResponse, function ($a, $b) {
            return $a->record - $b->record;
        });
        return $sortedResponse;
    }

    public static function getTransactionsByDate($accountList)
    {

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        $url = $yml['all']['bankgwDistributorUrl'] . '/khaan/date';

        $header[] = "Content-Type: application/json";
        $accountsDto = json_encode($accountList);

        $a = self::sendPost($url,$header, $accountsDto);
        $sortedResponse = json_decode($a);
        usort($sortedResponse, function ($a, $b) {
            return $a->record - $b->record;
        });

        return $sortedResponse;
    }

    public static function getAccountLatestRecords()
    {
//        set_time_limit(3600);
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        $url = $yml['all']['bankgwDistributorUrl'] . '/khaan/update-record';

        $header[] = "Content-Type: application/json";
        $accountsDto = json_encode(self::getAccountListWithRecord());
        $a = self::sendPost($url,$header, $accountsDto);
        return json_decode($a);
    }

    public static function sendPost($url, $header, $body) {
            // Initialisation
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_PROXY, '');
//            curl_setopt($ch, CURLOPT_PROXYPORT, '8080');

            // Request
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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

    public static function getAccountList()
    {
        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            $sql = "SELECT organization, account
                FROM bankgw.bank_khaan_record
                WHERE active = 1";
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function getAccountListWithRecord()
    {
        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            $sql = "SELECT organization, account, record
                FROM bankgw.bank_khaan_record
                WHERE active = 1";
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            return null;
        }
    }

}