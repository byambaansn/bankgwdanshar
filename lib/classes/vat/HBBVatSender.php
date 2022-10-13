<?php

/**
 * Description of HBBVatSender
 *
 * @author enkhsaikhan.da
 */
class HBBVatSender
{
    /**
     * Send VAT to HBB customer
     * @param int $number
     * @return mixed
     */
    public static function sendVat($amount, $isdn = null, $email = null)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $api = $yml['all']['vatsender']['api'];

        $username = $yml['all']['vatsender']['username'];
        $password = $yml['all']['vatsender']['password'];

        $header = array();
        $header[] = "Content-Type: application/json";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);
        $url = $api . 'receive?queue=true&amount=' . $amount . '&channel=bank&sendmail=true&company=mobinet&sendmail=true&autovat=true';
        if($isdn) {
            $url = $url . '&isdn=' . $isdn;
        }
        if($email) {
            $url = $url . '&email=' . $email;
        }
        $start = (new \DateTime())->format('Y-m-d H:i:s');
        $result = self::curlCall($url, null, $header);
        $end = (new \DateTime())->format('Y-m-d H:i:s');
        $httpcode = $result['HttpCode'];
        $response = array();
        $response['Code'] = $httpcode;
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if (isset($res['code'])) {
                $response['Code'] = (int) $res['code'];
                $response['Result'] = $res;
            }
        } else {
            $response['Message'] = $result['Result'];
        }
        self::logAccess($isdn,$response['Code'],$url,$result['Result'], $start, $end);
        return $response;
    }

    /**
     * Curl Request
     * @param array $header
     * @return mixed
     */
    public static function curlCall($url, $body, $header, $post = false)
    {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
//        curl_setopt($ch, CURLOPT_PROXY, "172.17.56.31:8080");
        // execute the connexion
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
        }
        // Close it
        curl_close($ch);

        $response = array();
        $response['HttpCode'] = $httpcode;
        $response['Result'] = $result;

        return $response;
    }

    public function logAccess($number, $responseCode, $url, $response, $start, $end)
    {
        $pdo = LogTools::getLogPDO();

        $sql = "INSERT INTO bankgw_log.`log_gateway_vat` (`number` ,`type_s`, `user_id`, `response_code`, `url`, `request_xml` ,`response_xml`, `created_at`, `updated_at`)VALUES ('" .
            $number . "', '" . get_class($this) . "', 0," . $responseCode . ", '" . $url . "', '', '" . $response . "', '". $start ."', '". $end ."');";
        $pdo->exec($sql);
    }

}

?>