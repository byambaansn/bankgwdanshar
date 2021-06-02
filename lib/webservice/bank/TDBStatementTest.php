<?php

/**
 *  Банкнаас хуулга хүлээж авах 
 *
 * @author Enkhaikhan.da
 */
class TDBStatementTest extends BaseGateway
{

    private $conn;
    private $error_msg;
    private $vendorId;
    private $invoiceId;
    private $invoiceIdSub;

    public function __construct()
    {
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
        $this->requestXml = $request->getContent();

        // parse request xml
        $xml = simplexml_load_string($this->requestXml);

//        $json = json_encode($xml);
//        $this->requestXmlArray = json_decode($json, TRUE);
        $this->requestXmlArray = $this->xmlToArray($xml);

        $this->remoteAddress = $request->getRemoteAddress();

        // set access log
        $logQuery = "INSERT INTO bankgw_log.`log_bank_call` (`function`, `ip` ,`request`) VALUES ('" . get_class($this) . "', '" . $this->remoteAddress . "', '" . mysql_escape_string($this->requestXml) . "')";
        $this->logId = LogTools::executeGetId($logQuery);

        if ($xml === FALSE) {
            $this->doStop('101', 'Post parameter not found');
            die('end');
        }
        // set remote address
//        $this->doValidate($allowedTags);
    }

    public function xmlToArray($xml)
    {
        $request = array();
        if (isset($xml->Username[0])) {
            $request['Username'] = (string) $xml->Username[0];
        }
        if (isset($xml->Password[0])) {
            $request['Password'] = (string) $xml->Password[0];
        }
        if (isset($xml->Journal[0])) {
            $request['Journal'] = (string) $xml->Journal[0];
        }
        if (isset($xml->JournalItem[0])) {
            $request['JournalItem'] = (string) $xml->JournalItem[0];
        }
        if (isset($xml->Date[0])) {
            $request['Date'] = (string) $xml->Date[0];
        }

        if (isset($xml->TxnType[0])) {
            $request['TxnType'] = (string) $xml->TxnType[0];
        }

        if (isset($xml->Account[0])) {
            $request['Account'] = (string) $xml->Account[0];
        }

        if (isset($xml->Amount[0])) {
            $request['Amount'] = (string) $xml->Amount[0];
        }

        if (isset($xml->Currency[0])) {
            $request['Currency'] = (string) $xml->Currency[0];
        }

        if (isset($xml->Desc[0])) {
            $request['Desc'] = (string) $xml->Desc[0];
        }

        if (isset($xml->Branch[0])) {
            $request['Branch'] = (string) $xml->Branch[0];
        }

        if (isset($xml->Teller[0])) {
            $request['Teller'] = (string) $xml->Teller[0];
        }
        if (isset($xml->Source[0])) {
            $request['Source'] = (string) $xml->Source[0];
        }

        return $request;
    }

    public function doValidate($allowedTags)
    {
//        $requestTags = array_keys((array) $this->requestXmlArray);
//
//
//        $missedTags = array_merge(array_diff(array_keys($allowedTags), $requestTags));
//
//        if (sizeof($missedTags)) {
//            $this->throwError(100, ' missing tag "' . $missedTags[0] . '"');
//        } else {
//            foreach ($allowedTags as $tag => $type) {
//                if (!is_array($type)) {
//                    if (!call_user_func("is_$type", $this->requestXmlArray[$tag])) {
//                        $this->throwError(100, 'invalid value in "' . $tag . '" tag!');
//                    }
//                } else {
//                    if (!in_array($this->requestXmlArray[$tag], $type)) {
//                        $this->throwError(100, 'invalid value in "' . $tag . '" tag!');
//                    }
//                }
//            }
//        }
    }

