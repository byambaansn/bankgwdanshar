<?php

/**
 * BankMobixpressTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class BankMobixpressTable extends Doctrine_Table
{

    const STAT_NEW = 1;
    const STAT_PROCESS = 2;
    const STAT_SUCCESS = 3;
    const STAT_FAILED_CHARGE = 4;
    const STAT_FAILED_OUTCOME = 5;
    const STAT_FAILED_DEALER = 6;
    const STAT_FAILED = 7;
    const STAT_FAILED_MAX_AMOUNT = 8;
    const STAT_FAILED_DUPLICATE_AMOUNT = 9;
    const STAT_FAILED_MIN_AMOUNT = 10;
    const STAT_WAIT_TEMP = 11;
    const STAT_BLOCK_DEALER = 12;
    const STAT_TRANS_VALUE_WRONG = 13;
    const MAX_TRY_COUNT = 3;
    const MAX_AMOUNT_LIMIT = 99999999;
    const MIN_AMOUNT_LIMIT = 28800;

    /**
     * Returns an instance of this class.
     *
     * @return object BankMobixpressTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('BankMobixpress');
    }

    /**
     * 
     * @param int $id
     * @param int $status
     * @return BankMobixpress
     */
    public static function retrieveByPK($id, $status = 0)
    {
        $q = Doctrine_Query::create()
                ->from('BankMobixpress')
                ->where('id = ?', $id);

        if ($status) {
            $q->andWhere('status = ?', $status);
        }

        return $q->fetchOne();
    }

    /**
     *
     * @param int $orderId
     * @param int $bankId
     * @return BankMobixpress
     */
    public static function findOneByOrderId($orderId, $bankId = BankTable::MOBIXPRESS)
    {
        $q = Doctrine_Query::create()
                ->from('BankMobixpress')
                ->where('order_id = ?', $orderId)
                ->addWhere('vendor_id = ?', $bankId);

        return $q->fetchOne();
    }

    public static function getByOrderId($orderId)
    {
        $sql = "SELECT * 
                FROM bankgw.bank_mobixpress
                WHERE order_id = $orderId";
        $pdo = Doctrine_Manager::connection()->getDbh();
        return $pdo->query($sql)->fetch();
    }

    /**
     * Дахин цэнэглэлт
     *
     * @param null $bankMobixpress
     * @param int $limit
     * @return boolean
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */
    public static function recharge($bankMobixpress = null, $limit = 50)
    {
        if ($bankMobixpress instanceof BankMobixpress) {
            if ($bankMobixpress->canReCharge()) {
                $bankMobixpress->status = self::STAT_PROCESS;
                $bankMobixpress->save();
            }
            $bankMobixpressRows = array($bankMobixpress);
        } elseif ($bankMobixpress) {
            return FALSE;
        } else {
            $bankMobixpressRows = self::updateForCharge('ADD', $limit);
        }

        foreach ($bankMobixpressRows as $bankMobixpress) {
            try {
                if (in_array($bankMobixpress->status, array(self::STAT_FAILED_CHARGE, self::STAT_FAILED))) {
                    $bankMobixpress->status = self::STAT_PROCESS;
                    $bankMobixpress->save();
                }
                if ($bankMobixpress->status == self::STAT_PROCESS) {
                    $bankMobixpress->try_count++;
                    $bankMobixpress->status = self::STAT_PROCESS;
                    $bankMobixpress->save();

                    $dealer = DealerCharge::getDealer($bankMobixpress->charge_mobile);
                    if ($dealer) {
                        # If the charge amount exceeds the max amount limit
                        if ($bankMobixpress->order_amount > self::MAX_AMOUNT_LIMIT) {
                            $bankMobixpress->status = self::STAT_FAILED_MAX_AMOUNT;
                            $bankMobixpress->save();


                            continue;
                        } elseif ($bankMobixpress->order_amount < self::MIN_AMOUNT_LIMIT) {
                            $bankMobixpress->status = self::STAT_FAILED_MIN_AMOUNT;
                            $bankMobixpress->save();


                            continue;
                        }
                    } else {
                        $bankMobixpress->status = self::STAT_FAILED_DEALER;
                        $bankMobixpress->save();


                        continue;
                    }

                    $logId = LogTools::setLogMobixpressCharge($bankMobixpress->id);

                    $time = -time();
                    # Цэнэглэлт хийх
                    $chargeResult = DealerCharge::charge($bankMobixpress->charge_mobile, $bankMobixpress->order_amount);
                    $time += time();

                    LogTools::updateLogMobixpressCharge($logId, $chargeResult['log_request'], $chargeResult['log_response'], $time);

                    if ($chargeResult['success'] == TRUE) {
                        # Цэнэглэгдсэн мөнгөн дүн болон хувийг хадгалах
                        $bankMobixpress->charge_amount = $chargeResult['transferred'];
                        $bankMobixpress->percent = $chargeResult['percent'];
                        $bankMobixpress->save();
                        $pdo = Doctrine_Manager::connection()->getDbh();
                        $sql = "UPDATE bankgw.bank_mobixpress
                                        SET status = '" . self::STAT_SUCCESS . "'
                                        WHERE id = '" . $bankMobixpress['id'] . "'
                                        AND status = '" . self::STAT_PROCESS . "'
                                        LIMIT 1";
                        $affectedRows = $pdo->exec($sql);
                    } else {
                        if ($chargeResult['error_code'] == self::STAT_FAILED_MAX_AMOUNT) {
                            $bankMobixpress->status = self::STAT_FAILED_MAX_AMOUNT;
                        } elseif ($chargeResult['error_code'] == self::STAT_FAILED_MIN_AMOUNT) {
                            $bankMobixpress->status = self::STAT_FAILED_MIN_AMOUNT;
                        } else {
                            $bankMobixpress->status = self::STAT_FAILED_CHARGE;
                        }
                        $bankMobixpress->save();

                        //
                        continue;
                    }
                } else {
                    if ($bankMobixpress->canReCharge() && $bankMobixpress->try_count < self::MAX_TRY_COUNT) {
                        $bankMobixpress->status = self::STAT_NEW;
                        $bankMobixpress->save();
                    }
                    //used to log here...
                }
            } catch (\Exception $ex) {
                $bankMobixpress->status = self::STAT_FAILED;
                $bankMobixpress->save();
            }
        }

        return TRUE;
    }

    /**
     * Цэнэглэх хуулгууд
     * @param $type
     * @param int $limit
     * @return Doctrine_Collection
     * @throws Doctrine_Manager_Exception
     * @throws Doctrine_Query_Exception
     */

    public static function updateForCharge($type, $limit)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        // FOR UPDATE ажиллахын тулд AUTO COMMIT идэвхгүй байх ёстой
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $pdo->beginTransaction();
        // Шинэ гүйлгээнүүдийн ID-г авч FOR UPDATE-р түгжинэ
        $ids = Doctrine_Query::create()
            ->select('id')
            ->from('BankMobixpress')
            ->whereIn('status', array(self::STAT_NEW))
            ->andWhere('order_type = ?', $type)
            ->limit($limit)
            ->forUpdate(true)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
        // Олдсон ID-нуудтай гүйлгээнүүдийн төлвийг PROCESS болгоно
        if ($ids) {
            Doctrine_Query::create()
                ->update('BankMobixpress')
                ->set('status', self::STAT_PROCESS)
                ->whereIn('id', $ids)
                ->execute();
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            // Төлөв нь шинэчлэгдсэн гүйлгээнүүдээ буцаах
            $qSelect = Doctrine_Query::create()
                ->from('BankMobixpress')
                ->whereIn('id', $ids);
            return $qSelect->execute();
        } else {
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            return null;
        }
    }

    /**
     *
     * @param bool $excel
     * @return \sfDoctrinePager
     * @throws Doctrine_Query_Exception
     * @throws sfException
     */
    public static function getList($excel = false)
    {
        $q = Doctrine_Query::create()
                ->from('BankMobixpress')
                ->where('vendor_id = ?', BankTable::MOBIXPRESS)
                ->orderBy('status DESC, id DESC');

        $request = sfContext::getInstance()->getRequest();

        // mandatory
        $dateFrom = $request->getParameter('dateFrom') ? $request->getParameter('dateFrom') : date('Y-m-d');
        $dateTo = $request->getParameter('dateTo') ? $request->getParameter('dateTo') : date('Y-m-d');
        $q->addWhere("created_at >= ?", $dateFrom . " 00:00:00");
        $q->addWhere("created_at <= ?", $dateTo . " 23:59:59");

        // optional
        $chargedMobile = (int) $request->getParameter('chargedMobile');
        if ($chargedMobile) {
            $q->addWhere('charge_mobile = ?', $chargedMobile);
        }

        // optional
        $orderedMobile = (int) $request->getParameter('orderedMobile');
        if ($orderedMobile) {
            $q->addWhere('order_mobile = ?', $orderedMobile);
        }

        // optional
        $orderId = $request->getParameter('orderId');
        if ($orderId) {
            $q->addWhere('order_id = ?', $orderId);
        }

        // optional
        $status = (int) $request->getParameter('status');
        if ($status) {
            $q->addWhere('status = ?', $status);
        }
        if ($excel) {
            return $q->execute();
        }

        // pager
        $page = (int) $request->getParameter('page', 1);

        $pager = new sfDoctrinePager('BankMobixpress', 100);
        $pager->setQuery($q);
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function getForSelectStatus()
    {
        $status = array(
            self::STAT_NEW => 'Шинэ хуулга',
            self::STAT_PROCESS => 'Цэнэглэж байна',
            self::STAT_SUCCESS => 'Амжилттай цэнэглэсэн',
            self::STAT_FAILED_CHARGE => 'Цэнэглэлт хийгдсэнгүй',
            self::STAT_FAILED_OUTCOME => 'Зарлага хийгдсэнгүй',
            self::STAT_FAILED_DEALER => 'Гэрээт олдсонгүй',
            self::STAT_FAILED => 'Алдаа гарсан',
            self::STAT_FAILED_MAX_AMOUNT => 'Их хэмжээний дүнтэй',
            self::STAT_FAILED_DUPLICATE_AMOUNT => 'Ижил хэмжээний үнийн дүнтэй цэнэглэлт/баталгаажуулах/',
            self::STAT_FAILED_MIN_AMOUNT => 'Цэнэглэх доод хязгаараас бага дүнтэй',
        );
        return $status;
    }

    /**
     * Төлвийн нэр
     *
     * @param int $v
     * @param int $type
     * @param boolean $img
     * @return string
     */
    public static function getStatusName($v, $type = 1, $img = false)
    {
        $v = (int) $v;

        $arr = self::getForSelectStatus($type);

        if (!isset($arr[$v])) {
            return '#';
        }

        if (!$img) {
            return $arr[$v];
        }

        $attr = 'title="' . $arr[$v] . '" alt="' . $arr[$v] . '"';

        switch ($v) {
            case self::STAT_NEW:
                return '<img src="/images/icons/info.png" ' . $attr . ' />';
            case self::STAT_PROCESS:
                return '<img src="/images/icons/process.png" ' . $attr . ' />';
            case self::STAT_FAILED_OUTCOME:
                return '<img src="/images/icons/warning.png" ' . $attr . ' />';
            case self::STAT_SUCCESS:
                return '<img src="/images/icons/save.png" ' . $attr . ' />';
            case self::STAT_FAILED_CHARGE:
                return '<img src="/images/icons/error.png" ' . $attr . ' />';
            case self::STAT_FAILED_DEALER:
                return '<img src="/images/icons/user_delete.png" ' . $attr . ' />';
            case self::STAT_FAILED:
                return '<img src="/images/icons/error.png" ' . $attr . ' />';
            case self::STAT_FAILED_MAX_AMOUNT:
                return '<img src="/images/icons/world.png" ' . $attr . ' />';
            case self::STAT_FAILED_MIN_AMOUNT:
                return '<img src="/images/icons/world.png" ' . $attr . ' />';
            case self::STAT_WAIT_TEMP:
                return '<img src="/images/icons/wait.png" ' . $attr . ' />';
            case self::STAT_BLOCK_DEALER:
                return '<img src="/images/icons/wait.png" ' . $attr . ' />';
            case self::STAT_TRANS_VALUE_WRONG:
                return '<img src="/images/icons/error.png" ' . $attr . ' />';
            default :
                return '#';
        }
    }

}
