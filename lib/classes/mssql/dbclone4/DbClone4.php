<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbClone4
 *
 * @author khishigdelger.b
 */
class DbClone4
{
    public static function getVatReturnBillList($isdn, $contNum, $date)
    {
//        $conn = new connection();
//        $query = "SELECT c_isdn, c_custnum, c_billId, c_amount, c_posresdate, CONVERT(VARCHAR(10),c_posresdate,111) AS resDate
//                  FROM t_vat_log_new WITH(NOLOCK)
//                    WHERE c_billid IS NOT NULL AND c_posresponse IS NOT NULL
//                    AND (c_custnum = '".$contNum."' OR c_isdn = '".$isdn."')
//                    AND c_posresdate BETWEEN DATEADD(MONTH,-1,'".$date."') AND '".$date."'";
//        $result = $conn->queryToArray($query);
//        return $result['res'];
        $result = BaseSms::checkBillPayment($date, $contNum, $isdn);
        return $result['res'];
    }
    
    public static function getVatReturnBill($billId)
    {
//        $conn = new connection();
//        $query = "SELECT c_isdn, c_custnum, c_billId, c_amount, c_group, c_posresdate, dbo.getLottery(c_posresponse) AS lotteryId
//                  FROM t_vat_log_new WITH(NOLOCK) WHERE c_billid = '".$billId."'";
//        $result = $conn->queryToArray($query);
//        return $result['res'];
        $result = BaseSms::vatBillPayment($billId);
        return $result['res'];
    }
}