    public function throwError($code = null, $info = null)
    {
        $res = array();

        if ($code) {
            $res['FaultCode'] = $code;
        } else {
            $res['FaultCode'] = 404;
        }

        if ($info) {
            $res['FaultString'] = $info;
        } else {
            $res['FaultString'] = 'Error!';
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
                WHERE  vu.vendor_id=" . VendorTable::BANK_TDB . " AND  vu.username = '" . $this->requestXmlArray['Username'] . "'
                    AND vu.password = '" . $this->requestXmlArray['Password'] . "'
                LIMIT 1";
        $vendor = $this->conn->mysqlFetchOne($sql);

        # is not vendor
        if (!$vendor) {
            $result['FaultCode'] = 101;
            $result['FaultString'] = 'invalid username or password';
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
            if ($this->conn->mysqlFetchOne($sql) || in_array($this->remoteAddress, array('127.0.0.1', '172.30.14.101'))) {
                if ($this->error_msg) {
                    $result['FaultCode'] = 103;
                    $result['FaultString'] = $this->error_msg;
                } else {
                    $transaction = $this->requestXmlArray;
                    $invoiceId = $transaction['Journal'];
                    $invoiceIdSub = $transaction['JournalItem'];
                    $type = $transaction['TxnType'];
                    if (!$invoiceId || !$invoiceIdSub) {
                        $result['FaultCode'] = 103;
                        $this->error_msg = "Invalid invoice_id.";
                    }
                    if ($this->error_msg) {
                        $result['FaultString'] = $this->error_msg;
                    } else {
                        if (BankTdbTable::getByOrderId($invoiceId, $invoiceIdSub, $type)) {
                            $this->error_msg = 'Statement with Journal ' . $invoiceId . '(' . $invoiceIdSub . ') already exists.';
                            $result['FaultCode'] = 105;
                            $result['FaultString'] = $this->error_msg;
                        } else {
                            $trans = array();
                            $trans['JournalNo'] = $transaction['Journal'];
                            $trans['JournalNoSub'] = $transaction['JournalItem'];
                            $trans['Account'] = $transaction['Account'];
                            $trans['TxnDesc'] = base64_decode($transaction['Desc']);
                            $trans['TxnType'] = $transaction['TxnType'];
                            $trans['Amount'] = $transaction['Amount'];
                            $trans['TxnDate'] = $transaction['Date'];
                            $trans['Currency'] = $transaction['Currency'];
                            $trans['Branch'] = $transaction['Branch'];
                            $trans['Teller'] = $transaction['Teller'];
                            $trans['Channel'] = $transaction['Source'];

                            try {
                                $bankTdb = $this->insert($trans);
                                if ($bankTdb) {
                                    $result['FaultCode'] = 0;
                                    $result['FaultString'] = 'SUCCESS';
                                } else {
                                    $result['FaultCode'] = 106;
                                    $result['FaultString'] = 'Error( not inserted)';
                                }
                            } catch (Exception $exc) {
                                $result['FaultCode'] = 104;
                                $result['FaultString'] = 'Fatal error(' . $exc->getMessage() . ')';
                                $logQuery = "INSERT INTO bankgw_log.`log_tdb_init_error` (`bank_account`, `bank_order_id` ,`bank_order_p` ,`order_date` ,`order_type` ,`order_amount` ,`comment`) VALUES ('" . $trans['Account'] . "', '" . $trans['JournalNo'] . "', '" . $trans['TxnDesc'] . "', '" . $trans['TxnDate'] . "', '" . $trans['TxnType'] . "', '" . $trans['Amount'] . "', '" . $result['FaultString'] . "')";
                                LogTools::execute($logQuery);
                            }
                        }
                    }
                }
            }
            # not allowed IP
            else {
                $result['FaultCode'] = 102;
                $result['FaultString'] = 'invalid IP address' . $this->remoteAddress;
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
     * Stop
     * 
     * @param string $code
     * @param string $info
     */
    public function doStop($code = null, $info = null)
    {
        $res = array();

        if ($code) {
            $res['FaultCode'] = $code;
        } else {
            $res['FaultCode'] = 404;
        }

        if ($info) {
            $res['FaultString'] = $info;
        } else {
            $res['FaultString'] = 'Error!';
        }

        header("Content-Type:text/xml");

        $xml = AppTools::arrayToXml($res);
        if ($this->logId) {
            $query = "UPDATE `log_bank_call` SET `response` = '" . mysql_escape_string(print_r($xml, true)) . "' WHERE `id` = " . $this->logId . " LIMIT 1";
        } else {
            $query = "INSERT INTO `log_bank_call` (`function`, `ip` ,`request`) VALUES ('" . get_class($this) . "', '" . $this->remoteAddress . "', '" . mysql_escape_string(print_r($res, true)) . "')";
        }
        LogTools::executeGetId($query);
        echo $xml->asXML();
        die();
    }

    public function insert($trans)
    {
        $bankOrder = new BankTdb();
        $bankOrder->order_id = $trans['JournalNo'];
        $bankOrder->order_id_sub = $trans['JournalNoSub'];
        $bankOrder->bank_account = $trans['Account'];
        $bankOrder->order_p = $trans['TxnDesc'];
        $bankOrder->order_type = $trans['TxnType'];
        $bankOrder->order_amount = $trans['Amount'];
        $bankOrder->order_date = $trans['TxnDate'];
        $bankOrder->order_s = $trans['Branch'];
        $bankOrder->order_channel = $trans['Channel'];
        $bankOrder->order_teller = $trans['Teller'];
        $bankOrder->status = 100;
        $bankOrder->vendor_id = VendorTable::BANK_TDB;
        $bankOrder->created_at = date('Y-m-d H:i:s');
        $bankOrder->transfer_sap = 1;

        $txnDesc = preg_replace("/\([0-9]{8}\)/", "", $trans['TxnDesc']);

        preg_match_all("/([9][954][0-9]{6})|(85[0-9]{6})/", $txnDesc, $matches);
        foreach ($matches as $numberArr) {
            foreach ($numberArr as $number) {
                $number = trim($number);
                if ($number) {
                    $bankOrder->order_mobile = $number;
                    $bankOrder->charge_mobile = $number;
                    continue;
                }
            }
        }
        $bankOrder->save();
        return $bankOrder;
    }

}

?>
