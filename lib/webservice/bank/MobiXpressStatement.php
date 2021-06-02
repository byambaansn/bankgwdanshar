<?php

/**
 *  MobiXpress хуулга хүлээж авах 
 *
 * @author Belbayar
 */
class MobiXpressStatement extends BaseGateway
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
        $requestArr = array();
        if ($request->getParameter('username')) {
            $requestArr['username'] = $request->getParameter('username');
        }
        if ($request->getParameter('password', 0)) {
            $requestArr['password'] = $request->getParameter('username', 0);
        }
        if ($request->getParameter('number', 0)) {
            $requestArr['number'] = (int) $request->getParameter('number', 0);
        }
        if ($request->getParameter('amount', 0)) {
            $requestArr['amount'] = $request->getParameter('amount', 0);
        }
        if ($request->getParameter('value', 0)) {
            $requestArr['value'] = $request->getParameter('value', 0);
        }
        if ($request->getParameter('date', 0)) {
            $requestArr['date'] = $request->getParameter('date');
        }
        if ($request->getParameter('order_branch', 0)) {
            $requestArr['order_branch'] = $request->getParameter('order_branch', 0);
        }
        if ($request->getParameter('channel', 0)) {
            $requestArr['channel'] = $request->getParameter('channel', 0);
        }
        if ($request->getParameter('account', 0)) {
            $requestArr['account'] = $request->getParameter('account', 0);
        }

        $this->requestXml = $requestArr;
        $this->requestXmlArray = $requestArr;

        // set remote address
        $this->remoteAddress = $request->getRemoteAddress();
        $logQuery = "INSERT INTO bankgw_log.`log_bank_call` (`function`, `ip` ,`request`) VALUES ('" . get_class($this) . "', '" . $this->remoteAddress . "', '" . mysql_escape_string(print_r($this->requestXmlArray, true)) . "')";

        // set access log
        $this->logId = LogTools::executeGetId($logQuery);

        if (count($requestArr) != 9) {
            $this->doStop(0, 'Parameter missing');
        }

        $allowedTags = array(
            'username' => 'string',
            'password' => 'string',
            'number' => 'numeric', //4Гүйлгээний дугаар
            'amount' => 'numeric', // Мөнгөн дүн
            'value' => 'string', // Гүйлгээний утга
            'date' => 'string', //  Гүйлгээний огноо
            'order_branch' => 'string', //  Гүйлгээний хийсэн салбар
            'channel' => 'string', //  Гүйлгээний сувгийн мэдээлэл
            'account' => 'numeric', //  Дансны дугаар
        );

        $this->doValidate($allowedTags);
    }

    public function doValidate($allowedTags)
    {
        $requestTags = array_keys((array) $this->requestXmlArray);

        $missedTags = array_merge(array_diff(array_keys($allowedTags), $requestTags));

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
                WHERE  vu.vendor_id=" . VendorTable::MOBIXPRESS . " AND vu.username = '" . $this->requestXmlArray['username'] . "'
                    AND vu.password = '" . $this->requestXmlArray['password'] . "'
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
            if ($this->conn->mysqlFetchOne($sql) || in_array($this->remoteAddress, array('127.0.0.1', '172.30.14.101'))) {
                if ($this->error_msg) {
                    $result['code'] = 103;
                    $result['info'] = $this->error_msg;
                } else {
                    $this->invoiceId = $this->requestXmlArray['number'];
                    $bankMobixpress = BankMobixpressTable::getByOrderId($this->invoiceId);
                    if ($bankMobixpress) {
                        $this->error_msg = 'Statement with invoice_id ' . $this->invoiceId . ' already exists.';
                    }
                    if ($this->error_msg) {
                        $result['code'] = 103;
                        $result['info'] = $this->error_msg;
                    }
                    if (strlen($this->invoiceId) > 16) {
                        $result['code'] = 103;
                        $this->error_msg = "Invalid invoice_id.";
                    }

                    if (!$this->error_msg) {
                        $orderId = $this->requestXmlArray['number'];
                        $orderAmount = $this->requestXmlArray['amount'];
                        $orderValue = $this->requestXmlArray['value'];
                        $orderDate = $this->requestXmlArray['date'];
                        $orderBranch = $this->requestXmlArray['order_branch'];
                        $orderChannel = $this->requestXmlArray['channel'];
                        $account = $this->requestXmlArray['account'];

                        try {
                            $bankMobixpress = new BankMobixpress();
                            $bankMobixpress->setOrderId($orderId);
                            $bankMobixpress->setVendorId(BankTable::MOBIXPRESS);
                            $bankMobixpress->setOrderAmount($orderAmount);
                            $bankMobixpress->setOrderP($orderValue);
                            $bankMobixpress->setOrderMobile($orderValue);
                            $bankMobixpress->setChargeMobile($orderValue);
                            $bankMobixpress->setOrderDate($orderDate);
                            $bankMobixpress->setOrderS($orderBranch);
                            $bankMobixpress->setOrderType('ADD');
                            $bankMobixpress->setOrderChannel($orderChannel);
                            $bankMobixpress->setBankAccount($account);
                            $bankMobixpress->setCreatedAt(date('Y-m-d H:i:s'));
                            $bankMobixpress->save();
                            $result['code'] = 0;
                            $result['info'] = 'SUCCESS';
                        } catch (Exception $exc) {
                            $result['code'] = 104;
                            $result['info'] = 'Fatal error(' . $exc->getTraceAsString() . ')';
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
        LogTools::execute("UPDATE bankgw_log.log_bank_call SET `response` = '" . mysql_escape_string(print_r($array, true)) . "' WHERE `id` = " . $this->logId . " LIMIT 1");
    }

}

?>
