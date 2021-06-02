<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CandyLoanCore
 *
 * @author belbayar
 */
class CandyLoanCore
{

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function candyLoanAccounts()
    {
        $status = array(
            BankKhaanAccountTable::ACCOUNT_MOBIFINANCE_CANDY,
            BankTdbTable::ACCOUNT_MOBIFINANCE_CANDY,
            BankGolomtTable::ACCOUNT_MOBIFINANCE_CANDY,
            BankXacAccountTable::ACCOUNT_MOBIFINANCE_CANDY,
            BankSavingsTable::ACCOUNT_MOBIFINANCE_CANDY,
        );
        return $status;
    }

    /**
     * ТӨРӨЛ
     * 
     * @return array
     */
    public static function getForSelectType()
    {
        $status = array(
            8 => 'QPAY',
            9 => 'CASH IN',
            10 => 'LOAN',
            11 => 'AGENT'
        );
        return $status;
    }

    /**
     * Амжилтгүй төлвүүд
     * @return Array
     */
    public static function getStatusesFailed()
    {
        $status = BankpaymentTable::getForSelectStatus();
        unset($status[BankpaymentTable::STAT_NEW]);
        unset($status[BankpaymentTable::STAT_SUCCESS]);
        unset($status[BankpaymentTable::STAT_PROCESS]);

        return array_keys($status);
    }

    /**
     * 
     * @return \sfDoctrinePager
     */
    public static function getList($dateFrom, $dateTo, $status, $bank = 0, $keyword = 0, $type = 0)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $where = array();
        $whereBP = array();
        $where[] = "bank.created_at >= '$dateFrom 00:00:00'";
        $where[] = "bank.created_at <= '$dateTo 23:59:59'";
        $where[] = "bank.order_type IN ('ADD', 'C', 'income',1)";
#validate date interval
        $daysDiff = AppTools::getDays($dateFrom, $dateTo);
        if ($daysDiff > 31) {
            sfContext::getInstance()->getUser()->setFlash('warning', 'Та 31 хоногоос илүү шүүх боломжгүй');
            return array();
        }
        if ($bank) {
            if (is_array($bank)) {
                $where[] = "bank.vendor_id IN (" . implode(',', $bank) . ")";
                $whereBP[] = "bp.vendor_id IN (" . implode(',', $bank) . ")";
            } else {
                $where[] = "bank.vendor_id ='$bank'";
                $whereBP[] = "bp.vendor_id ='$bank'";
            }
        }
        
        if ($type) {
            if (is_array($type)) {
                $whereBP[] = "bp.type IN(" . implode(',', $type) . ")";
            } else {
                $whereBP[] = "bp.type ='$type'";
            }
        } else {
            $whereBP[] = "bp.type IN(8, 9, 10, 11)";
        }
        
        // optional
        if ($keyword) {
            $where[] = "(bank.order_id ='$keyword' OR bank.order_mobile ='$keyword' OR bank.charge_mobile ='$keyword' OR bank.order_p LIKE '%$keyword%' OR bank.order_amount='$keyword')";
            $whereBP[] = "(bp.bank_order_id ='$keyword' OR bp.number ='$keyword' OR bp.paid_amount='$keyword')";
        }
        
        if ($status) {
            if (is_array($status)) {
                $whereBP[] = "bp.status IN(" . implode(',', $status) . ")";
            } else {
                $whereBP[] = "bp.status ='$status'";
            }
        }
        
        $where = implode(' AND ', $where);
        $whereBP = implode(' AND ', $whereBP);

        $query = "SELECT
            bp.id,
            bp.bank_order_id,
            bp.vendor_id,
            bp.type,
            bp.number,
            bp.paid_amount,
            bp.status,
            bp.created_at,
            bp.updated_at,
            v.name as bank_name,
            bp.parent_id,
            bp.try_count,
            bk.bank_account
            FROM bankpayment bp 
            INNER JOIN ";
        
        $bankQuery = array();
        $select = "
            bank.id,     
            bank.bank_account,
            bank.vendor_id 
            ";
# khaan
        $bankQuery[] = "(SELECT
     $select
FROM bank_khaan bank 
WHERE $where AND bank.bank_account IN ('" . implode("','", array(BankKhaanAccountTable::ACCOUNT_MOBIFINANCE_CANDY, BankKhaanAccountTable::ACCOUNT_CANDY_CASHIN)) . "') )";
# golomt
        $bankQuery[] = "(SELECT
     $select
