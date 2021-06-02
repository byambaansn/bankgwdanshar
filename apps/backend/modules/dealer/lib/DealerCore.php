<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DealerCore
 *
 * @author belbayar
 */
class DealerCore
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
    const STAT_CORRECTION = 11; // банкны залруулга
    const STAT_CORRECTION_SUCCESS = 12; // банкны залруулга амжилттай
    const STAT_CORRECTION_FAILED = 13; // банкны залруулга амжилтгүй
    const STAT_BANKPAYMENT_AMOUNT = 14; // Гэрээний үлдэгдэлээс зөрүүтэй 3000
    const STAT_BANKPAYMENT_TRANS_VALUE = 15; // Гүйлгээний утга буруу

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
            self::STAT_CORRECTION => 'Банкны залруулга',
            self::STAT_CORRECTION_SUCCESS => 'Банкны залруулга амжилттай',
            self::STAT_CORRECTION_SUCCESS => 'Банкны залруулга амжилтгүй',
        );
        return $status;
    }

    /**
     * Төлвийн нэр
     * 
     * @param int $v
     * @param boolean $img
     * @return string
     */
    public static function getStatusName($v, $img = false)
    {
        $v = (int) $v;

        $arr = self::getForSelectStatus();

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
            case self::STAT_CORRECTION:
                return '<img src="/images/icons/adjustment.png" ' . $attr . ' />';
            case self::STAT_CORRECTION_SUCCESS:
                return '<img src="/images/icons/adjustment_green.png" ' . $attr . ' />';
            case self::STAT_CORRECTION_FAILED:
                return '<img src="/images/icons/adjustment_red.png" ' . $attr . ' />';
            default :
                return '#';
        }
    }

    /**
     * Амжилтгүй төлвүүд
     * @return Array
     */
    public static function getStatusesFailed()
    {
        $status = self::getForSelectStatus();
        unset($status[self::STAT_NEW]);
        unset($status[self::STAT_SUCCESS]);
        unset($status[self::STAT_PROCESS]);

        return array_keys($status);
    }

    /**
     * 
     * @return \sfDoctrinePager
     */
    public static function getList($dateFrom, $dateTo, $status, $bank = 0, $orderId = 0, $chargedMobile = 0, $orderedMobile = 0, $allowMerge = false)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $where = array();
        $where[] = "bank.created_at >='$dateFrom 00:00:00'";
        $where[] = "bank.created_at <='$dateTo 23:59:59'";
        //$where[] = "bank.order_type NOT IN('SUB',0)";
        $where[] = "bank.order_type IN('ADD','income','C','1')";
#validate date interval
        $daysDiff = AppTools::getDays($dateFrom, $dateTo);
        if ($daysDiff > 31 && !$allowMerge) {
            sfContext::getInstance()->getUser()->setFlash('warning', 'Та 31 хоногоос илүү шүүх боломжгүй');
            return array();
        }
        if ($bank) {
            if (is_array($bank)) {
                $where[] = "bank.vendor_id IN (" . implode(',', $bank) . ")";
            } else {
                $where[] = "bank.vendor_id ='$bank'";
            }
        }
        // optional
        if ($chargedMobile) {
            $where[] = "bank.charge_mobile ='$chargedMobile'";
        }
        // optional
        if ($orderedMobile) {
            $where[] = "bank.order_mobile ='$orderedMobile'";
        }
        // optional
        if ($orderId) {
            $where[] = "bank.order_id ='$orderId'";
        }

// optional
        if ($status) {
            if (is_array($status)) {
                $where[] = "bank.status IN(" . implode(',', $status) . ")";
            } else {
                $where[] = "bank.status ='$status'";
            }
        }
        $where = implode(' AND ', $where);

        $bankQuery = array();
        $select = "
            bank.id,     
            bank.charge_mobile,     
            bank.charge_amount,     
            bank.percent,     
            bank.bank_account,
            bank.order_id,
            bank.order_p,    
            bank.order_mobile,    
            bank.order_date,
            bank.order_type,
            bank.order_type,
            bank.order_amount,
            bank.order_s,
            bank.status,
            bank.try_count,
            bank.created_at,
            bank.updated_at,
            bank.vendor_id,
            v.name as bank_name";
# khaan
        $bankQuery[] = "(SELECT
     $select
FROM bank_khaan bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankKhaanAccountTable::ACCOUNT_DEALER, BankKhaanAccountTable::ACCOUNT_DEALER_MOBICOM)) . "') 
       AND $where )";
# golomt
        $bankQuery[] = "(SELECT
     $select
FROM bank_golomt bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankGolomtTable::ACCOUNT_DEALER, BankGolomtTable::ACCOUNT_DEALER_MOBICOM)) . "') 
       AND $where )";
# savings
        $bankQuery[] = "(SELECT
     $select
FROM bank_savings bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankSavingsAccountTable::ACCOUNT_DEALER)) . "') 
       AND $where )";
# xac
        $bankQuery[] = "(SELECT
     $select
FROM bank_xac bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankXacAccountTable::ACCOUNT_DEALER, BankXacAccountTable::ACCOUNT_DEALER_MOBICOM)) . "') 
       AND $where )";
# tdb
        $bankQuery[] = "(SELECT
     $select
FROM bank_tdb bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankTdbTable::ACCOUNT_DEALER, BankTdbTable::ACCOUNT_DEALER_MOBICOM)) . "') 
       AND $where )";
# capital
        $bankQuery[] = "(SELECT
     $select
FROM bank_capital bank 
left join vendor v ON v.id=bank.vendor_id
WHERE bank.bank_account IN ('" . implode("','", array(BankCapitalAccountTable::ACCOUNT_DEALER, BankCapitalAccountTable::ACCOUNT_DEALER_MOBICOM)) . "') 
       AND $where )";
# candy
        $bankQuery[] = "(SELECT
     $select
FROM bank_candy bank 
left join vendor v ON v.id=bank.vendor_id
WHERE $where )";
        $query = implode(' UNION ALL ', $bankQuery);
        $query .= " ORDER BY created_at desc";
//        echo $query;
//        die();
        $rows = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * Status link
     * @return \sfDoctrinePager
     */
    public static function getStatusLink($vendorId)
    {
        switch ($vendorId) {
            case VendorTable::BANK_KHAAN:
                $link = '@bank_khaan_status';
                break;
            case VendorTable::BANK_SAVINGS:
                $link = '@bank_savings_status';
                break;
            case VendorTable::BANK_XAC:
                $link = '@bank_xac_status';
                break;
            case VendorTable::GOLOMT:
                $link = '@bank_golomt_status';
                break;
            case VendorTable::BANK_TDB:
                $link = '@bank_tdb_status';
                break;
            case VendorTable::BANK_CAPITAL:
                $link = '@bank_capital_status';
                break;
            case VendorTable::CANDY:
                $link = '@bank_candy_status';
                break;
            default:
                $link = '@homepage';
                break;
        }
        return $link;
    }

    public static function reoutcome($bank, $dealer, $date, $dealerAgent, $percent)
    {
        if ($bank instanceof BankKhaan) {
            return BankKhaanTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankSavings) {
            return BankSavingsTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankXac) {
            return BankXacTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankTdb) {
            return BankTdbTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankGolomt) {
            return BankGolomtTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankCapital) {
            return BankCapitalTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankCapitron) {
            return BankCapitronTable::reoutcome($bank, $dealer, $date, $dealerAgent, $percent);
        } elseif ($bank instanceof BankCandy) {
            return true;
        }
        return null;
    }

}
