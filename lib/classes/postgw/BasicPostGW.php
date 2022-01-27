<?php

class BasicPostGW
{

    protected $number;
    protected $xml_request;
    protected $xml_response_raw;
    protected $xml_response;
    protected $url;
    protected $header;
    protected $error_msg;
    protected $track;
    protected $userId;
    protected $orderId;
    protected $debug = 1;
    protected $curl_timeout;

    public function BasicPostGW()
    {
        $this->setHeader();
        if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '172.30.14.101'))) {
            die('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
        }
	$yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $this->curl_timeout = $yml['all']['curl_timeout'];
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getArrayResponse()
    {
//        $res = json_encode($this->xml_response);
//        $result = json_decode($res, true);
        return AppTools::xml2array($this->xml_response);
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader()
    {
        $username = sfConfig::get('app_postgateway_username');
        $password = sfConfig::get('app_postgateway_password');
        
        $this->header = array();
        $this->header[] = "Content-Type: text/xml";
        $this->header[] = "User: $username";
        $this->header[] = "Password: $password";
    }

    public function getXmlRequest()
    {
        return $this->xml_request;
    }

    public function setXmlRequest($xml, $option = "")
    {
        $this->xml_request = $xml;
    }

    public function setAttr()
    {
        
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function httpsPost($request, $url)
    {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->debug == 1) {
            $request_file = "debug.xml";
            $fp = fopen($request_file, 'w');

            //debug
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/curlError.log'));
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            $this->responseCode = $info['http_code'];
        }else{
            $error_msg = curl_error($ch);
            $logger->log('=============== Started : ' . date('Y-m-d H:i:s'), sfFileLogger::INFO);
            $logger->log('$type: ' . $this->getClassName(), sfFileLogger::INFO);
            $logger->log('$number: ' . $this->number, sfFileLogger::INFO);
            $logger->log('$xml_request: ' . $this->xml_request, sfFileLogger::INFO);
            $logger->log('$xml_response: ' . $this->xml_response, sfFileLogger::INFO);
            $logger->log('Curl Error msg: '.$error_msg, sfFileLogger::ERR);
        }
        // Close it
        curl_close($ch);
        return $result;
    }

    public function utime()
    {
        $time = explode(" ", microtime());
        $usec = (int) ($time[0] * 1000);
        $sec = (int) ($time[1]);
        return $sec . $usec;
    }

    public function logAccess($xml_request)
    {
        $userId = isset($_REQUEST['UserId']) ? $_REQUEST['UserId'] : 0;
        $orderId = isset($_REQUEST['OrderId']) ? $_REQUEST['OrderId'] : 0;

        $this->setUserId($userId);
        $this->setOrderId($orderId);

        $pdo = LogTools::getLogPDO();
        $sql = "INSERT INTO bankgw_log.`log_gateway_number` (`number` ,`type_s`, `user_id`, `order_id`, `request_xml`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "', '" . $this->orderId . "', '$xml_request');";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_number` SET `response_xml` = :text WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $this->xml_response_raw . print_r($this->getResponse(), true), ':logId' => $logId));
    }

    public function isValid()
    {
        return true;
    }

    public function call($xml_request)
    {
        $this->setHeader();
        $logId = $this->logAccess($xml_request);
        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost($xml_request, $this->url);
        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
        }
        $this->track .= "Задлаж байна <br />";
        if ($this->xml_response = simplexml_load_string($this->xml_response_raw, 'SimpleXMLElement')) {
            $this->track .= "Задалсан <br />";
            $this->setAttr($this->xml_response);
        } else {
            $this->error_msg .= "XML ирсэнгүй";
            $this->logAccessUpdate($this->error_msg);
            return false;
        }
        $this->track .= "Лог бүртгэж байна <br />";
        //$this->cache($xml_request);
        $this->logAccessUpdate($logId);
        if ($this->isValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function setUrl()
    {
        $this->url = sfConfig::get('app_postgateway_api');
    }

}

?>
