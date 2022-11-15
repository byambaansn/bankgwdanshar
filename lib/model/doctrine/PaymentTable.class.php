<?php

/**
 * PaymentTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PaymentTable extends Doctrine_Table
{

    CONST STATUS_PAYMENT = 1;
    CONST STATUS_SAP = 2;

    /**
     * Returns an instance of this class.
     *
     * @return object PaymentTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Payment');
    }

    /**
     * 
     * @param int $id
     * @param int $status
     * @return BankKhaan
     */
    public static function retrieveByPK($id)
    {
        $q = Doctrine_Query::create()
                ->from('Payment')
                ->where('id = ?', $id);
        return $q->fetchOne();
    }

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function getForSelectStatus()
    {
        $status = array(
            self::STATUS_PAYMENT => self::getStatusName(self::STATUS_PAYMENT),
            self::STATUS_SAP => self::getStatusName(self::STATUS_SAP),
        );
        return $status;
    }

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function getStatusName($id)
    {
        $status = array(
            self::STATUS_PAYMENT => 'Төлөлт болгосон',
            self::STATUS_SAP => 'SAP руу оруулсан',
        );
        return $status[$id];
    }

    public static function insert($type, $comment, $amount, $userId, $username, $assignment = '')
    {
        $payment = new Payment();
        $payment->setTypeId($type);
        $payment->setDescription($comment);
        $payment->setAssignment($assignment);
        $payment->setAmount($amount);
        $payment->setCreatedUserId($userId);
        $payment->setUsername($username);
        $payment->setCreatedAt(date('Y-m-d H:i:s'));
        $payment->save();
        return $payment;
    }

    /**
     * Payment table дээр шинэ төлөлт нэмнэ.
     * 
     * @param $type, $comment, $transaction
     * @return $payment;
     */
    public static function add($type, $comment, $transaction, $assignment = '')
    {

        if ($transaction && $type) {
            $pdo = Doctrine_Manager::getInstance()->getConnection("transaction")->getDbh();
            $q = "insert into payment(type_id, description, amount, created_user_id, username, assignment, created_at) values(?,?,?,?,?,?,?)";
            $stmt = $pdo->prepare($q);
            $stmt->execute(array($type, $comment, $transaction['order_amount'], HrmCore::BOT_USER, 'add', $assignment,date('Y-m-d H:i:s')));
            $id = $pdo->lastInsertId();
            if ($id) {
                $q = "insert into transaction_payment(transaction_id, payment_id) values(?,?);"
                        . "update transaction set status = ? where id = ?";
                $stmt = $pdo->prepare($q);
                $stmt->execute(array($transaction['id'], $id, TransactionTable::STATUS_PAYMENT, $transaction['id']));
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     *
     * @return \array
     */
    public static function getListCustom($all = 0, $dateFrom = 0, $dateTo = 0, $bank = 0, $account = null, $orderId = 0, $orderAmount = 0, $orderValue = 0, $status = 0, $type = 0, $startIndex = 0, $pageSize = 10, $orderBy = 'created_at ASC')
    {
        $pdo = Doctrine_Manager::getInstance()->getConnection('dbclone')->getDbh();

        $wherePayment = array();
        $whereTransaction = array();
        $transactionIndex = "FROM transaction\n";
        if (!$orderBy) {
            $orderBy = 'created_at ASC';
        }
        if ($dateFrom) {
            if ($all == 2) {
                $whereTransaction[] = "order_date >='$dateFrom 00:00:00'";
            } else {
                $whereTransaction[] = "created_at >='$dateFrom 00:00:00'";
            }
            $wherePayment[] = "created_at >='$dateFrom 00:00:00'";
        }
        if ($dateTo) {
            if ($all == 2) {
                $whereTransaction[] = "order_date <='$dateTo 23:59:59'";
            } else {
                $whereTransaction[] = "created_at <='$dateTo 23:59:59'";
            }
        }
        if ($orderAmount) {
            $whereTransaction[] = "order_amount ='$orderAmount'";
        }
        if ($orderValue) {
            $whereTransaction[] = "order_p like '%$orderValue%'";
        }
        if ($bank) {
            $whereTransaction[] = "bank_id ='$bank'";
        }
        if ($status) {
            $wherePayment[] = "status ='$status'";
        }
        if ($type) {
            $wherePayment[] = "type_id ='$type'";
        }
        if ($account) {
            if (is_array($account)) {
                $whereTransaction[] = "bank_account IN (" . implode(',', $account) . ")";
            } else {
                $whereTransaction[] = "bank_account ='$account'";
            }
        }
        if ($orderId) {
            $whereTransaction[] = "order_id ='$orderId'";
        }
        $wherePayment = implode(' AND ', $wherePayment);
        $whereTransaction = implode(' AND ', $whereTransaction);
        $limit = '';
        if (!$all) {
            $limit = " LIMIT $startIndex ,$pageSize";
        }

        $query = "
            SELECT p.id                                                                                            AS p_id,
                   b.name                                                                                          AS bank_name,
                   t.`bank_account`,  
                   t.`related_account`,                                                                             
                   t.`order_id`,
                   t.`order_date`,
                   t.`order_p`,
                   t.`order_type`,
                   t.`order_amount`,
                   t.`order_branch`,
                   p.created_at                                                                                    AS created_at,
                   t.created_at                                                                                    AS t_created_at,
                   IF(pt.id = 1, p.assignment, pt.NAME)                                                            AS payment_type,
                   pt.id                                                                                           AS payment_id,
                   c.ref_id                                                                                        AS company_id,
                   tp.id,
                   p.username,
                   p.`amount`,
                   p.`description`,
                   IF(p.`status` = 1, '<b class=\"green\">D</b>', IF(p.`status` = 2, '<b class=\"red\">SAP</b>', '-')) AS status,
                   ba.sap_account,
                   ba.sap_gl_account
            FROM (SELECT *
                  FROM payment
                  WHERE $wherePayment
                 ) p
                     STRAIGHT_JOIN transaction_payment tp ON p.id = tp.payment_id
                     STRAIGHT_JOIN (SELECT *
                                    $transactionIndex
                                    WHERE $whereTransaction
                ) t ON t.id = tp.transaction_id
                     LEFT JOIN payment_type pt ON pt.id = p.type_id
                     LEFT JOIN bank b ON b.id = t.bank_id
                     LEFT JOIN `bank_account` ba ON ba.account = t.bank_account
                     LEFT JOIN `company` c ON c.id = ba.company_id
            WHERE t.id IS NOT NULL
            GROUP BY p.id
            ORDER BY $orderBy " . $limit;

        $rows = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * 
     * @return \array
     */
    public static function getListCustomCount($dateFrom, $dateTo, $bank = 0, $account = null, $orderId = 0, $orderAmount = 0, $orderValue = 0, $status = 0, $type = 0)
    {
        $pdo = Doctrine_Manager::getInstance()->getConnection('dbclone')->getDbh();

        $whereTransaction = array();
        $wherePayment = array();

        $where[] = "id IS NOT NULL";

        if ($dateFrom) {
            $whereTransaction[] = "created_at >='$dateFrom 00:00:00'";
            $wherePayment[] = "created_at >='$dateFrom 00:00:00'";
        }
        if ($dateTo) {
            $whereTransaction[] = "created_at <='$dateTo 23:59:59'";
        }
        if ($bank) {
            $whereTransaction[] = "bank_id ='$bank'";
        }
        if ($orderAmount) {
            $whereTransaction[] = "order_amount ='$orderAmount'";
        }
        if ($orderValue) {
            $whereTransaction[] = "order_p like '%$orderValue%'";
        }
        // optional
        if ($status) {
            $wherePayment[] = "status ='$status'";
        }
        // optional
        if ($type) {
            $wherePayment[] = "type_id ='$type'";
        }
        if ($account) {
            if (is_array($account)) {
                $whereTransaction[] = "bank_account IN (" . implode(',', $account) . ")";
            } else {
                $whereTransaction[] = "bank_account ='$account'";
            }
        }
        // optional
        if ($orderId) {
            $whereTransaction[] = "order_id ='$orderId'";
        }
        $whereTransaction = implode(' AND ', $whereTransaction);
        $wherePayment = implode(' AND ', $wherePayment);

        $query = "
            SELECT count(p.id) AS total
                FROM (SELECT *
                      FROM payment
                      WHERE $wherePayment
                     ) p
                         STRAIGHT_JOIN transaction_payment tp ON tp.payment_id = p.id
                         STRAIGHT_JOIN (SELECT *
                                        FROM transaction
                                        WHERE $whereTransaction
                    ) t ON t.id = tp.transaction_id";

        $rows = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        return $rows['total'];
    }

    /**
     *
     * @return \array
     */
    public static function getListCustomFooter($dateFrom, $dateTo, $bank = 0, $account = null, $orderId = 0, $orderAmount = 0, $orderValue = 0, $status = 0, $type = 0)
    {
        $pdo = Doctrine_Manager::getInstance()->getConnection('dbclone')->getDbh();

        $whereTransaction = array();
        $wherePayment = array();
        $whereTransaction[] = "id IS NOT NULL";

        if ($dateFrom) {
            $whereTransaction[] = "created_at >='$dateFrom 00:00:00'";
            $wherePayment[] = "created_at >='$dateFrom 00:00:00'";
        }
        if ($dateTo) {
            $whereTransaction[] = "created_at <='$dateTo 23:59:59'";
        }
        if ($bank) {
            $whereTransaction[] = "bank_id ='$bank'";
        }
        if ($orderAmount) {
            $whereTransaction[] = "order_amount ='$orderAmount'";
        }
        if ($orderValue) {
            $whereTransaction[] = "order_p like '%$orderValue%'";
        }
        // optional
        if ($status) {
            $wherePayment[] = "status ='$status'";
        }
        // optional
        if ($type) {
            $wherePayment[] = "type_id ='$type'";
        }
        if ($account) {
            if (is_array($account)) {
                $whereTransaction[] = "bank_account IN (" . implode(',', $account) . ")";
            } else {
                $whereTransaction[] = "bank_account ='$account'";
            }
        }
        // optional
        if ($orderId) {
            $whereTransaction[] = "t.order_id ='$orderId'";
        }

        $whereTransaction = implode(' AND ', $whereTransaction);
        $wherePayment = implode(' AND ', $wherePayment);

        $query = "
            SELECT sum(CASE WHEN t.order_type = 'SUB' THEN (-1 * p.amount) ELSE p.amount END) AS total
                FROM (SELECT *
                      FROM payment
                      WHERE $wherePayment
                    ) p
                         STRAIGHT_JOIN transaction_payment tp ON tp.payment_id = p.id
                         STRAIGHT_JOIN (SELECT *
                                        FROM transaction
                                        WHERE $whereTransaction
                    ) t ON t.id = tp.transaction_id";

        $rows = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        return $rows['total'];
    }

    /**
     * Payment table SAP дата татсан тэмдэглэгээ тавих.
     * 
     * @param $type, $comment, $transaction
     * @return $payment;
     */
    public static function setSapDataExport($paymentIds)
    {
        Doctrine_Query::create()
                ->update('Payment')
                ->set('status', self::STATUS_SAP)
                ->whereIn('id', $paymentIds)
                ->execute();
    }

    public static function updateTypeId($bankId, $orderId, $orderDate, $orderType, $amount, $typeId)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        
        $query = "
            UPDATE bank_transaction.payment A
                INNER JOIN bank_transaction.transaction_payment B ON A.id = B.payment_id
                INNER JOIN bank_transaction.transaction C ON B.transaction_id = C.id
            SET type_id = $typeId
            WHERE C.bank_id = $bankId AND C.order_id = '$orderId' AND C.order_date = '$orderDate' AND C.order_type = '$orderType' AND A.amount = $amount";
        $pdo->prepare($query)->execute();
    }
    
    public static function getTypeId($bankId, $orderId, $orderDate, $orderType, $amount)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $query = "
            SELECT A.* FROM bank_transaction.`payment` A
                INNER JOIN bank_transaction.transaction_payment B ON A.id = B.payment_id
                INNER JOIN bank_transaction.transaction C ON B.transaction_id = C.id
            WHERE C.bank_id = $bankId AND C.order_id = '$orderId' AND C.order_date = '$orderDate' AND C.order_type = '$orderType' AND A.amount = $amount";
        $rows = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);

        return $rows;
    }
    
    public static function deletePayment($transactionId, $userId)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        
        $query = "
            INSERT INTO bank_transaction.deleted_payment
            SELECT 0, A.transaction_id, B.id, B.type_id, B.assignment, B.amount, B.description, B.status, B.username, '$userId', NOW()
            FROM bank_transaction.transaction_payment A INNER JOIN bank_transaction.payment B ON A.payment_id = B.id
            WHERE A.transaction_id = $transactionId;
            DELETE FROM bank_transaction.transaction_payment WHERE transaction_id = $transactionId;
            DELETE B FROM bank_transaction.deleted_payment A INNER JOIN payment B ON A.payment_id = B.id AND A.transaction_id = $transactionId";
        $pdo->prepare($query)->execute();
    }
    
    public static function updateAmountByTypeId($tranId, $typeId, $amount)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        
        $query = "
            UPDATE bank_transaction.payment A
                INNER JOIN bank_transaction.transaction_payment B ON A.id = B.payment_id
            SET A.amount = A.amount + $amount
            WHERE B.transaction_id = '$tranId' AND A.type_id = $typeId";
        $pdo->prepare($query)->execute();
    }
}
