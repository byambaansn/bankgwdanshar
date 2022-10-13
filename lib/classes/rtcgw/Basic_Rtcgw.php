<?php

class Basic_Rtcgw
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
    protected $timeout;

    public function __construct()
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_rtcgw.yml');
        $url = $yml['all']['rtcgw']['api'];
        $timeout = $yml['all']['rtcgw']['curl_timeout'];
        $username = $yml['all']['rtcgw']['username'];
        $password = $yml['all']['rtcgw']['password'];
        $this->setUrl($url);
        $this->setHeader($username, $password);
        $this->setTimeout($timeout);
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader($username, $password)
    {
        $this->header = array();
        $this->header[] = "Content-Type: text/xml";
        $this->header[] = "User: $username";
        $this->header[] = "Password: $password";
    }

    public function getXmlRequest()
    {
        return $this->xml_request;
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

    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    public function httpsPost($request, $url)
    {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
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
        $pdo = LogTools::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_gateway_topup (`number`, `user_id`, `type_s`, `request_xml`, `created_at`)
                VALUES ('" . $this->number . "','" . $this->userId . "','" . $this->getClassName() . "', '" . $xml_request . "', '" . date('Y-m-d H:i:s') . "')";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_topup` SET `response_xml` = :text, `updated_at` = :date WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $this->xml_response_raw, ':date' => (new \DateTime())->format('Y-m-d H:i:s'), ':logId' => $logId));
    }

    public function isValid()
    {
        return true;
    }

    public function call($xml_request)
    {

        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $logId = $this->logAccess($xml_request);
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
            return false;
        }
        $this->track .= "Лог бүртгэж байна <br />";
        $this->logAccessUpdate($logId);

        if ($this->isValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getClassName()
    {
        return get_class($this);
    }

}

?>
