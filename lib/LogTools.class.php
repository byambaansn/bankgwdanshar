<?php

class LogTools
{

    const SERVER = '127.0.0.1'; //192.168.9.19
    const USERNAME = 'bankgw';
    const PASSWORD = 'B4H3sAWyDvsjC382';

    public static function getLogPDO()
    {
        $dsn = 'mysql:dbname=bankgw_log;host=' . self::SERVER;
        try {
            $dbh = new PDO($dsn, self::USERNAME, self::PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
            return $dbh;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        return false;
    }

    public static function execute($query)
    {
        $link = mysql_connect(self::SERVER, self::USERNAME, self::PASSWORD) or die(mysql_error());
        mysql_set_charset('utf8', $link) or die(mysql_error());
        $result = mysql_query($query, $link);
        mysql_close($link);

        return $result;
    }

    public static function executeGetId($query)
    {
        $link = mysql_connect(self::SERVER, self::USERNAME, self::PASSWORD) or die(mysql_error());
        mysql_set_charset('utf8', $link) or die(mysql_error());
        $result = mysql_query($query, $link);
        if ($result) {
            $result = mysql_insert_id($link);
        }
        mysql_close($link);

        return $result;
    }

    public static function executeFetchArray($query, $executeAsArray = false)
    {
        $link = mysql_connect(self::SERVER, self::USERNAME, self::PASSWORD) or die(mysql_error());
        mysql_set_charset('utf8', $link) or die(mysql_error());
        $result = mysql_query($query, $link);
        mysql_close($link);

        if ($executeAsArray) {
            $arr = array();
            while ($r = mysql_fetch_array($result)) {
                $arr[] = $r;
            }
            return $arr;
        }

        return mysql_fetch_array($result);
    }

    /**
     * Bank savings init
     */
    public static function setLogSavingsInit($request, $response, $responseSet, $errorCode, $desc, $get, $set, $username)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_savings_init(request, response, response_set, error_code, description, get_success, set_success, bank_user, ip)
                                            VALUES(:request,:response,:response_set,:error_code,:description,:get_success,:set_success,:bank_user,'{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(
                    ':request' => $request,
                    ':response' => $response,
                    ':response_set' => $responseSet,
                    ':error_code' => $errorCode,
                    ':description' => $desc,
                    ':get_success' => $get,
                    ':set_success' => $set,
                    ':bank_user' => $username));
    }

    /**
     * Bank savings charge
     */
    public static function setLogSavingsCharge($bankSavingsId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_savings_charge (bank_savings_id, ip)
                                            VALUES(:bank_savings_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_savings_id' => $bankSavingsId));
        return $pdo->lastInsertId();
    }

    public static function updateLogSavingsCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_savings_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank mobixpress charge
     */
    public static function setLogMobixpressCharge($bankOrderId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_mobixpress_charge (order_id, ip)
                                            VALUES(:order_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':order_id' => $bankOrderId));
        return $pdo->lastInsertId();
    }

    public static function updateLogMobixpressCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_mobixpress_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank Khaan init
     */
//    public static function setLogKhaanInit($request, $response, $responseSet, $errorCode, $desc, $get, $set)
//    {
//        $pdo = self::getLogPDO();
//        $sql = "INSERT INTO bankgw_log.log_khaan_init(request, response, response_set, error_code, description, get_success, set_success, ip)
//                                            VALUES(:request,:response,:response_set,:error_code,:description,:get_success,:set_success,'{$_SERVER['REMOTE_ADDR']}')";
//        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/my-khaan-order.log'));
//        $params = array(':request' => $request,
//            ':response' => $response,
//            ':response_set' => $responseSet,
//            ':error_code' => $errorCode,
//            ':description' => $desc,
//            ':get_success' => $get,
//            ':set_success' => $set);
//        $logger->log('--INIT--=' . $sql . print_r($params, true), sfFileLogger::INFO);
//        $stmt = $pdo->prepare($sql);
//        return $stmt->execute(array(
//                    ':request' => $request,
//                    ':response' => $response,
//                    ':response_set' => $responseSet,
//                    ':error_code' => $errorCode,
//                    ':description' => $desc,
//                    ':get_success' => $get,
//                    ':set_success' => $set));
//    }

    public static function setLogKhaanInit($request, $response, $responseSet, $errorCode, $desc, $get, $set)
    {
        $query = "INSERT INTO bankgw_log.log_khaan_init (request, response, response_set, error_code, description, get_success, set_success, ip)
              VALUES ('$request', 
                      '$response',
                      '$responseSet',
                      '$errorCode',
                      '$desc',
                      '$get',
                      '$set',
                      '" . $_SERVER['REMOTE_ADDR'] . "')";

        return self::execute($query);
    }

