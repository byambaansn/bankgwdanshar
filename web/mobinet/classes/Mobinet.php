<?php

class Mobinet
{

    private $number;
    private $xml_request;
    private $xml_response_raw;
    private $xml_response;
    private $url = 'http://172.29.1.18:8080';
    private $header;
    private $error_msg;
    private $track;
    private $userId;
    public $logId;

    public function Basic()
    {
        $this->setHeader();
        if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '172.30.14.101'))) {
            die('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
        }
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader()
    {
        $this->header = array();
        $this->header[] = "Content-Type: text/xml;charset=utf-8";
        /* $this->header[] = "User: sms_admin";
          $this->header[] = "Password: x52wEB"; */
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

    public function httpsPost($request, $url)
    {
// Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "bankpayment:4cb4b8");
// Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
//echo $request;
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

    public function mysqlExecute($query)
    {
        $link = mysql_connect('127.0.0.1', 'bankgw', 'B4H3sAWyDvsjC382') or die(mysql_error());
        mysql_set_charset('utf8', $link) or die(mysql_error());
        $result = mysql_query($query, $link);
        $this->logId = mysql_insert_id();
        mysql_close($link);

        return $result;
    }

    public function logAccess($xml_request)
    {
        $sql = "INSERT INTO bankgw_log.`log_gateway_mobinet` (`number` ,`type_s`, `user_id` ,`request_xml`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "', '$xml_request');";
        $this->mysqlExecute($sql);
    }

    public function logAccessUpdate($text = "")
    {
        if ($text != "") {
            $str = $text;
        } else {
            $str = $this->xml_response->asXml();
        }
        $sql = "UPDATE bankgw_log.`log_gateway_mobinet` SET `response_xml` = '" . $str . "' WHERE id = " . $this->logId;
        $this->mysqlExecute($sql);
    }

    public function isValid()
    {
        return true;
    }

    public function call($xml_request, $url)
    {
        $this->logAccess($xml_request);
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобинетээс дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost($xml_request, $url);

        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
            $this->logAccessUpdate($this->error_msg);
            return false;
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
        $this->logAccessUpdate();
//echo $this->track;
        if ($this->isValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function responseIsEmpty()
    {
        $xmler = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<PostPhoneInfoRes><Code>1004</Code><Info>Response Empty</Info></PostPhoneInfoRes>
XML;
        return $xmler;
    }

}

?>