<?php

class BasicMobinetGW
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
    protected $hasHeader = true;
    protected $debug = 1;
    public $logId;

    public function BasicMobinetGW()
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $this->username = $yml['all']['mobinethbb']['username'];
        $this->password = $yml['all']['mobinethbb']['password'];
        $this->curl_timeout = $yml['all']['curl_timeout'];
        $this->setHeader();
        $this->setUrl();
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getArrayResponse()
    {
        return AppTools::xml2array($this->xml_response);
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader()
    {
        $this->header = array();

        if ($this->hasHeader) {
            if ($this->customHeader) {
                $this->header[] = "Content-Type: application/json";
            } else {
                $this->header[] = "Content-Type: application/xml";
            }
            $this->header[] = 'Accept-Encoding: gzip, deflate';
        }
        $this->header[] = "User: " . $this->username;
        $this->header[] = "Password: " .$this->password;
        $this->userPwd = $this->username.":".$this->password;
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

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function httpsPost()
    {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_USERPWD, $this->userPwd);
        curl_setopt($ch, CURLOPT_HTTPHEADER, false);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->debug == 1) {
            $request_file = "debug.xml";
            $fp = fopen($request_file, 'w');

            //debug
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }
        // Request Type
        if ($this->customRequest != '') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->customRequest);
        }
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xmlRequest);
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
            $logger->log('$type: ' . get_called_class(), sfFileLogger::INFO);
            $logger->log('$number: ' . $this->number, sfFileLogger::INFO);
            $logger->log('$xml_request: ' . $this->xml_request, sfFileLogger::INFO);
            $logger->log('$xml_response: ' . $this->xml_response, sfFileLogger::INFO);
            $logger->log('Curl Error msg: '.$error_msg, sfFileLogger::ERR);
        }

        // Close it
        curl_close($ch);
        return $result;
    }

    public function isValid()
    {
        return true;
    }

    public function call($xml_request)
    {
        $this->logAccess($xml_request);
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost();
        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
            $this->logAccessUpdate($this->error_msg);
            return false;
        }
        $this->track .= "Задлаж байна <br />";
        $this->xml_response = $this->xml_response_raw;
        $this->track .= "Лог бүртгэж байна <br />";
        $this->logAccessUpdate();
        if ($this->isValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function setUrl()
    {
        $this->url = sfConfig::get('app_mobinethbb_api');
    }

    public function logAccess($xml_request)
    {
        $sql = "INSERT INTO bankgw_log.`log_gateway_mobinet` (`number` ,`type_s`, `user_id` ,`request_xml`)VALUES ('" . $this->number . "', '" . get_called_class() . "', '" . $this->userId . "', '$xml_request')";
        $this->logId = LogTools::executeGetId($sql);
    }

    public function logAccessUpdate($text = "")
    {
        if ($text != "") {
            $str = $text;
        } else {
            $str = $this->xml_response;
        }
        $sql = "UPDATE bankgw_log.`log_gateway_mobinet` SET `response_xml` = '" . $str . "', `updated_at` = '". (new \DateTime())->format('Y-m-d H:i:s') ."' WHERE id = " . $this->logId;
        LogTools::execute($sql);
    }

}

?>