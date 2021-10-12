<?php

/**
 * Description of LoyaltyCharge
 *
 * @author belbayar
 */
class LoyaltyCharge
{

    public $number;
    public $userId;
    public $orderId;
    public $type;

    /**
     * Гүйлгээний утгаас CANDY агент ашиглан CANDY орлого хийх гэж буй эсэхийг таних
     * 
     * @param array $transValue
     * @return Bankpayment|null
     */
    public static function chargeQPay($bankOrder, $bankpaymentRow, $number = null)
    {
        $transactionValue = stristr(trim($bankOrder['order_p']), 'qpay');
        $values = explode(" ", $transactionValue);
        $invoiceId = trim($values[2]);
        $status = 0;
        $result = LoyaltyCharge::checkQpayTransaction($invoiceId, $number, $bankOrder['id']);

        if ($result['Code'] == 0) {
            $res1 = (int) isset($result['Result']['result']['qpayStatus']) ? $result['Result']['result']['qpayStatus'] : 0;
            $invoiceId = $result['Result']['result']['accountNo'];
            if ($res1 == 1) {
                $status = 200;
            }
        }
        $bankpaymentRow->setNumber($invoiceId);
        $bankpaymentRow->setContractNumber("QPAY");
        $bankpaymentRow->save();

        $response = array();
        $response['Code'] = $status;
        $response['Number'] = $invoiceId;
        return $response;

    }

    /**
     * Гүйлгээний утгаас CANDY агент ашиглан CANDY орлого хийх гэж буй эсэхийг таних
     * 
     * @param array $transValue
     * @return Bankpayment|null
     */
    public static function checkCandyAccount($transValue, $bankName)
    {
        // Candy Agent sonsoh dans configoos avah
        $bankAgentAccounts = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_bank_accounts.yml');
        $agentAccounts = explode(",", preg_replace('/\s+/', '_', $bankAgentAccounts['all'][$bankName]));

        $isCandyAccount = false;
        if (in_array($transValue, $agentAccounts)) {
            $isCandyAccount = true;
        }

        return $isCandyAccount;
    }

    /**
     * Гүйлгээний утгаас CANDY агент ашиглан CANDY орлого хийх гэж буй эсэхийг таних
     * 
     * @param array $transValue
     * @return Bankpayment|null
     */
    public static function checkCandyAgent($transValue)
    {
        $matches = array();
        $numbers = array();
        $number = false;
        $transValue = preg_replace('/\s+/', '', $transValue);
        preg_match("/.*(([9][954][0-9]{6}[A])|(85[0-9]{6}[A])).*/", mb_strtoupper($transValue, 'UTF-8'), $matches);
        if (isset($matches[0]) && $matches[0]) {
            $transValue = $matches[0];
            preg_match("/([9][954][0-9]{6}[A])|(85[0-9]{6}[A])/", $transValue, $numbers);
            if (isset($numbers[0]) && $numbers[0]) {
                $number = $numbers[0];
                $number = rtrim($number, "A");
            }
        } else {
            // Mongol A useg tanih
            preg_match("/.*(([9][954][0-9]{6}[p{L}А])|(85[0-9]{6}[p{L}А])).*/u", mb_strtoupper($transValue, 'UTF-8'), $matches);
            if (isset($matches[0]) && $matches[0]) {
                $transValue = $matches[0];
                preg_match("/([9][954][0-9]{6}[p{L}А])|(85[0-9]{6}[p{L}А])/u", $transValue, $numbers);
                if (isset($numbers[0]) && $numbers[0]) {
                    $number = $numbers[0];
                    $number = rtrim($number, "А");
                }
            }
        }

        return $number;
    }

