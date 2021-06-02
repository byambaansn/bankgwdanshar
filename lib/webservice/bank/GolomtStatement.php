<?php

/**
 *  Банкнаас хуулга хүлээж авах 
 *
 * @author Belbayar
 */
class GolomtStatement extends BaseGateway
{

    private $conn;
    private $error_msg;
    private $vendorId;
    private $invoiceId;

    public function __construct()
    {
        parent::__construct();
        # parse symfony config
        $database = sfYaml::load(dirname(__FILE__) . '/../../../config/databases.yml');
        $dsn = $database['all']['doctrine']['param']['dsn'];
        preg_match('/host=(.*);(\s)*dbname=(.*);/', $dsn, $host);
        if ($host[1] && $host[3]) {
            # connection
            $this->error_msg = '';
            $this->conn = new BaseConnection(array(
                'host' => $host[1],
                'username' => $database['all']['doctrine']['param']['username'],
                'password' => $database['all']['doctrine']['param']['password'],
                'database' => $host[3],
            ));
        } else {
            die('Wrong host.');
        }
    }

    public function doParse(sfWebRequest $request)
    {
        $this->requestXml = self::fixDescValue($request->getContent());
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/GolomtStatementdoParse.log'));
        $logger->log('--start--', sfFileLogger::INFO);
        $logger->log('--requestXml--' . $this->requestXml, sfFileLogger::INFO);
        // parse request xml
        $xml = simplexml_load_string($this->requestXml);
        $this->requestXmlArray = $this->xmlToArray($xml);
        $logger->log('--requestXmlArray--' . print_r($this->requestXmlArray, TRUE), sfFileLogger::INFO);

        $this->remoteAddress = $request->getRemoteAddress();

        // set access log
        $logQuery = "INSERT INTO bankgw_log.`log_bank_call` (`function`, `ip` ,`request`) VALUES ('" . get_class($this) . "', '" . $this->remoteAddress . "', '" . mysql_escape_string($this->requestXml) . print_r($this->requestXmlArray, true) . "')";
        $this->logId = LogTools::executeGetId($logQuery);

        if ($xml === FALSE) {
            $this->doStop('101', 'Post parameter not found');
        }


        // set remote address

        $allowedTags = array(
            'username' => 'string',
            'password' => 'string',
            'number' => 'numeric', //4Гүйлгээний дугаар
            'amount' => 'numeric', // Мөнгөн дүн
            'value' => 'string', // Гүйлгээний утга
            'date' => 'datetime', //  Гүйлгээний огноо
            'type' => 'string', //  Гүйлгээний төрөл
            'order_branch' => 'string', //  Гүйлгээний хийсэн салбар
            'channel' => 'string', //  Гүйлгээний сувгийн мэдээлэл
            'account' => 'numeric', //  Дансны дугаар
        );

        $this->doValidate($allowedTags);
    }

    public function xmlToArray($xml)
    {
        $request = array();
        if (isset($xml->username[0])) {
            $request['username'] = (string) $xml->username[0];
        }
        if (isset($xml->password[0])) {
            $request['password'] = (string) $xml->password[0];
        }
        if (isset($xml->number[0])) {
            $request['number'] = (string) $xml->number[0];
        }
        if (isset($xml->amount[0])) {
            $request['amount'] = (string) $xml->amount[0];
        }
        if (isset($xml->value[0])) {
            $request['value'] = (string) $xml->value[0];
        }

        if (isset($xml->date[0])) {
            $request['date'] = (string) $xml->date[0];
        }

        if (isset($xml->type[0])) {
            $request['type'] = (string) $xml->type[0];
        }

        if (isset($xml->order_branch[0])) {
            $request['order_branch'] = (string) $xml->order_branch[0];
        }

        if (isset($xml->channel[0])) {
            $request['channel'] = (string) $xml->channel[0];
        }

        if (isset($xml->account[0])) {
            $request['account'] = (string) $xml->account[0];
        }
        return $request;
    }

    public function doValidate($allowedTags)
    {
        $requestTags = array_keys((array) $this->requestXmlArray);

        $missedTags = array_merge(array_diff(array_keys($allowedTags), $requestTags));

        function is_datetime($data)
        {
            if (date('Y/m/d H:i:s', strtotime($data)) == $data || date('Y-m-d H:i:s', strtotime($data)) == $data) {
                return true;
            } else {
                return false;
            }
        }

        if (sizeof($missedTags)) {
            $this->throwError(100, ' missing tag "' . $missedTags[0] . '"');
        } else {
            foreach ($allowedTags as $tag => $type) {
                if (!is_array($type)) {
                    if (!call_user_func("is_$type", $this->requestXmlArray[$tag])) {
                        $this->throwError(100, 'invalid value in "' . $tag . '" tag!');
                    }
                } else {
                    if (!in_array($this->requestXmlArray[$tag], $type)) {
                        $this->throwError(100, 'invalid value in "' . $tag . '" tag!');
                    }
                }
            }
        }
    }

    public function throwError($code = null, $info = null)
    {
        $res = array();

        if ($code) {
            $res['code'] = $code;
        } else {
            $res['code'] = 404;
        }

        if ($info) {
            $res['info'] = $info;
        } else {
            $res['info'] = 'Error!';
        }

        header("Content-Type:text/xml");

        $this->setResponseXml($res);
        echo $this->responseXml;
        die();
    }

