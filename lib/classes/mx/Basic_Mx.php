<?php

class Basic_Mx
{

    private $number;
    private $xml_request;
    private $xml_response_raw;
    private $xml_response;
//    private $url = 'http://172.30.52.194:8080';
    private $url = 'http://10.10.41.26:8080';
    private $header;
    private $error_msg;
    private $bank_id;
    private $order_id;
    public $logId;

    public function Basic_Mx()
    {
        $this->setHeader();
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getXmlResponseRaw()
    {
        return $this->xml_response_raw;
    }

    public function setHeader()
    {
        $this->header = array();
        $this->header[] = "Content-Type: application/xml;charset=utf-8";
        $this->header[] = "User: NTCUSer";
        $this->header[] = "Password: Password1";
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

    public function setBankId($id)
    {
        $this->bank_id = $id;
    }

    public function setOrderId($id)
    {
        $this->order_id = $id;
    }

    public function httpsPost($request, $url)
    {
// Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_USERPWD, "sms_admin:x52wEB");
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
        mysql_close($link);

        return $result;
    }

    public function logAccess($xml_request)
    {
        $sql = "INSERT INTO bankgw_log.`log_mx_charge` (`bank_id` ,`order_id`, `request` ,`ip`,`created_at`)VALUES ('" . $this->bank_id . "', '" . $this->order_id . "', '" . $xml_request . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . date('Y-m-d H:i:s') . "');";
        $link = mysql_connect('127.0.0.1', 'bankgw', 'B4H3sAWyDvsjC382') or die(mysql_error());
        mysql_set_charset('utf8', $link) or die(mysql_error());
        $result = mysql_query($sql, $link);
        $this->logId = mysql_insert_id();
        mysql_close($link);
    }

    public function logAccessUpdate($text = "")
    {
        if ($text != "") {
            $str = $text;
        } else {
            $str = $this->xml_response->asXml();
        }
        $now = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $sql = "UPDATE bankgw_log.`log_mx_charge` SET `response` = '" . $str . "', `updated_at` = '". $now ."' WHERE id = " . $this->logId;

        $this->mysqlExecute($sql);
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $xml_request = $this->xml_request;
        $url = $this->url;

        $this->logAccess($xml_request);
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобинетээс дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost($xml_request, $url);

        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
            $this->logAccessUpdate($this->error_msg);
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