    /**
     * Bank khaan charge
     */
    public static function setLogKhaanCharge($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_khaan_charge (bank_khaan_id, ip)
                                            VALUES(:bank_khaan_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_khaan_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogKhaanCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_khaan_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank Golomt charge
     */
    public static function setLogGolomtCharge($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_golomt_charge (bank_golomt_id, ip)
                                            VALUES(:bank_golomt_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_golomt_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogGolomtCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_golomt_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank Capital charge
     */
    public static function setLogCapitalCharge($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_capital_charge (bank_capital_id, ip)
                                            VALUES(:bank_capital_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_capital_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogCapitalCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_capital_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank khaan charge callpayment
     */
    public static function setLogKhaanChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_khaan_charge_call_payment (bank_khaan_id, ip)
                                            VALUES(:bank_khaan_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_khaan_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogKhaanChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_khaan_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank Golomt charge callpayment
     */
    public static function setLogGolomtChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_golomt_charge_call_payment (bank_golomt_id, ip)
                                            VALUES(:bank_golomt_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_golomt_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogGolomtChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_golomt_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank khaan charge callpayment
     */
    public static function setLogSavingsChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_savings_charge_call_payment (bank_savings_id, ip)
                                            VALUES(:bank_savings_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_savings_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogSavingsChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_savings_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank capital charge callpayment
     */
    public static function setLogCapitalChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_capital_charge_call_payment (bank_capital_id, ip)
                                            VALUES(:bank_capital_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_capital_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogCapitalChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_capital_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    public static function setLogEmail($subject, $body, $from, $to)
    {
        if (is_array($from)) {
            $from = join(',', $from);
        }
        if (is_array($to)) {
            $to = join(',', $to);
        }
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_email (subject, body, from_address, to_address)
                                    VALUES(:subject, :body, :from, :to)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':subject' => $subject,
            ':body' => $body,
            ':from' => $from,
            ':to' => $to));
    }

    public static function setSmsOutcomeParam($data)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_test (result)
                                    VALUES(:result)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':result' => $data));
    }

    public static function getLogSavingsCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_savings_charge` WHERE `bank_savings_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogKhaanCharge($khaanBankId)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_khaan_charge` WHERE `bank_khaan_id`=$khaanBankId ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogGolomtCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_golomt_charge` WHERE `bank_golomt_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogXacCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_xac_charge` WHERE `bank_xac_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogCapitalCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_capital_charge` WHERE `bank_capital_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogTDBCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_tdb_charge` WHERE `bank_tdb_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogKhaanChargeCallPayment($khaanBankId)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_khaan_charge_call_payment` WHERE `bank_khaan_id`=$khaanBankId ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogGolomtChargeCallPayment($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_golomt_charge_call_payment` WHERE `bank_golomt_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogSavingsChargeCallPayment($savingsBankId)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_savings_charge_call_payment` WHERE `bank_savings_id`=$savingsBankId ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogXacChargeCallPayment($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_xac_charge_call_payment` WHERE `bank_xac_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogCapitalChargeCallPayment($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_capital_charge_call_payment` WHERE `bank_capital_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogTDBhargeCallPayment($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_tdb_charge_call_payment` WHERE `bank_tdb_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogMxCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_mx_charge` WHERE `order_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function getLogMobixpressCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_mobixpress_charge` WHERE `order_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    /**
     * Bank mobixpress charge
     */
    public static function setLogCandyCharge($bankOrderId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_candy_charge (order_id, ip)
                                            VALUES(:order_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':order_id' => $bankOrderId));
        return $pdo->lastInsertId();
    }

    public static function updateLogCandyCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_candy_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    public static function getLogCandyCharge($id)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response FROM `log_candy_charge` WHERE `order_id`=$id ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    /**
     * Bank  
     */
    public static function setMessageDealer($request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_candy_dealer_message (request, response,created_at,exec_time)
                                            VALUES(:request,:response,:created_at,:exec_time)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':request' => $request,
            ':response' => $response,
            ':created_at' => date('Y-m-d H:i:s'),
            ':exec_time' => $time));
        return $pdo->lastInsertId();
    }

    public static function setLogXacInit($request, $params = '', $response = '', $responseSet = '', $errorCode = '', $desc = '', $get = '', $set = '')
    {
        $query = "INSERT INTO bankgw_log.log_xac_init (request,request_param, response, response_set, error_code, description, get_success, set_success, ip)
              VALUES ('$request', 
                      '$params',
                      '$response',
                      '$responseSet',
                      '$errorCode',
                      '$desc',
                      '$get',
                      '$set',
                      '" . $_SERVER['REMOTE_ADDR'] . "')";

        return self::executeGetId($query);
    }

    public static function setLogXacInitUpdate($id, $fields)
    {
        $params = array();
        foreach ($fields as $key => $row) {
            $params[] = $key . "='" . $row . "'";
        }
        $sql = "UPDATE bankgw_log.log_xac_init SET " . implode(',', $params) . " WHERE id=" . $id;

        return self::execute($sql);
    }

    /**
     * Bank xac charge
     */
    public static function setLogXacCharge($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_xac_charge (bank_xac_id, ip)
                                            VALUES(:bank_xac_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_xac_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogXacCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_xac_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank xac charge callpayment
     */
    public static function setLogXacChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_xac_charge_call_payment (bank_xac_id, ip)
                                            VALUES(:bank_xac_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_xac_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogXacChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_xac_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * IN DEALER toc gateway log
     */
    public static function setLogGatewayTocGW($type, $request, $number)
    {
        $query = "INSERT INTO bankgw_log.log_gateway_toc_gw (number, 
                                             type_s,
                                             request_xml)
              VALUES ('$number', 
                      '$type',
                      \"$request\")";
        return self::executeGetId($query);
    }

    /**
     * Bank xac charge callpayment
     */
    public static function setLogGatewayTocGwUpdate($id, $response)
    {
        $query = "UPDATE bankgw_log.log_gateway_toc_gw
              SET response_xml = \"$response\", updated_at = \"(new \DateTime())->format('Y-m-d H:i:s')\"
              WHERE id = '$id'";
        return self::execute($query);
    }

    public static function setLogCapitalInit($request, $response, $responseSet, $errorCode, $desc, $get, $set)
    {
        $query = "INSERT INTO bankgw_log.log_capital_init (request, response, response_set, error_code, description, get_success, set_success, ip)
              VALUES ('$request', 
                      '$response',
                      '$responseSet',
                      '$errorCode',
                      '$desc',
                      '$get',
                      '$set',
                      '" . $_SERVER['REMOTE_ADDR'] . "')";

        return self::execute($query);
    }

    /**
     * Bank TDB charge
     */
    public static function setLogTdbCharge($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_tdb_charge (bank_tdb_id, ip)
                                            VALUES(:bank_tdb_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_tdb_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogTdbCharge($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_tdb_charge
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    /**
     * Bank TDB charge callpayment
     */
    public static function setLogTdbChargeCallPayment($bankId)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bankgw_log.log_tdb_charge_call_payment (bank_tdb_id, ip)
                                            VALUES(:bank_tdb_id, '{$_SERVER['REMOTE_ADDR']}')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':bank_tdb_id' => $bankId));
        return $pdo->lastInsertId();
    }

    public static function updateLogTdbChargeCallPayment($logId, $request, $response, $time)
    {
        $pdo = self::getLogPDO();
        $sql = "UPDATE bankgw_log.log_tdb_charge_call_payment
                    SET request = :request,
                        response = :res,
                        exec_time = $time
                    WHERE id = $logId";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(':request' => $request, ':res' => $response));
    }

    public static function getLogLoyaltyApiResponse($orderId, $number)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response_xml FROM `log_gateway_loyaltyapi` WHERE `order_id`=$orderId and number='$number' ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function setLogCapitronInit($request, $response, $responseSet, $errorCode, $desc, $get, $set)
    {
        $query = "INSERT INTO bankgw_log.log_capitron_init (request, response, response_set, error_code, description, get_success, set_success, ip)
              VALUES ('$request', 
                      '$response',
                      '$responseSet',
                      '$errorCode',
                      '$desc',
                      '$get',
                      '$set',
                      '" . $_SERVER['REMOTE_ADDR'] . "')";

        return self::execute($query);
    }

    public static function getLogDealerCharge($orderId, $type)
    {
        $pdo = self::getLogPDO();
        $sql = "SELECT response_xml AS response FROM `log_gateway_dealer` WHERE `order_id`=$orderId AND type_s='$type' ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($log)) {
            return $log[0];
        }
        return false;
    }

    public static function setLogBankpayment($bankpayment)
    {
        
        $query = "INSERT INTO `bankgw_log`.`log_bankpayment`(`bankpayment_id`, `parent_id`, `child_num`, `vendor_id`, `bank_order_id`, `type`, `bank_payment_code`, 
                    `number`, `contract_number`, `contract_name`, `bill_cycle`, `paid_amount`, `contract_amount`, `credit_control`, `insurance_date`, `insurance_amount`, 
                    `username`, `status`, `status_comment`, `updated_user_id`, `updated_at`, `created_at`, ip)
              VALUES (".$bankpayment['id'].",
                        ".$bankpayment['parent_id'].",
                        ".$bankpayment['child_num'].",
                        ".$bankpayment['vendor_id'].",
                        ".$bankpayment['bank_order_id'].",
                        ".$bankpayment['type'].",
                        '".$bankpayment['bank_payment_code']."',
                        '".$bankpayment['number']."',
                        '".$bankpayment['contract_number']."',
                        '".$bankpayment['contract_name']."',
                        ".$bankpayment['bill_cycle'].",
                        ".$bankpayment['paid_amount'].",
                        ".$bankpayment['contract_amount'].",
                        ".$bankpayment['credit_control'].",
                        '".$bankpayment['insurance_date']."',
                        ".$bankpayment['insurance_amount'].",
                        '".$bankpayment['username']."',
                        ".$bankpayment['status'].",
                        '".$bankpayment['status_comment']."',
                        ".$bankpayment['updated_user_id'].",
                        '".$bankpayment['updated_at']."',
                        '".$bankpayment['created_at']."',
                        '".$_SERVER['REMOTE_ADDR']."')";
        
        return self::execute($query);
    }
}
