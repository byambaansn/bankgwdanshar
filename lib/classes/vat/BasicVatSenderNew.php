<?php

/**
 * Description of NewVatSender
 *
 * @author enkhtsetseg.da
 */
class BasicVatSenderNew
{
    
    /**
     * Create and Send Vat to customer
     * @param int $number
     * @return mixed
     */
    public static function createAndSendVat($isdn, $custno, $amount, $bankAccountNo, $paymentCode, $bankName, $productName, $productCode, $email, $company)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $api = $yml['all']['vatsendernew']['api'];
        $type = 'createAndSendVat';
        $header = array();
        $header[] = "Content-Type: application/json";
        $url = $api . 'vat-channel-offline/rest/receive';
        
        $detail = array();
        $detail['name']=$productName;
        $detail['code']= 8413;
        $detail['qty']=1;
        $detail['price']=(int) $amount;
        $detail['profile']= $custno;

        $data = array();
        $data["custnum"] = null;
        $data["isdnB"] = $isdn;
        $data["amount"] = $amount;
        $data["register"] = null;
        $data["email"] = null;
        $data["sendmail"] = true;
        if($email){
            $data["email"] = $email;
        }
        $data["company"] = 'mobicom';
        if($company){
            $data["company"] = $company;
        }
        $data["bankAccountNo"] = $bankAccountNo;
        $data["bankName"] = $bankName;
        $data["paymentCode"] = $paymentCode;
        $data["smsOrderId"] = null;
        $data["autovat"] = true;
        $data["channel"] = 'bank';
        $data["group"] = false;
        $data["productList"] = array($detail);
        $body = json_encode($data);
        $result = self::curlCall($url, $body, $header, true);
        $httpcode = $result['HttpCode'];
        $resp = $result['Result'];
        $response = array();
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if (isset($res['success']) && $res['success'] == true) {
                $response = $res;
            }
        } else {
            $res = json_decode($result['Result'], true);
            $response['Message'] = $res['info'];
            $response['Result'] = $res['result'];
        }
        self::logAccess($isdn,$httpcode,$url,$result['Result'], $type, $body);
        return $response;
    }
    public static function createAndSendVatTeacher($isdn, $custno, $amount, $bankAccountNo, $paymentCode, $bankName, $productName, $productCode, $email, $company, $register)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $api = $yml['all']['vatsendernew']['api'];
        $type = 'createAndSendVatTeacher';
        $header = array();
        $header[] = "Content-Type: application/json";
        $url = $api . 'vat-channel-offline/rest/receive';

        $detail = array();
        $detail['name']=$productName;
        $detail['code']= 8413;
        $detail['qty']=1;
        $detail['price']=(int) $amount;
        $detail['profile']= $custno;

        $data = array();
        $data["custnum"] = null;
        $data["isdnB"] = null;
        $data["amount"] = $amount;
        $data["register"] = $register;
        $data["email"] = null;
        $data["sendmail"] = false;
        $data["company"] = 'mobicom';
        if($company){
            $data["company"] = $company;
        }
        $data["bankAccountNo"] = $bankAccountNo;
        $data["bankName"] = $bankName;
        $data["paymentCode"] = 'BNC0B';
        $data["smsOrderId"] = null;
        $data["autovat"] = false;
        $data["sendsms"] = false;
        $data["channel"] = 'bank';
        $data["group"] = false;
        $data["productList"] = array($detail);
        $body = json_encode($data);
        $result = self::curlCall($url, $body, $header, true);
        $httpcode = $result['HttpCode'];
        $resp = $result['Result'];
        $response = array();
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if (isset($res['success']) && $res['success'] == true) {
                $response = $res;
            }
        } else {
            $res = json_decode($result['Result'], true);
            $response['Message'] = $res['info'];
            $response['Result'] = $res['result'];
        }
        self::logAccess($isdn,$httpcode,$url,$result['Result'], $type, $body);
        return $response;
    }

    
    /**
     * Get customer VAT list
     * @param int $number
     * @return mixed
     */
    public static function getVatBillList($start, $end, $custNum = null, $staff = null, $limit = 10, $offset = 0)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $api = $yml['all']['vatsendernew']['api'];
        $type = 'getVatBillList';
        $header = array();
        $header[] = "Content-Type: application/json";
        $url = $api . 'vat-report/rest/report/vat';
        
        $data = array();
        $data["custnum"] = $custNum;
        $data["startdate"] = $start;
        $data["enddate"] = $end;
        $data["staff"] = $staff;
        $data["limit"] = $limit;
        $data["offset"] = $offset;

        $body = json_encode($data);
        
        $result = self::curlCall($url, $body, $header, true);
        $httpcode = $result['HttpCode'];
        $resp = $result['Result'];
        $response = array();
        $response['Code'] = $httpcode;
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if (isset($res['list'])) {
                $response['Result'] = $res['list'];
            }
        } else {
            $res = json_decode($result['Result'], true);
            $response['Message'] = $res['info'];
            $response['Result'] = [];
        }
        self::logAccess($custNum,$response['Code'],$url,$result['Result'], $type, $body);
        return $response['Result'];
    }
    
    /**
     * Return Vat to customer
     * @param int $number
     * @return mixed
     */
    public static function returnVat($isdn, $billId, $date, $description, $amount, $lottery, $vat, $email, $staff)
    {
        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app.yml');
        $api = $yml['all']['vatsendernew']['api'];
        $type = 'returnVat';
        $header = array();
        $header[] = "Content-Type: application/json";
        $url = $api . 'vat-channel-return/rest/receive';

        $detail = array();
        $detail['amount']=$amount;
        $detail['lottery']=$lottery;
        $detail['unitPrice']=(int) $amount;
        $detail['vat']=$vat;

        $data = array();
        $data["isdn"] = $isdn;
        $data["email"] = $email;
        $data["date"] = $date;
        $data["returnBillId"] = $billId;
        $data["description"] = $description;
        $data["sendsms"] = true;
        $data["sendmail"] = true;
        $data["staff"] = $staff;
        $data["billDetailList"] = $detail;
        $data["channel"] = "bank";
        
        $body = json_encode($data);
        $result = self::curlCall($url, $body, $header, true);
        $httpcode = $result['HttpCode'];
        $resp = $result['Result'];
        $response = array();
//        $response['Code'] = $httpcode;
        if ($httpcode == 200) {
            $res = json_decode($result['Result'], true);
            if (isset($res['success']) && $res['success'] == true) {
                $response = $res;
            }
        } else {
            $res = json_decode($result['Result'], true);
            $response['Message'] = $res['info'];
            $response['Result'] = $res['result'];
        }
        self::logAccess($isdn,$httpcode,$url,$result['Result'], $type, $body);
        return $response;
    }

    /**
     * Curl Request
     * @param array $header
     * @return mixed
     */
    public static function curlCall($url, $body, $header, $post = false)
    {
        // Initialisation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Request
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_POST, $post);
        }
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_TIMEOUT, 999);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
//        curl_setopt($ch, CURLOPT_PROXY, "172.17.56.31:8080");
        // execute the connexion
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
        }
        // Close it
        curl_close($ch);

        $response = array();
        $response['HttpCode'] = $httpcode;
        $response['Result'] = $result;

        return $response;
    }

    public static function logAccess($number, $responseCode, $url, $response, $type, $request)
    {
        $pdo = LogTools::getLogPDO();

        $sql = "INSERT INTO bankgw_log.`log_gateway_vat` (`number` ,`type_s`, `user_id`, `response_code`, `url`, `request_xml` ,`response_xml`)VALUES ('" .
            $number . "', '".$type."', 0," . $responseCode . ", '" . $url . "', '".$request."', '" . $response . "');";
        $pdo->exec($sql);
    }
    
    public static function getVatReturnBillListFormatter($billId, $billList)
    {
        $result = array();
        foreach ($billList as $key => $value) {
            if(isset($billList[$key]['ebarimt'])){
                if(isset($billList[$key]['ebarimt']['billId']) ){
                    if($billList[$key]['ebarimt']['billId'] == $billId){
                        $result = $billList[$key];
                    }
                }
            }

        }
        return $result;
    }

}