    public function doProcess()
    {
        # vendor
        $sql = "SELECT vu.vendor_id
                FROM vendor_user vu
                WHERE  vu.vendor_id=" . VendorTable::GOLOMT . " AND  vu.username = '" . mysql_escape_string($this->requestXmlArray['username']) . "'
                    AND vu.password = '" . mysql_escape_string($this->requestXmlArray['password']) . "'
                LIMIT 1";
        $vendor = $this->conn->mysqlFetchOne($sql);

        # is not vendor
        if (!$vendor) {
            $result['code'] = 101;
            $result['info'] = 'invalid username or password';
        }
        # is vendor
        else {
            $this->vendorId = $vendor['vendor_id'];
            # IP
            $sql = "SELECT ip.id
                    FROM vendor_ip ip
                    WHERE ip.ip_address = '" . $this->remoteAddress . "'
                        AND ip.vendor_id = '" . $vendor['vendor_id'] . "'
                    LIMIT 1";

            # allowed IP
            if ($this->conn->mysqlFetchOne($sql) || in_array($this->remoteAddress, array('127.0.0.1', '172.30.14.101', '192.168.0.60', '192.168.0.199'))) {
                if ($this->error_msg) {
                    $result['code'] = 103;
                    $result['info'] = $this->error_msg;
                } else {
                    $this->invoiceId = $this->requestXmlArray['number'];
//                    $bankGolomt = BankGolomtTable::getByOrderId($this->invoiceId);
//                    if ($bankGolomt) {
//                        $this->error_msg = 'Statement with invoice_id ' . $this->invoiceId . ' already exists.';
//                    }
                    if ($this->error_msg) {
                        $result['code'] = 103;
                        $result['info'] = $this->error_msg;
                    }
                    if (strlen($this->invoiceId) > 30) {
                        $result['code'] = 103;
                        $this->error_msg = "Invalid invoice id.";
                        $result['info'] = $this->error_msg;
                    }

                    if (!$this->error_msg) {
                        $trans = array();
                        $trans['JournalNo'] = $this->requestXmlArray['number'];
                        $trans['Account'] = $this->requestXmlArray['account'];
                        $trans['TxnDesc'] = $this->requestXmlArray['value'];
                        $trans['TxnType'] = $this->requestXmlArray['type'];
                        $trans['Amount'] = $this->requestXmlArray['amount'];
                        $trans['TxnDate'] = $this->requestXmlArray['date'];
                        $trans['Branch'] = $this->requestXmlArray['order_branch'];
                        $trans['Channel'] = $this->requestXmlArray['channel'];

                        try {
                            $bankGolomt = BankGolomtTable::insert($trans);
                            if ($bankGolomt) {
                                $result['code'] = 0;
                                $result['info'] = 'SUCCESS';
                            } else {
                                $result['code'] = 103;
                                $result['info'] = 'Error( not inserted)';
                            }
                        } catch (Exception $exc) {
                            $result['code'] = 104;
                            $result['info'] = 'Fatal error(' . $exc->getMessage() . ')';
                            $logQuery = "INSERT INTO bankgw_log.`log_golomt_init_error` (`bank_account`, `bank_order_id` ,`bank_order_p` ,`order_date` ,`order_type` ,`order_amount` ,`comment`) VALUES ('" . $trans['Account'] . "', '" . $trans['JournalNo'] . "', '" . $trans['TxnDesc'] . "', '" . $trans['TxnDate'] . "', '" . $trans['TxnType'] . "', '" . $trans['Amount'] . "', '" . $result['info'] . "')";
                            LogTools::execute($logQuery);
                        }
                    }
                }
            }
            # not allowed IP
            else {
                $result['code'] = 102;
                $result['info'] = 'invalid IP address' . $this->remoteAddress;
            }
        }
        $this->setResponseXml($result);
    }

    public function setResponseXml($array)
    {
        $array = (array) $array;

        $xml = AppTools::arrayToXml($array);

        $this->responseXml = $xml->asXML();

        // set access log
        LogTools::execute("UPDATE bankgw_log.log_bank_call SET `response` = '" . mysql_escape_string($this->responseXml) . "' WHERE `id` = " . $this->logId . " LIMIT 1");
    }

    /**
     * Removes invalid XML
     *
     * @access public
     * @param string $value
     * @return string
     */
    function fixDescValue($value)
    {
        $matches = '';
        $regex = '#<\s*?value\b[^>]*>(.*?)</value\b[^>]*>#s';
        preg_match($regex, $value, $matches);
        if ($matches[1]) {
            $desc = $matches[1];
            $ret = "";
            $current;
            $length = strlen($desc);
            for ($i = 0; $i < $length; $i++) {
                $current = $desc{$i};
                switch ($current) {
                    case '"':
                        $ret .= '&quot;';
                        break;
                    case '\'':
                        $ret .= '&apos;';
                        break;
                    case '<':
                        $ret .= '&lt;';
                        break;
                    case '>':
                        $ret .= '&gt;';
                        break;
                    case '&':
                        $ret .= '&amp;';
                        break;
                    default :
                        $ret .= $current;
                        break;
                }
            }
            $ret = "<value>" . $ret . "</value>";
            $value = preg_replace($regex, $ret, $value);
        }
        return $value;
    }

}

?>
