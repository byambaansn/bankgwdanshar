<?php
/**
 * Description of Basic VatSender
 *
 * @author sukhbaatar.e
 */
class Basic_VatSender
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
    protected $appName = 'BankGW';
    protected $responseCode = 0;
    protected $customRequest = '';
    protected $htmlSpecialChar = 0;
    protected $userPwd = '';
    protected $defaultResponse = false;
    protected $command = 0;
    protected $isProd = 1;
    protected $hasHeader = true;
    protected $customHeader = false;
    protected $manufacture = 1; # Mobicom
    protected $manufactures = array();

    const SERVER = "";
    const SUCCESSFUL = 1;
    const FAILED = 2;

    public function __construct($manufacture = 1)
    {
        $this->manufacture = $manufacture;

        $this->setManufactures();
        $this->setHeader();
        $this->setUrl();
        error_reporting(E_ERROR);
    }

    public function setManufactures()
    {
        $this->manufactures = array();
        $this->manufactures[1] = 80;
        $this->manufactures[2] = 84;
        $this->manufactures[3] = 81;
        $this->manufactures[4] = 83;
        $this->manufactures[7] = 85;
    }

    public function setHeader()
    {
        $this->header = array();
        $this->userPwd = '';

        if ($this->hasHeader) {
            if ($this->customHeader) {
                 $this->header[] = "Content-Type: application/json";
            } else {
                $this->header[] = "Content-Type: application/xml";
            }
            $this->header[] = "X-Interface: " . $this->appName;
            $this->header[] = "X-Request-Id: " . $this->getRequestId();
            
            
            $this->header[] = 'Accept-Encoding: gzip, deflate';
            if ($this->isProd == 1) {
                $this->header[] = "Authorization: Basic dV9zbXM6RG9yakR1bGFtIUAx";
            } else {
//                $this->header[] = "Authorization: Basic dV9zbXM6RG9yakR1bGFtIUAx";
            }
        }
    }

    public function setUrl()
    {
        $port = 80; # default port
        if (isset($this->manufactures[$this->manufacture])) {
            $port = $this->manufactures[$this->manufacture];
        }

        if ($this->isProd == 1) {
            $this->url = "http://192.168.8.37:" . $port . "/VatSender";
        } else {
            $this->url = "http://172.22.2.58:44455/VatSender";
        }
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


    public function logAccess($start, $end)
    {
        $pdo = LogTools::getLogPDO();

        $rt = (print_r($this->xmlRequest, true));
        $re = (print_r($this->xml_response_raw, true));

        $rt = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
            return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
        }, $rt);

        $rt = str_replace("'", "\'", $rt);
        $re = str_replace("'", "\'", $re);

        $sql = "INSERT INTO bankgw_log.`log_gateway_vat` (`number` ,`type_s`, `user_id`, `response_code`, `url`, `request_xml` ,`response_xml`, `created_at`, `updated_at`)VALUES ('" . $this->number . "', '" . $this->getClassName() . "', '" . $this->userId . "'," . $this->responseCode . ", '" . $this->url . "', '" . $rt . "', '" . $re . "', '". $start ."', '". $end ."');";
        $pdo->exec($sql);
    }

    public function isValid()
    {
        return true;
    }

    public function call()
    {
        $this->setHeader();
        $this->track .= "Call function дуудагдав <br /> XML ийг мобикомоос дуудаж байна <br />";
        $start = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $this->xml_response = $this->httpsPost();
        $end = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u");
        $this->xml_response_raw = $this->xml_response;
        if ($this->xml_response_raw) {
            $this->track .= "Хариу ирсэн <br />";
        } else {
            $this->error_msg .= "Хариу ирсэнгүй <br />";
        }
        $this->track .= "Задлаж байна <br />";
        $this->track .= "Лог бүртгэж байна <br />";
        $this->logAccess($start, $end);

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