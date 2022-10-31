<?php

class Basic_WifiOnline
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
    protected $appName = 'BANKGW';
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
//        error_reporting(E_ERROR);
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_wifi.yml');
        $url = $yml['all']['wifi']['api'];
        $username = $yml['all']['wifi']['username'];
        $password = $yml['all']['wifi']['password'];
        $this->setUrl($url);
        $this->setHeader($username, $password);
    }

    public function setHeader($username, $password)
    {
        $this->header = array();
        $this->header[] = "Content-Type: application/xml";
        $this->header[] = "X-Interface: " . $this->appName;
        $this->header[] = "X-Request-Id: " . $this->getRequestId();

        $this->header[] = "user: $username";
        $this->header[] = "password: $password";
        $this->userPwd = "$username:$password";
    }

    public function setUrl($url)
    {
        $this->url = $url;
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

    public function logAccess()
    {
        $pdo = LogTools::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_gateway_sapc (`number`, `user_id`, `type_s`, `request_xml`, `created_at`)
                VALUES ('" . $this->number . "','" . $this->userId . "','" . $this->getClassName() . "', '" . $this->xmlRequest . "', '" . date('Y-m-d H:i:s') . "')";
        $pdo->exec($sql);
        return $pdo->lastInsertId();
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_sapc` SET `response_xml` = :text, `updated_at` = :date WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $now = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $sth->execute(array(':text' => $this->xml_response_raw, ':date' => $now, ':logId' => $logId));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/gw-log/basicWifiOnline-' . date("Ymd") . '.log'));
        $logger->log('logAccessUpdate -- id='. $logId . '; sql='. $sql . '; time: ' . $now, sfFileLogger::INFO);
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $logId = $this->logAccess();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost();
        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
        }
        $this->track .= "Задлаж байна <br />";
        $this->track .= "Лог бүртгэж байна <br />";

        $this->logAccessUpdate($logId);
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
//        curl_setopt($ch, CURLOPT_HTTPHEADER, false);
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

        // Close it
        curl_close($ch);
        return $result;
    }

    public function getRequestId()
    {
        return round(microtime(true) * 1000);
    }

    public function sentInfo()
    {
        echo $this->url;
        echo '<br>';
        echo $this->xmlRequest;
        die();
    }

}

?>