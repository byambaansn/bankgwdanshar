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
class SapcGateway
{

    /**
     * ДАТА  цэнэглэлт
     * @param String $number утасны дугаар
     * @param String $card profile code
     * @param integer $userId төлөх дүн
     * @param integer $chargerId төлөлт хийж буй огноо
     * @return array Цэнэгэлэлт орсон тухай
     */
    public static function chargeFreePackage($msisdn, $package, $logger, $bankName)
    {
        $appName = "USSD";
        $message = "Ta #name bagts avah erhtei bolloo. Data bagtsaa idevhjuuleh bol #smsname gej bicheed 592 dugaart ilgeene uu.";
        $params = "system=$appName&cmd=free&isdn=$msisdn&package=$package&customsms=" . urlencode($message) . "&promotion=$bankName$appName" . "&desc=$bankName$appName";
        $logId = self::logAccess($msisdn, $params);

        if (!$msisdn || !$package) {
            $logger->log(" --not found parameters --dealerId:$msisdn, productOptId:$package", sfFileLogger::ERR);
            return null;
        }

        $request = "http://10.12.15.70/upcc-provision-web/rest/extendlistener?$params";
        // Initialisation
        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $response = curl_exec($ch);
        // Close it
        curl_close($ch);
        self::logAccessUpdate($logId, $response);

        $result = array();
        $result['Code'] = 500;
        $result['Info'] = $response;
        if ($response === "OK") {
            $result['Code'] = 0;
            $result['Info'] = 'Success';
        }
        return $result;
    }

    public static function logAccess($number, $request)
    {
        $pdo = LogTools::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_gateway_sapc (`number`, `user_id`, `type_s`, `request_xml`, `created_at`)
                VALUES ('" . $number . "','0','SapcFreePackage', '" . $request . "', '" . date('Y-m-d H:i:s') . "')";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public static function logAccessUpdate($logId, $result = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_sapc` SET `response_xml` = :text, `updated_at` = :date WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $result, ':date' => (new \DateTime())->format('Y-m-d H:i:s'), ':logId' => $logId));
    }

}
