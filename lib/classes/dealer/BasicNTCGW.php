<?php

class BasicNTCGW
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

    public function BasicNTCGW()
    {
        $this->setHeader();
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
        $this->header = array();
        $this->header[] = "Content-Type: application/json";
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
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
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
        $sql = "INSERT INTO bankgw_log.`log_gateway_dealer` (`number` ,`type_s`, `user_id`, `order_id`, `request_xml`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "', '" . $this->orderId . "', '$xml_request');";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_dealer` SET `response_xml` = :text WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $sth->execute(array(':text' => $this->xml_response_raw, ':logId' => $logId));
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $this->setHeader();
        $xml_request = $this->xml_request;
        $logId = $this->logAccess($xml_request);
        $this->track .= "Call function ?????????????????? <br /> XML ?????? ???????????????????? ???????????? ?????????? <br />";
        $this->xml_response_raw = $this->httpsPost($xml_request, $this->url);
        if ($this->xml_response_raw) {
            $this->track .= "?????????? ?????????? <br />";
        } else {
            $this->error_msg .= "?????????? ???????????????? <br />";
        }
        $this->track .= "???????????? ?????????? <br />";
        if ($this->xml_response = simplexml_load_string($this->xml_response_raw, 'SimpleXMLElement')) {
            $this->track .= "???????????????? <br />";
            $this->setAttr($this->xml_response);
        } else {
            $this->error_msg .= "XML ????????????????";
            return false;
        }
        $this->track .= "?????? ?????????????? ?????????? <br />";
        //$this->cache($xml_request);
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

    public function getClassName()
    {
        return get_class($this);
    }

}

?>