FROM bank_golomt bank 
WHERE $where AND  bank.bank_account IN ('" . implode("','", array(BankGolomtTable::ACCOUNT_MOBIFINANCE_CANDY, BankGolomtTable::ACCOUNT_CANDY_CASHIN)) . "') )";
# savings
        $bankQuery[] = "(SELECT
     $select
FROM bank_savings bank 
WHERE $where AND bank.bank_account IN ('" . implode("','", array(BankSavingsTable::ACCOUNT_MOBIFINANCE_CANDY)) . "') )";
# xac
        $bankQuery[] = "(SELECT
     $select
FROM bank_xac bank 
WHERE $where AND bank.bank_account IN ('" . implode("','", array(BankXacAccountTable::ACCOUNT_MOBIFINANCE_CANDY, BankXacAccountTable::ACCOUNT_CANDY_CASHIN)) . "') )";
# tdb
        $bankQuery[] = "(SELECT
     $select
FROM bank_tdb bank 
WHERE $where AND bank.bank_account IN ('" . implode("','", array(BankTdbTable::ACCOUNT_MOBIFINANCE_CANDY, BankTdbTable::ACCOUNT_CANDY_CASHIN)) . "')  )";
# capital
        $bankQuery[] = "(SELECT
     $select
FROM bank_capital bank 
WHERE $where AND bank.bank_account IN ('" . implode("','", array(BankCapitalAccountTable::ACCOUNT_MOBIFINANCE_CANDY)) . "') )";
        $query .= "(" . implode(' UNION ALL ', $bankQuery) . ") bk ON bp.bank_order_id = bk.id AND bp.vendor_id = bk.vendor_id 
                LEFT OUTER JOIN vendor v ON v.id = bp.vendor_id
                WHERE $whereBP
                ORDER BY bp.created_at";
//        echo $query;
//        die();
        $rows = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public static function recharge($bankOrderId, $number = 0, $bankpaymentId)
    {
        $result = array();
        $result['order_id'] = "0";
        $result['info'] = false;
        if (!$bankOrderId) {
            return $result;
        }
        $bankpayment = BankpaymentTable::retrieveByPK($bankpaymentId);
        $result = BankpaymentTable::chargeLoyaltyApi($bankpayment, $number);
        $result['info'] = $result;
        if ($bank) {
            $result['order_id'] = $bank['order_id'];
            $result['status'] = $bank['status'];
        }
        return $result;
    }

    public static function rechargeCashin($vendorId, $bankOrderId, $number = 0)
    {
        $result = array();
        $result['order_id'] = "0";
        $result['info'] = false;
        if (!$bankOrderId) {
            return $result;
        }
        switch ($vendorId) {
            case VendorTable::BANK_KHAAN:
                $bank = BankKhaanTable::retrieveByPK($bankOrderId);
                $result = BankKhaanTable::chargeLoyaltyCashin($bank, $number);
                break;
            case VendorTable::BANK_SAVINGS:
                $bank = BankSavingsTable::retrieveByPK($bankOrderId);
                $result = BankSavingsTable::chargeLoyaltyCashin($bank, $number);
                break;
            case VendorTable::BANK_XAC:
                $bank = BankXacTable::retrieveByPK($bankOrderId);
                $result = BankXacTable::chargeLoyaltyCashin($bank, $number);
                break;
            case VendorTable::GOLOMT:
                $bank = BankGolomtTable::retrieveByPK($bankOrderId);
                $result = BankGolomtTable::chargeLoyaltyCashin($bank, $number);
                break;
            case VendorTable::BANK_TDB:
                $bank = BankTdbTable::retrieveByPK($bankOrderId);
                $result = BankTdbTable::chargeLoyaltyCashin($bank, $number);
                break;
            case VendorTable::BANK_CAPITAL:
                $bank = BankCapitalTable::retrieveByPK($bankOrderId);
                $result = BankCapitalTable::chargeLoyaltyCashin($bank, $number);
                break;
            default:
                $result = false;
                break;
        }
        $result['info'] = $result;
        if ($bank) {
            $result['order_id'] = $bank['order_id'];
            $result['status'] = $bank['status'];
        }
        return $result;
    }

    public static function typeToName($type)
    {
        if ($type == 8) {
            $type = 'QPAY';
        } elseif ($type == 9) {
            $type = 'CASH-IN';
        } elseif ($type == 10) {
            $type = 'LOAN';
        } elseif ($type == 11) {
            $type = 'AGENT';
        }
        return $type;
    }

}