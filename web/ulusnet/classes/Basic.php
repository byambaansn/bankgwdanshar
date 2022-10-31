<?php

class Basic
{

    protected $number;
    protected $xmlRequest;
    protected $xml_response_raw;
    protected $xml_response;
    protected $url;
    protected $header;
    protected $error_msg;
    protected $track;
    protected $userId;
    protected $status;
    protected $debug = 0;
    protected $appName = 'Bankpayment';
    protected $responseCode = 0;
    protected $customRequest = '';
    protected $htmlSpecialChar = 0;
    protected $userPwd = '';
    protected $defaultResponse = false;
    protected $command = 0;
    protected $isProd = 1;

    const SERVER = "";
    const SUCCESSFUL = 1;
    const FAILED = 2;

    public function __construct()
    {
        $this->setHeader();
        $this->setUrl();
        error_reporting(E_ERROR);
    }

    public function setHeader()
    {
        $this->header = array();
        $this->header[] = "Authorization: Basic VWx1c25ldDo1UFJrNmpS";
    }

    public function setUrl()
    {
        $this->url = 'http://172.27.40.8:8080/ulusnet-service/main';
    }

    public function getXmlResponse()
    {
        return $this->xml_response;
    }

    public function getXmlResponseRaw()
    {
        if ($this->defaultResponse) {
            return $this->xml_response_raw;
        } else {
            $xml = '<Response>';
            $xml.= '<HttpCode>' . $this->responseCode . '</HttpCode>';
            if ($this->responseCode == 200) {
                $xml.= '<isSuccessful>1</isSuccessful>';
            } else {
                $status = $this->command == 1 && $this->responseCode == 404 ? 1 : 0;
                $xml.= '<isSuccessful>' . $status . '</isSuccessful>';
            }
            $xml.= '<Result>';
            if ($this->htmlSpecialChar) {
                $xml.= htmlspecialchars($this->xml_response_raw);
            } else {
                $xml.= $this->xml_response_raw;
            }
            $xml.= '</Result>';
            $xml.= '</Response>';

            return $xml;
        }
    }

    public function getXmlRequest()
    {
        return $this->xmlRequest;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
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

    public function logAccess()
    {
        $re = '';
        $sql = "INSERT INTO bankgw_log.`log_gateway_ulusnet` (`number` ,`type_s`, `user_id`, `request_xml` ,`response_xml`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "', '" . $this->xmlRequest . "', '" . $re . "');";

        $this->mysqlExecute($sql);
    }

    public function logAccessUpdate($text = "")
    {
        if ($text != "") {
            $str = $text;
        } else {
            $str = $this->xml_response_raw;
        }
        $now = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $sql = "UPDATE bankgw_log.`log_gateway_ulusnet` SET `response_xml` = '" . $str . "', `updated_at` = '". $now ."' WHERE id = " . $this->logId;

        $this->mysqlExecute($sql);
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/gw-log/Basic-' . date("Ymd") . '.log'));
        $logger->log('logAccessUpdate -- sql='. $sql . '; time: ' . $now, sfFileLogger::INFO);
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $this->logAccess();
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost();

        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
            $this->xml_response_raw = "<Response>
    <HttpCode>100</HttpCode>
    <Result>Response is empty</Result>
</Response>";
            $this->logAccessUpdate($this->error_msg);
            return false;
        }
        $this->track .= "Задлаж байна <br />";
        $this->track .= "Лог бүртгэж байна <br />";
        $this->logAccessUpdate();
        if ($this->isValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function getClassName()
    {
        return get_class($this);
    }

    public function parse()
    {
        $options = array(
            "indent" => "    ",
            "linebreak" => "\n",
            "typeHints" => false,
            "addDecl" => true,
            "encoding" => "UTF-8",
            "rootAttributes" => array("version" => "1.0"),
            "defaultTagName" => "item",
            "attributesArray" => "_attributes"
        );

        $serializer = new XML_Serializer($options);

        $result = $serializer->serialize($this->xml_response_raw);
        if ($result === true) {
            $this->xml_response .= ($serializer->getSerializedData());
        }
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        // execute the connexion
        $result = curl_exec($ch);
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            $this->responseCode = $info['http_code'];
        }
        if ($this->command == 1) {
            $responseXml = simplexml_load_string($result, 'SimpleXMLElement');

            $json = json_encode($responseXml);
            $response = json_decode($json, TRUE);

            $result = '<ServiceResponse>';
            if ($response['Code'] == 0) {
                $result .= '<isSuccessful>1</isSuccessful>';
            } else {
                $result .= '<isSuccessful>0</isSuccessful>';
            }
            $result .= '<Status>' . $response['Code'] . '</Status>';
            $result .= '<Description>' . $response['Info'] . '</Description>';
            $result .= '</ServiceResponse>';
        }
        // Close it
        curl_close($ch);
        return $result;
    }

    public function getRequestId()
    {
        return round(microtime(true) * 1000);
    }

}
