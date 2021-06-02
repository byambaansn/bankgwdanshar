<?php

class Basic_SmallUnit
{

    protected $number;
    protected $xml_request;
    protected $xml_response_raw;
    protected $xml_response;
    protected $url;
    protected $appName = 'BANKGW';
    protected $header;
    protected $error_msg;
    protected $track;
    protected $userId;
    protected $vendorId;

    public function Basic_SmallUnit()
    {
        $this->setHeader();
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader()
    {
        $this->header = array();
        $this->header[] = "Content-Type: application/xml";
        $this->header[] = "X-Interface: " . $this->appName;
        $this->header[] = "X-Request-Id: " . $this->getRequestId();
//        $this->header[] = "user: usalsms";
//        $this->header[] = "password: fj3fjlsj832jf.ksJFH#*@FJpjw2fo";
//        $this->userPwd = 'usalsms:fj3fjlsj832jf.ksJFH#*@FJpjw2fo';
    }

    public function getXmlRequest()
    {
        return $this->xml_request;
    }

    public function setXmlRequest($xml)
    {
        $this->xml_request = $xml;
    }

    public function setAttr()
    {
        
    }

    public function getRequestId()
    {
        return round(microtime(true) * 1000);
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
        #Initialisation
        //echo  $url . $request;die();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url . $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        #execute the connexion
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
        #Close it
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
        $sql = "INSERT INTO bankgw_log.log_topup_charge (`number`, `user_id`, `log_function`, `request`, `created_at`)
                VALUES ('" . $this->number . "','" . $this->userId . "','" . $this->getClassName() . "', '" . $xml_request . "', '" . date('Y-m-d H:i:s') . "')";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_topup_charge` SET `log_response` = :text WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $this->xml_response_raw, ':logId' => $logId));
    }

    public function isValid()
    {
        return true;
    }

    public function call($xml_request)
    {
        $logId = $this->logAccess($this->url . $xml_request);
        $this->setHeader();

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

    public function setUrl()
    {
        //$this->url = "http://192.168.40.11:5999";
        $this->url = "http://10.12.16.53:4999";
    }

    public function getClassName()
    {
        return get_class($this);
    }

}

?>
