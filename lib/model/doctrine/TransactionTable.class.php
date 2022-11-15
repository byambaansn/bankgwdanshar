<?php

/**
 * TransactionTable
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TransactionTable extends Doctrine_Table
{

    CONST TYPE_ALL = 0;
    CONST STATUS_NEW = 1;
    CONST STATUS_PAYMENT = 2;
    CONST STATUS_TEMP = 5;

    /**
     * Returns an instance of this class.
     *
     * @return object TransactionTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Transaction');
    }

    /**
     *
     * @param int $id
     * @param int $status
     * @return Transaction
     */
    public static function retrieveByPK($id, $status = 0)
    {
        $q = Doctrine_Query::create()
            ->from('Transaction')
            ->where('id = ?', $id);

        if ($status) {
            $q->andWhere('status = ?', $status);
        }

        return $q->fetchOne();
    }

    /**
     *
     * @param $bank
     * @param $orderId
     * @param null $orderType
     * @param null $orderAmount
     * @param null $orderDate
     * @return Transaction
     */
    public static function retrieveByBankAndOrderId($bank, $orderId, $orderType = null, $orderAmount = null, $orderDate = null)
    {
        $q = Doctrine_Query::create()
            ->from('Transaction FORCE INDEX(ind_order_date)')
            ->where('order_date = ?', $orderDate)
            ->andWhere('bank_id = ?', $bank)
            ->andWhere('order_id = ?', $orderId)
            ->andWhere('order_type = ?', $orderType)
            ->andWhere('order_amount = ?', $orderAmount)
        ;
//        if ($bank == BankTable::KHAAN) {
//            $q->andWhere('order_date > ?', date("Y-m-d"));
//        }
        return $q->fetchOne();
    }

    /**
     * Returns an instance of this class.
     *
     * @param $bankId
     * @param $bankAccount
     * @param $orderId
     * @param $orderDate
     * @param $orderP
     * @param $orderType
     * @param $orderAmount
     * @param $orderBranch
     * @param $relatedAccount
     * @return object Transaction
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public static function insert($bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount)
    {
        $transaction = new Transaction();
        $transaction->setBankId($bankId);
        $transaction->setBankAccount($bankAccount);
        $transaction->setOrderId($orderId);
        $transaction->setOrderDate($orderDate);
        $transaction->setOrderP($orderP);
        $transaction->setOrderType($orderType);
        $transaction->setOrderAmount($orderAmount);
        $transaction->setOrderBranch($orderBranch);
        $transaction->setStatus(TransactionTable::STATUS_NEW);
        $transaction->setCreatedAt(date('Y-m-d H:i:s'));
//        ??
        $transaction->setUpdatedUserId(0);
        $transaction->setRelatedAccount($relatedAccount);
        
        $transaction->save();
        return $transaction;
    } 

    /**
     * Төлөв
     *
     * @return array
     */
    public static function getForSelectStatus()
    {
        $status = array(
            self::STATUS_NEW => self::getStatusName(self::STATUS_NEW),
            self::STATUS_PAYMENT => self::getStatusName(self::STATUS_PAYMENT),
        );
        return $status;
    }

    /**
     * Төлөв
     *
     * @param $id
     * @return array
     */
    public static function getStatusName($id)
    {
        $status = array(
            self::STATUS_NEW => 'Шинээр татагдсан',
            self::STATUS_PAYMENT => 'Төлөлт болгосон',
        );
        return $status[$id];
    }

    /**
     *
     * @param int $dateFrom
     * @param int $dateTo
     * @param int $bankId
     * @param int $orderId
     * @param int $orderType
     * @param int $orderAmount
     * @param int $orderValue
     * @param int $account
     * @param int $status
     * @param int $bankDate
     * @param int $page
     * @return \sfDoctrinePager
     */
    public static function getList($dateFrom = 0, $dateTo = 0, $bankId = 0, $orderId = 0, $orderType = 0, $orderAmount = 0, $orderValue = 0, $account = 0,  $status = 0, $bankDate = 0, $page = 1)
    {
        $q = Doctrine_Query::create()
            ->from('Transaction FORCE INDEX(ind_order_date)')
            ->orderBy('status DESC, id DESC');

        if ($bankDate) {
            if ($dateFrom) {
                $q->addWhere("order_date >= ?", $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $q->addWhere("order_date <= ?", $dateTo . ' 23:59:59');
            }
        } else {
            if ($dateFrom) {
                $q->addWhere("created_at >= ?", $dateFrom . ' 00:00:00');
            }
            if ($dateTo) {
                $q->addWhere("created_at <= ?", $dateTo . ' 23:59:59');
            }
        }
        if ($account) {
            $bankAccounts = $account;
        } else {
            $bankAccounts = BankAccountTable::getBankAccountByCred($bankId);
        }
        if ($bankAccounts) {
            if (is_array($bankAccounts)) {
                $q->andWhereIn('bank_account', $bankAccounts);
            } else {
                $q->addWhere('bank_account = ?', $bankAccounts);
            }
        }
       
        if ($bankId) {
            $q->addWhere('bank_id=?', $bankId);
        }
        if ($orderId) {
            $q->addWhere('order_id = ?', $orderId);
        }
        if ($orderType) {
            if ($orderType == 2) {
                $q->addWhere('order_type = ?', 'SUB');
            } else {
                $q->andWhereIn('order_type', array('ADD', 'C', 'income', '1'));
            }
        }
        if ($orderAmount) {
            $q->addWhere('order_amount = ?', $orderAmount);
        }
        if ($orderValue) {
            $q->addWhere('order_p LIKE \'%' . $orderValue . '%\'');
        }

        if ($status) {
            $q->addWhere('status = ?', $status);
        }
//        print_r($q->getParams());
//        print_r($q->getSqlQuery());
//        die();
        $pager = new sfDoctrinePager('Transaction', 10000);
        $pager->setQuery($q);
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     *
     * @param int $dateFrom
     * @param int $dateTo
     * @param int $bankId
     * @param int $orderId
     * @param int $orderType
     * @param int $orderAmount
     * @param int $orderValue
     * @param int $account
     
     * @param int $status
     * @param int $bankDate
     * @return \array
     * @throws Doctrine_Manager_Exception
     */
    public static function getListCustom($dateFrom = 0, $dateTo = 0, $bankId = 0, $orderId = 0, $orderType = 0, $orderAmount = 0, $orderValue = 0, $account = 0, $status = 0, $bankDate = 0)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();

        $where = array();

        if ($account) {
            $bankAccounts = $account;
        } else {
            $bankAccounts = BankAccountTable::getBankAccountByCred($bankId);
        }
        if ($bankAccounts) {
            if (is_array($bankAccounts)) {
                $where[] = "t.bank_account IN(" . implode(',', $bankAccounts) . ")";
            } else {
                $where[] = "t.bank_account=" . $bankAccounts;
            }
        }
        if ($bankId) {
            $where[] = "t.bank_id=" . $bankId;
        }
        if ($bankDate) {
            if ($dateFrom) {
                $where[] = "t.order_date>='" . $dateFrom . " 00:00:00'";
            }
            if ($dateTo) {
                $where[] = "t.order_date<='" . $dateTo . " 23:59:59'";
            }
        } else {
            if ($dateFrom) {
                $where[] = "t.created_at>='" . $dateFrom . " 00:00:00'";
            }
            if ($dateTo) {
                $where[] = "t.created_at<='" . $dateTo . " 23:59:59'";
            }
        }

        if ($orderId) {
            $where[] = "t.order_id='" . $orderId . "'";
        }
        if ($orderType) {
            if ($orderType == 2) {
                $where[] = "t.order_type='SUB'";
            } else {
                $where[] = "t.order_type IN('ADD', 'C', 'income', '1')";
            }
        }
        if ($orderAmount) {
            $where[] = "t.order_amount=" . $orderAmount;
        }
        if ($orderValue) {
            $where[] = 't.order_p LIKE \'%' . $orderValue . '%\'';
        }
        if ($status) {
            $where[] = "t.status=" . $status;
        }
        $where = implode(' AND ', $where);

        $query = "
            SELECT b.name as bank_name ,t.*
            FROM bank_transaction.`transaction` AS t
            LEFT JOIN  bank_transaction.bank b ON b.id=t.bank_id
            WHERE $where
            ORDER BY t.created_at";

        $rows = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     *
     * @param int $dateFrom
     * @param int $dateTo
     * @param int $bankId
     * @param int $orderId
     * @param int $orderType
     * @param int $orderAmount
     * @param int $orderValue
     * @param int $account
     * @param int $status
     * @param int $bankDate
     * @return \array
     * @throws Doctrine_Manager_Exception
     */
    public static function getListTotalAmount($dateFrom = 0, $dateTo = 0, $bankId = 0, $orderId = 0, $orderType = 0, $orderAmount = 0, $orderValue = 0, $account = 0, $status = 0, $bankDate = 0)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();

        $where = array();
        if ($account) {
            $bankAccounts = $account;
        } else {
            $bankAccounts = BankAccountTable::getBankAccountByCred($bankId);
        }
        if ($bankAccounts) {
            if (is_array($bankAccounts) && count($bankAccounts)) {
                $where[] = "t.bank_account IN(" . implode(',', $bankAccounts) . ")";
            } else {
                $where[] = "t.bank_account=" . $bankAccounts;
            }
        } else {
            $where[] = "1=2";
        }
        
        if ($bankId) {
            $where[] = "t.bank_id=" . $bankId;
        }

        if ($bankDate) {
            if ($dateFrom) {
                $where[] = "t.order_date>='" . $dateFrom . " 00:00:00'";
            }
            if ($dateTo) {
                $where[] = "t.order_date<='" . $dateTo . " 23:59:59'";
            }
        } else {
            if ($dateFrom) {
                $where[] = "t.created_at>='" . $dateFrom . " 00:00:00'";
            }
            if ($dateTo) {
                $where[] = "t.created_at<='" . $dateTo . " 23:59:59'";
            }
        }

        if ($orderId) {
            $where[] = "t.order_id='" . $orderId . "'";
        }
        if ($orderType) {
            $orderType = ($orderType == 2) ? 'SUB' : 'ADD';
            $where[] = "t.order_type=" . $orderType;
        }
        if ($orderAmount) {
            $where[] = "t.order_amount=" . $orderAmount;
        }
        if ($orderValue) {
            $where[] = 't.order_p LIKE \'%' . $orderValue . '%\'';
        }
        if ($status) {
            $where[] = "t.status=" . $status;
        }
        $where = implode(' AND ', $where);

        $query = "
            SELECT sum(t.order_amount) as total
            FROM bank_transaction.transaction AS t
            WHERE $where ";
// echo $query;die();
        $rows = $pdo->query($query)->fetch(PDO::FETCH_ASSOC);
        return $rows['total'];
    }

    /**
     *
     * @return \array
     */
    public static function getAccountNumbers()
    {
        Doctrine_Manager::getInstance()->setCurrentConnection("transaction");
        $pdo = Doctrine_Manager::connection()->getDbh();
        $query = "
            SELECT bank_account
            FROM bank_transaction.transaction AS t
            WHERE 1=1
                GROUP BY t.bank_account";

        $rows = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        foreach ($rows as $row) {
            $result[$row['bank_account']] = $row['bank_account'];
        }
        return $result;
    }


    /**
     *  Gereet
     * @param $type
     * @param $bankId
     * @param $bankAccount
     * @param $orderId
     * @param $orderDate
     * @param $orderP
     * @param $orderType
     * @param $orderAmount
     * @param $orderBranch
     * @param $relatedAccount
     * @return \array
     * @throws Doctrine_Manager_Exception
     */
    public static function setDealerAssignment($type, $bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount)
    {
        $transaction = self::retrieveByBankAndOrderId($bankId, $orderId, $orderType, $orderAmount, $orderDate);
        if (!$transaction) {
            try {
                $transaction = self::insert($bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount);
            } catch (Exception $ex) {

            }
        }

        $pdo = Doctrine_Manager::connection()->getDbh();
        $sql = "UPDATE bank_transaction.transaction
              SET status = '" . TransactionTable::STATUS_TEMP . "'
              WHERE id = '" . $transaction['id'] . "'
              AND status = '" . TransactionTable::STATUS_NEW . "'
              LIMIT 1";
        $affectedRows = $pdo->exec($sql);
        if ($affectedRows == 0) {
            return false;
        }

        if ($transaction && $type) {
            $payment = PaymentTable::insert($type, 'dealer charger', $orderAmount, HrmCore::BOT_USER, 'BOT');
            if ($payment) {
                $transactionPayment = TransactionPaymentTable::insert($transaction->id, $payment->id);
                if ($transactionPayment) {
                    $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                    $transaction->save();
                }
                return $payment;
            }
        }
        return $transaction;
    }

    /**
     *  Recharge assignment
     * @param $type
     * @param $bankId
     * @param $bankAccount
     * @param $orderId
     * @param $orderDate
     * @param $orderP
     * @param $orderType
     * @param $orderAmount
     * @param $orderBranch
     * @param $relatedAccount
     * @return \array
     * @throws Exception
     */
    public static function setRechargeAssignment($type, $bankId, $bankAccount,$orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount)
    {
        $transaction = self::retrieveByBankAndOrderId($bankId, $orderId, $orderType, $orderAmount, $orderDate);
        if (!$transaction) {
            $transaction = self::setDealerAssignment($type, $bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount);
            return TRUE;
        }

        if ($transaction && $type) {
            $transPayment = TransactionPaymentTable::checkPayment($transaction['id']);
            if (!$transPayment){
                $payment = PaymentTable::insert($type, 'dealer charger', $orderAmount, HrmCore::BOT_USER, 'BOT');
                if ($payment) {
                    $transactionPayment = TransactionPaymentTable::insert($transaction->id, $payment->id);
                    if ($transactionPayment) {
                        $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                        $transaction->save();
                    }
                    return $payment;
                }
            } else {
                $payment = PaymentTable::updateTypeId($bankId, $orderId, $orderDate, $orderType, $orderAmount, $type);
                return $payment;
            }
        }
        return $transaction;
    }

    /**
     *  Gereet
     * @param $type
     * @param $bankId
     * @param $bankAccount
     * @param $orderId
     * @param $orderDate
     * @param $orderP
     * @param $orderType
     * @param $orderAmount
     * @param $orderBranch
     * @param $relatedAccount
     * @param string $comment
     * @param bool $isChild
     * @param int $childAmount
     * @return int
     * @throws Exception
     */
    public static function setAssignmentMain($type, $bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount, $comment = 'desc', $isChild = false, $childAmount = 0)
    {
        $transaction = self::retrieveByBankAndOrderId($bankId, $orderId, $orderType, $orderAmount, $orderDate);

        if (!$transaction) {
            $transaction = self::insert($bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount);
        }

        $checkPayment = false;
        if (!$isChild) {
            $checkPayment = TransactionPaymentTable::checkPayment($transaction['id']);
        } else {
            $checkPayment = TransactionPaymentTable::checkPaymentByType($transaction['id'], $type);
        }

        $paymentAmount = $childAmount > 0 ? $childAmount : $orderAmount;
        if ($type && !$checkPayment) {
            $payment = PaymentTable::insert($type, $comment, $paymentAmount, HrmCore::BOT_USER, $comment);
            if ($payment) {
                $transactionPayment = TransactionPaymentTable::insert($transaction->id, $payment->id);
                if ($transactionPayment) {
                    $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                    $transaction->save();
                }
            }
        }
        return 1;
    }

    public static function setAssignmentCopy($type, $bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount, $comment = 'desc', $isChild = false, $childAmount = 0)
    {
        $transaction = self::retrieveByBankAndOrderId($bankId, $orderId, $orderType, $orderAmount, $orderDate);

        if (!$transaction) {
            $transaction = self::insert($bankId, $bankAccount, $orderId, $orderDate, $orderP, $orderType, $orderAmount, $orderBranch, $relatedAccount);
        }

        $checkPayment = false;
        if (!$isChild) {
            $checkPayment = TransactionPaymentTable::checkPayment($transaction['id']);
        } else {
            $checkPayment = TransactionPaymentTable::checkPaymentByType($transaction['id'], $type);
        }

        $paymentAmount = $childAmount > 0 ? $childAmount : $orderAmount;
        if ($type && !$checkPayment) {
            $payment = PaymentTable::insert($type, $comment, $paymentAmount, HrmCore::BOT_USER, $comment);
            if ($payment) {
                $transactionPayment = TransactionPaymentTable::insert($transaction->id, $payment->id);

                if ($transactionPayment) {
                    $transaction->setStatus(TransactionTable::STATUS_PAYMENT);
                    $transaction->save();
                }
            }
        }
        else {
            PaymentTable::updateAmountByTypeId($transaction['id'], $type, $paymentAmount);
        }
        return 1;
    }

    /**
     * Татагдсан шинэ хуулгуудыг буцаана.
     *
     * @param $dateFrom
     * @param $dateTo
     * @param int $limit
     * @return array|null $pdo->query($q);
     * @throws Doctrine_Manager_Exception
     */
    public static function updateNewTransactions($dateFrom, $dateTo, $limit)
    {
        $pdo = Doctrine_Manager::getInstance()->getConnection("transaction")->getDbh();
        // FOR UPDATE ажиллахын тулд AUTO COMMIT идэвхгүй байх ёстой
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $pdo->beginTransaction();
        // Шинэ гүйлгээнүүдийн ID-г авч FOR UPDATE-р түгжинэ
        $ids = Doctrine_Query::create()
            ->select('id')
            ->from('Transaction')
            ->where('order_date >= ?', $dateFrom)
            ->andWhere('order_date <= ?', $dateTo)
            ->andWhere('status = ?', self::STATUS_NEW)
            ->limit($limit)
            ->forUpdate(true)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
        // Олдсон ID-нуудтай гүйлгээнүүдийн төлвийг TEMP болгоно
        if ($ids) {
            Doctrine_Query::create()
                ->update('Transaction')
                ->set('status', self::STATUS_TEMP)
                ->whereIn('id', $ids)
                ->execute();
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            // Төлөв нь шинэчлэгдсэн гүйлгээнүүдээ буцаах
            $qSelect = Doctrine_Query::create()
                ->from('Transaction')
                ->whereIn('id', $ids);
            return $qSelect->fetchArray();
        } else {
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            return null;
        }
    }

    /**
     * Дансны ангилалыг буцаана.
     *
     * @param $transaction , $bankAccounts
     * @param $bankAccounts
     * @return bankAccount->type_id
     */
    public static function getAccType($transaction, $bankAccounts)
    {
        $b = $bankAccounts->findOneByAccount($transaction['bank_account']);
        if ($b)
            return $b->getType();
        return FALSE;
    }

    /**
     * Тухайн гүйлгээг шүүж хөрвүүлнэ.
     *
     * @param $transaction , $filter
     * @param $filter
     * @return boolean;
     */
    public static function checkFilter($transaction, $filter)
    {
        //Үгээр хайх эсэхийг шалгана.
        if ($filter['filter_type'] == ConfigAssignmentTable::FILTER_WORD) {
            //Тухайн гүйлгээний утганд шүүх үг нь байгаа эсэхийг шалгаж байна.
            if (mb_stripos($transaction['order_p'], $filter['filter']) !== FALSE) {
                //Шүүх үг нь гүйлгээний утганд олдсон учир төлөлт болгож байна.
                PaymentTable::add($filter['type_id'], 'setAssignment', $transaction);
                return TRUE;
            }
        } else
            //Кодоор хайх эсэхийг шалгана.
            if ($filter['filter_type'] == ConfigAssignmentTable::FILTER_CODE) {
                preg_match($filter['filter'], $transaction['order_p'], $matches);
                //Тухайн гүйлгээний утганд код нь байгаа эсэхийг шалгаж байна.
                if ($matches) {
                    //Код нь гүйлгээний утганд олдсон учир төлөлт болгож байна.
                    PaymentTable::add($filter['type_id'], 'setAssignment', $transaction, $matches[0]);
                    return TRUE;
                }
            } else {
                //Бусад гүйлгээг төлөлт болгож байна.
                PaymentTable::add($filter['type_id'], 'setAssignment', $transaction);
                return TRUE;
            }
        return FALSE;
    }

    /**
     * Төлөлт шүүж хөрвүүлэх
     *
     * @param &$result , $logger, $bankAccounts, $filter_day
     * @param $logger
     * @param $bankAccounts
     * @param $filter_day
     * @param $limit
     * @param $days
     * @return void ;
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public static function execAssignment(&$result, $logger, $bankAccounts, $filter_day, $limit, $days = 3)
    {

        if (!ConfigAssignmentTable::checkIfFilterExists()) {
            return;
        }

        if ($filter_day == ConfigAssignmentTable::FILTER_EVERY_DAY) {
            // Өдөр болгон ажиллах шүүлт нь зөвхөн
            //  өмнөх өдрийн гүйлгээн дээр ажиллана.
            $dateFrom = date('Y-m-d', strtotime('-'. $days .' day'));
            $dateTo = date('Y-m-d', strtotime('+1 day'));
        } else {
            //  Сард 1 удаа ажиллах шүүлт нь зөвхөн
            //  өмнөх сарын гүйлгээн дээр ажиллана.
            $dateFrom = date('Y-m-01', strtotime('-1 month'));
            $dateTo = date('Y-m-01');
        }
        //Төлөлт болоогүй гүйлгээнүүдийн жагсаалтыг авч байна.
        $transactions = self::updateNewTransactions($dateFrom, $dateTo, $limit);
        foreach ($transactions AS $transaction) {
            try {
                $result['rowCount']++;
                $logger->log('--$transID --=' . $transaction['id'], sfFileLogger::INFO);
                $filters = ConfigAssignmentTable::getTransactionFilters(self::getAccType($transaction, $bankAccounts), $filter_day);
                foreach ($filters as $filter) {
                    if (self::checkFilter($transaction, $filter)) {
                        $result['successCount']++;
                        break;
                    }
                }
            } catch (\Exception $ex) {
                // log later
            }
        }
    }

    /**
     * Төлөлт шүүж хөрвүүлэх
     *
     * @param $limit
     * @param $days
     * @return string $report;
     */
    public static function setAssignment($limit, $days)
    {

        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/my-setAssignment.log'));
        $logger->log('--setAssignment start--=', sfFileLogger::INFO);

        mb_internal_encoding('UTF-8');
        //Дансны ангилалыг эндээс авна.
        $bankAccounts = BankAccountTable::getInstance();

        $result = array('rowCount' => 0, 'successCount' => 0);
        try {
            //Өдөр болгон ажиллах шүүлтийг ажиллуулна.
            self::execAssignment($result, $logger, $bankAccounts, ConfigAssignmentTable::FILTER_EVERY_DAY, $limit, $days);
            //Сард нэг удаа ажиллах шүүлтийг ажиллуулна.
            self::execAssignment($result, $logger, $bankAccounts, date('j'), $limit);
        } catch (Exception $exc) {
            $logger->log('execAssignment' . $exc->getMessage(), sfFileLogger::ERR);
        }

        $report = sprintf('Total Transaction = %d; Inserted to Payment = %d', $result['rowCount'], $result['successCount']);

        $logger->log($report, sfFileLogger::INFO);

        return $report;
    }

    /**
     * Төлөлт болгож TEMP төлөвт орсон гүйлгээг буцаах
     *
     * @return void ;
     * @throws Doctrine_Manager_Exception
     */
    public static function rolebackTemp()
    {
        $pdo = Doctrine_Manager::getInstance()->getConnection("transaction")->getDbh();
        # cron-d ajillasan temp tuluvt oruulah
        $stmt = $pdo->prepare("update `transaction` set status=? where status=?");
        $stmt->execute(array(self::STATUS_NEW, self::STATUS_TEMP));
    }

}