    /**
     * Гүйлгээний утгаас CANDY QPAY ашиглан CANDY орлого хийх гэж буй эсэхийг таних
     * 
     * @param array $transValue
     * @return Bankpayment|null
     */
    public static function checkCandyQpay($transValue)
    {
        $matches = array();
        $result = null;
        preg_match("/(QPAY 22639)/", mb_strtoupper($transValue, 'UTF-8'), $matches);
        if (isset($matches[0]) && $matches[0]) {
            $result = str_replace("QPAY ", "", $matches[0]);
        }
        return $result;
    }

    /**
     * Гүйлгээний утгаас CANDY зээл төлөлт хайх
     * 
     * @param array $transValue
     * @return Bankpayment|null
     */
    public static function findCandyLoan($transValue)
    {
        $matches = array();
        $numbers = array();
        $number = false;
        //utasnii dugaar orson bol tanidag bolgov
        preg_match("/.*((9[459]|8[5])\\d{6}).*/", strtoupper($transValue), $matches);

        foreach ($matches as $match) {
            if (strlen(trim($match)) == 8) {
                $numbers[] = $match;
            }
        }
        if (count($numbers)) {
            $number = end($numbers);
        }

        return $number;
    }

    /**
     * ACCOUNTNO
     * 
     * @param array $transValue
     * @return BOOLEAN|null
     */
    public static function isAccountNo($transValue)
    {
        $matches = array();
        $numbers = array();
        preg_match("/.*([123][12][0-9]{9}).*/", strtoupper($transValue), $matches);
        if (isset($matches[0]) && $matches[0]) {
            $transValue = $matches[0];
            preg_match("/([123][12][0-9]{9})/", $transValue, $numbers);
            if (isset($numbers[0]) && $numbers[0]) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Loyalty Charge by Candy
     * @param int $mobile
     * @return mixed
     */
    public static function charge($isdn, $amount, $orderId, $isLoan, $desc = '', $smsPrefix = '', $smsSuffix = '')
    {

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $loyaltyapi = $yml['all']['loyaltyapi']['api'];
        $params = array();
        $params['sender']['type'] = $yml['all']['loyaltyapi']['account_type'];


        if ($isLoan) {
            $preDes = "[$desc] " . $yml['all']['loyaltyapi']['loan_description'];
            $params['sender']['value'] = $yml['all']['loyaltyapi']['account_loan'];
        } else {
            $preDes = $yml['all']['loyaltyapi']['cashin_description'].', '.$desc;
            $params['sender']['value'] = $yml['all']['loyaltyapi']['account_cashin'];
        }
        $username = $yml['all']['loyaltyapi']['cashin_username'];
        $password = $yml['all']['loyaltyapi']['cashin_password'];

        $type = 'PHONE';
        if (self::isAccountNo($isdn)) {
            $type = 'ACCOUNTNO';
        }

        $header = array();
        $header[] = "Content-Type: application/json;charset=utf-8";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);

        $params['receiver']['type'] = $type;
        $params['receiver']['value'] = $isdn;
        $params['amount'] = $amount;
        $params['description'] = $preDes;
        $params['suffix'] = $smsSuffix;
        $params['prefix'] = $smsPrefix;

        $xmlRequest = json_encode($params);

        $loyalty = new LoyaltyCharge();
        $loyalty->number = $isdn;
        $loyalty->orderId = $orderId;
        $loyalty->type = 'LoyaltyCharge';
        $logId = $loyalty->logAccess($xmlRequest);

        $result = self::curlCall($loyaltyapi, $xmlRequest, $header, TRUE);

        $loyalty->logAccessUpdate($logId, $result['Result']);
        $response = array();
        $response['Code'] = $result['HttpCode'];
        if ($result['HttpCode'] == 200) {
            $responseXml = json_decode($result['Result'], true);
            $response['Message'] = $responseXml['info'];
        } else {
            $response['Message'] = $result['Result'];
        }
        return $response;
    }

    /**
     * Loyalty Loan Charge by Candy
     * @param int $mobile
     * @return mixed
     */
    public static function chargeLoan($isdn, $amount, $orderId, $bankName = '')
    {

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $api = $yml['all']['loan']['api'] . '/repay/by-bank';
        $username = $yml['all']['loan']['username'];
        $password = $yml['all']['loan']['password'];

        $params = array();
        $header = array();
        $header[] = "Content-Type: application/json;charset=utf-8";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);

        $params['channel'] = "BankGW-" . $bankName;
        $params['sendSMS'] = "true";
        $params['isdn'] = $isdn;
        $params['amount'] = $amount;
        $params['description'] = "Эргэн төлөлт";

        $xmlRequest = json_encode($params);

        $loyalty = new LoyaltyCharge();
        $loyalty->number = $isdn;
        $loyalty->orderId = $orderId;
        $loyalty->type = 'LoyaltyCharge';
        $logId = $loyalty->logAccess($xmlRequest);

        $result = self::curlCall($api, $xmlRequest, $header, TRUE);

        $loyalty->logAccessUpdate($logId, $result['Result']);
        $response = array();
        $response['Code'] = $result['HttpCode'];
        if ($result['HttpCode'] == 200) {
            $responseXml = json_decode($result['Result'], true);
            $response['Message'] = $responseXml['info'];
        } else {
            $response['Message'] = $responseXml['items']['0']['refundOverRepayment'];
        }
        return $response;
    }

