<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductPayment
 *
 * @author Belbayar
 */
class ProductPayment
{

    const SERVER = '172.27.30.100'; //192.168.186.2
    const USERNAME = 'bankfinance';
    const PASSWORD = 'Ytu3FulffPtjt4uEy5mgr';

    public static function getLogPDO()
    {
        $dsn = 'mysql:dbname=bank_transaction;host=' . self::SERVER;
        try {
            $dbh = new PDO($dsn, self::USERNAME, self::PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
            return $dbh;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        return false;
    }

    /**
     * Төлбөрын хуулга
     */
    public static function charge($params)
    {
        $pdo = self::getLogPDO();
        $sql = "INSERT INTO bank_transaction.transaction(bank_id, bank_account, related_account, order_id, order_date, order_p, order_type, order_amount,  order_branch, status,created_at)
                                            VALUES(:bank_id,:bank_account,:related_account,:order_id,:order_date,:order_p,:order_type,:order_amount,:order_branch,:status,:created_at)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array(
                    ':bank_id' => '9',
                    ':bank_account' => $params['transAccount'],
                    ':related_account' => $params['relatedAccount'],
                    ':order_id' => $params['transNumber'],
                    ':order_date' => $params['transDate'],
                    ':order_p' => $params['transValue'],
                    ':order_type' => $params['transType'],
                    ':order_amount' => $params['amount'],
                    ':order_branch' => $params['transBranch'],
                    ':status' => '1',
                    ':created_at' => date("Y-m-d H:i:s")));
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

}

?>
