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
        $url = $yml['all']['corpgwUrl'] . '/statement/khaan/date';

        $header[] = "Content-Type: application/json";
        $accountsDto = json_encode($accountList);

        $a = self::sendPost($url,$header, $accountsDto);
        $sortedResponse = json_decode($a);
        usort($sortedResponse, function ($a, $b) {
            return $a->record - $b->record;
        });

        return $sortedResponse;
    }

    public static function getStatementByAccount($organization, $account) {
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/khaan-corpgw.log'));
        $logger->log('get statement by account',sfFileLogger::INFO);
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        $url = $yml['all']['corpgwUrl'] . '/account/khaan/' . $organization . '/' . $account;
        $logger->log('url = ' . $url,sfFileLogger::INFO);
        $header[] = "Content-Type: application/json";
        $a = self::sendPost($url, $header, null);
        var_dump(json_decode($a));
        die();
        return json_decode($a);
    }

    public static function getAccountLatestRecords()
    {
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/khaan-corpgw.log'));
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_banks.yml');
        $accountsDto = self::getAccountListWithRecord();
        $logger->log('get account latest record', sfFileLogger::INFO);
        $transactions = [];
        foreach($accountsDto as $account) {
            $logger->log('foreach organization=' . $account['organization'] . '; account=' . $account['account'], sfFileLogger::INFO);
            $response = self::getStatementByAccount($account['organization'], $account['account']);
            var_dump($response);
            die();
            $logger->log('foreach date=' . $response['account']['lastFinancialTranDate'], sfFileLogger::INFO);
            $account['date'] = str_replace("\\", "", $response['account']['lastFinancialTranDate']);
            $logger->log('replaced date=' . $account['date'], sfFileLogger::INFO);

            $item = getTransactionsByDate($account);
            array_push($transactions, $item);
        }
        var_dump(json_decode($transactions));
        die();
        return json_decode($transactions);
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