    /**
     * Charge by Candy Agent
     * @param int $mobile
     * @return mixed
     */
    public static function chargeCandyAgent($loyaltyId, $account, $candy, $smsPrefix = '', $smsSuffix = '')
    {
        if ($candy == 0) {
            $response = array();
            $response['Code'] = 404;
            return $response;
        }

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $loyaltyapi = $yml['all']['loyaltyapi']['api'];
        $params = array();
        $params['sender']['type'] = $yml['all']['loyaltyapi']['account_type'];
        $params['sender']['value'] = $yml['all']['loyaltyapi']['account_cashin'];
        $preDes = $yml['all']['loyaltyapi']['cashin_agent_description'];

        $username = $yml['all']['loyaltyapi']['cashin_username'];
        $password = $yml['all']['loyaltyapi']['cashin_password'];

        $type = 'ACCOUNTID';

        $header = array();
        $header[] = "Content-Type: application/json;charset=utf-8";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);

        $params['receiver']['type'] = $type;
        $params['receiver']['value'] = $loyaltyId;
        $params['amount'] = $candy;
        $params['description'] = $preDes;
        $params['suffix'] = $smsSuffix;
        $params['prefix'] = $smsPrefix;

        $xmlRequest = json_encode($params);

        $loyalty = new LoyaltyCharge();
        $loyalty->number = $loyaltyId;
        $loyalty->orderId = $orderId;
        $loyalty->type = 'chargeCandyAgent';
        $logId = $loyalty->logAccess($xmlRequest);

        $result = self::curlCall($loyaltyapi, $xmlRequest, $header, TRUE);

