<?php

class Basic_Sapc
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
    protected $orderId;
    protected $status;
    protected $command = 0;
    protected $timeout;

    const SUCCESSFUL = 1;
    const FAILED = 2;

    public function __construct()
    {
        $this->setHeader();
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_sapc.yml');
        
        $timeout = $yml['all']['sapc']['curl_timeout'];
        $url = $yml['all']['sapc']['api'];
        $username = $yml['all']['sapc']['username'];
        $password = $yml['all']['sapc']['password'];
        $this->setUrl($url);
        $this->setHeader($username, $password);
        $this->setTimeout($timeout);

        error_reporting(E_ERROR);
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
        $this->header[] = "username: $username";
        $this->header[] = "password: $password";
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

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
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
        $sql = "INSERT INTO bankgw_log.`log_gateway_sapc` (`number` ,`type_s`, `user_id`, `order_id`, `request_xml`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "', '" . $this->orderId . "', '" . (print_r($this->xmlRequest, true)) . "');";
        $this->mysqlExecute($sql);
    }

    public function logAccessUpdate($logId, $text = "")
    {
        $pdo = LogTools::getLogPDO();
        $sql = "UPDATE bankgw_log.`log_gateway_sapc` SET `response_xml` = :text, `updated_at` = :date WHERE id = :logId";
        $sth = $pdo->prepare($sql);
        $now = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $sth->execute(array(':text' => $this->xml_response_raw, ':date' => $now, ':logId' => $logId));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/gw-log/basicSapc-' . date("Ymd") . '.log'));
        $logger->log('logAccessUpdate -- id='. $logId . '; sql='. $sql . '; time: ' . $now, sfFileLogger::INFO);
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $this->xml_response_raw = $this->httpsPost();
        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
        }
        $this->track .= "Задлаж байна <br />";
        $this->track .= "Лог бүртгэж байна <br />";
        $this->logAccess();

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
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xmlRequest);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
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

        if ($this->command == 1) {
            $responseXml = simplexml_load_string($result, 'SimpleXMLElement');

            $json = json_encode($responseXml);
            $response = json_decode($json, TRUE);

            $result = '<SAPCProvisionResponse>';
            $result .= '<Code>' . $response['Code'] . '</Code>';
            if ($response['Description'] == 'R_BOUGHTPACKAGE') {
                $result .= '<isSuccessful>1</isSuccessful>';
            } else {
                $result .= '<isSuccessful>0</isSuccessful>';
            }
            $result .= '<Description>' . $response['Description'] . '</Description>';
            $result .= '<Msisdn>' . $response['MSISDN'] . '</Msisdn>';

            if (isset($response['Package'])) {
                $result .= '<Package>' . print_r($response['Package'], true) . '</Package>';
            }
            if (isset($response['SMS'])) {
                $result .= '<SMS>' . $response['SMS'] . '</SMS>';
            }

            $result .= '</SAPCProvisionResponse>';
        }

        return $result;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}

?>