        $loyalty->logAccessUpdate($logId, $result['Result']);
        $response = array();
        $response['Code'] = $result['HttpCode'];
        if ($result['HttpCode'] == 200) {
            $responseXml = json_decode($result['Result'], true);
            $response['Message'] = $responseXml['info'];
        } else {
            $response['Message'] = $result['Result'];
        }
        return $response;
    }

    /**
     * Check loan 
     * @param int $number
     * @return mixed
     */
    public static function getCandy($number)
    {

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $loyaltypartner = $yml['all']['loyaltypartner']['api'];
        $username = $yml['all']['loyaltypartner']['username'];
        $password = $yml['all']['loyaltypartner']['password'];

        $header = array();
        $header[] = "Content-Type: application/json";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);
        $xmlRequest = $loyaltypartner . '?partner=' . $number . '&partner.system=ISDN';

        $loyalty = new LoyaltyCharge();
        $loyalty->number = $number;
        $loyalty->type = 'getCandy';
        $logId = $loyalty->logAccess($xmlRequest);
        $result = self::curlCall($xmlRequest, $loyalty->type, $header);
        $httpcode = $result['HttpCode'];
        $response = 0;
        $loyalty->logAccessUpdate($logId, $result['Result']);
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if ($res['type'] === "agent") {
                $response = (int) $res['lid'];
            }
        }

        return $response;
    }

    /**
     * Check loan 
     * @param int $number
     * @return mixed
     */
    public static function checkLoan($number)
    {
        if (LoyaltyCharge::isAccountNo($number)) {
            $response['Code'] = 0;
            $response['Result'] = 'ACCOUNTNO';
            return $response;
        }
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $api = $yml['all']['loan']['api'] . '/balance';
        $username = $yml['all']['loyaltyapi']['username'];
        $password = $yml['all']['loyaltyapi']['password'];


        $header = array();
        $header[] = "Content-Type: application/json";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);

        $loan = new LoyaltyCharge();
        $loan->number = $number;
        $loan->type = 'checkCandyLoan';
        $logId = $loan->logAccess($number);
        $result = self::curlCall($api . '?isdn=' . $number, $number, $header);
        $loan->logAccessUpdate($logId, $result['Result']);
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
        return $response;
    }

    /**
     * Check loan 
     * @param int $number
     * @return mixed
     */
    public static function checkQpayTransaction($invoiceId, $retry = null, $orderId = 0)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $api = $yml['all']['candyqpay']['api_check'];
        $type = 'checkQpayTransaction';
        if ($retry) {
            $api = $yml['all']['candyqpay']['api_retry'];
            $type = 'retryQpayTransaction';
        }
        $username = $yml['all']['candyqpay']['username'];
        $password = $yml['all']['candyqpay']['password'];


        $header = array();
        $header[] = "Content-Type: application/json";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);

        $loan = new LoyaltyCharge();
        $loan->number = $orderId;
        $loan->orderId = $orderId;
        $loan->type = $type;
        $logId = $loan->logAccess($invoiceId);
        $result = self::curlCall($api . '?invoiceId=' . $invoiceId, $invoiceId, $header);
        $loan->logAccessUpdate($logId, $result['Result']);
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
        return $response;
    }

    /**
     * Үлдэгдэл candy авах
     * 
     * @param type $number
     * @param type $date    2015-09-01
     * @param type $system
     * @return type
     */
    public static function lapiGetCustomer($number)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');

        $api = $yml['all']['loyaltyapi']['api_customer'];
        $username = $yml['all']['loyaltyapi']['username'];
        $password = $yml['all']['loyaltyapi']['password'];

        $header = array();
        $header[] = "Content-Type: application/json";
        $userPwd = "$username:$password";
        $header[] = "Authorization: Basic " . base64_encode($userPwd);
//        echo $userPwd;die();
        $loan = new LoyaltyCharge();
        $loan->number = $number;
        $loan->type = 'lapiGetCustomer';
        $logId = $loan->logAccess($number);
        $url = str_replace('#number', $number, $api);
        $url = str_replace('#system', 'ISDN', $url);
        $result = self::curlCall($url, '', $header);
        $loan->logAccessUpdate($logId, $result['Result']);
        $httpcode = $result['Result'];
        $response = array();
        $response['HttpCode'] = $httpcode;
        if ($httpcode == 200) {
            $response['Result'] = json_decode($result['Result'], true);
        } else {
            $response['Message'] = $result['Result'];
        }
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

    public function logAccess($xml_request)
    {
        $pdo = LogTools::getLogPDO();
        $sql = "INSERT INTO bankgw_log.`log_gateway_loyaltyapi` (`number` ,`type_s`, `order_id`, `request_xml` )VALUES ('" . $this->number . "', '" . $this->type . "', '" . $this->orderId . "', '" . $xml_request . "');";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_loyaltyapi` SET `response_xml` = :text WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $text, ':logId' => $logId));
    }

}

?>