<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseSms
 *
 * @author Belbayar
 */
class BaseSms
{

    const CATEGORY_MTOPUP = 4;
    const CATEGORY_ULUSNET_TOPUP = 4;
    const PRODUCT_MTOPUP = 16;
    const PRODUCT_MTOPUP_4 = 949; # M top Up /Gereet 4%/
    const PRODUCT_MTOPUP_5 = 948; # M top Up /Gereet 5%/
    const PRODUCT_MTOPUP_6 = 950; # M top Up /Gereet 6%/
    const PRODUCT_MTOPUP_7 = 2847; # M top Up /Gereet 7%/
    // payment types
    const PAYMENT_BANK = 1;
    const PAYMENT_CASH = 3;
    // banks
    const BANK_GOLOMT = 2;
    const BANK_SAVINGS = 7;
    const BANK_KHAAN = 1;
    const BANK_XAC = 3;
    const BANK_TDB = 4;
    const BANK_CAPITRON = 5;
    const BANK_CAPITAL = 8;

    #
    const HOST_SMS = "http://sms.api/";
    const HOST_SMS_API = "http://sms.api/api/";
    const HOST_SMS_USER_KHAAN = "BOT_KHAAN";
    const HOST_SMS_USER_KHAAN_PASSWORD = "123";
    const HOST_SMS_USER_SAVINGS = "BOT";
    const HOST_SMS_USER_SAVINGS_PASSWORD = "123";
    const HOST_SMS_USER_GOLOMT = "BOT_GOLOMT";
    const HOST_SMS_USER_GOLOMT_PASSWORD = "123";
    const HOST_SMS_USER_TDB = "bot_tdb";
    const HOST_SMS_USER_TDB_PASSWORD = "123";
    const HOST_SMS_USER_XAC = "bot_xac";
    const HOST_SMS_USER_XAC_PASSWORD = "123";
    const HOST_SMS_USER_CAPITAL = "none";
    const HOST_SMS_USER_CAPITAL_PASSWORD = "123";
    const HOST_SMS_USER_CAPITRON = "bot_capitron";
    const HOST_SMS_USER_CAPITRON_PASSWORD = "123";
#
    const GROUP_CUSTOMER = 11;
    const CUSTOMER_CONSUMER = 39;
    const CUSTOMER_CORPORATE = 40;
    const DEALER_SYSTEM = 10000;

    #
    const SD_DEALER = '/[Ss][Dd][0-9]{8}/';
    const AD_SHOP = '/[Aa][Dd][0-9]{4}/';
    const CHAIN_DEALER = '/CD-((Quickpay))/';

    #
    const SALES_PRODUCT_CALLPAYMENT = 73;
    const SALES_PRODUCT_MOBILEOFFICE = 4384;
    const SALES_PRODUCT_IR = 74;
    const SALES_PRODUCT_MP = 947;
    const SALES_PRODUCT_LEASEDLINE = 549;
    
    const TYPE_TOPUP = 5;
    const TYPE_SAPC = 6;

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function insertOutcome($params)
    {
        $b = new sfWebBrowser(array(), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/dealers.log'));

        try {
            if (!$b->post(self::HOST_SMS . "gateway/doOutcomeOrder", $params)->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                //$logger->log('--successful response (eg. 200, 201, etc)--=' . print_r($params, true), sfFileLogger::INFO);
            } else {
                $logger->log('-eeeeeeeeeeeeeeeeeeeeeeeeee--=' . print_r($params, true), sfFileLogger::INFO);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . print_r($params, true), sfFileLogger::INFO);
            return FALSE;
        }
        $xml = simplexml_load_string($b->getResponseXML()->asXML());

        $json = json_encode($xml);
        $response = json_decode($json, TRUE);
        LogTools::setSmsOutcomeParam(print_r($response, TRUE));

        if (isset($response['result']) && $response['result'] == 1) {
            return $response['order_id'];
        } else {
            return FALSE;
        }
    }

    /**
     * Create SalesMS борлуулалт шивэх 
     * @param array $params
     * @return boolean
     */
    public static function insertOutcomeAPI($params)
    {
        extract($params);

        switch (intval($percent)) {
            case 4:
                $productId = BaseSms::PRODUCT_MTOPUP_4;
                break;
            case 5:
                $productId = BaseSms::PRODUCT_MTOPUP_5;
                break;
            case 6:
                $productId = BaseSms::PRODUCT_MTOPUP_6;
                break;
            case 7:
                $productId = BaseSms::PRODUCT_MTOPUP_7;
                break;
            default :
                $productId = BaseSms::PRODUCT_MTOPUP;
                break;
        }

        switch ($vendor) {
            case VendorTable::BANK_KHAAN:
                $user = self::HOST_SMS_USER_KHAAN;
                $password = self::HOST_SMS_USER_KHAAN_PASSWORD;
                $bankId = BaseSms::BANK_KHAAN;
                break;
            case VendorTable::BANK_SAVINGS:
                $user = self::HOST_SMS_USER_SAVINGS;
                $password = self::HOST_SMS_USER_SAVINGS_PASSWORD;
                $bankId = BaseSms::BANK_SAVINGS;
                break;
            case VendorTable::BANK_XAC:
                $user = self::HOST_SMS_USER_XAC;
                $password = self::HOST_SMS_USER_XAC_PASSWORD;
                $bankId = BaseSms::BANK_XAC;
                break;
            case VendorTable::BANK_TDB:
                $user = self::HOST_SMS_USER_TDB;
                $password = self::HOST_SMS_USER_TDB_PASSWORD;
                $bankId = BaseSms::BANK_TDB;
                break;
            case VendorTable::BANK_CAPITAL:
                $user = self::HOST_SMS_USER_CAPITAL;
                $password = self::HOST_SMS_USER_CAPITAL_PASSWORD;
                $bankId = BaseSms::BANK_CAPITAL;
                break;
            case VendorTable::GOLOMT:
                $user = self::HOST_SMS_USER_GOLOMT;
                $password = self::HOST_SMS_USER_GOLOMT_PASSWORD;
                $bankId = BaseSms::BANK_GOLOMT;
                break;
            case VendorTable::BANK_CAPITRON:
                $user = self::HOST_SMS_USER_CAPITRON;
                $password = self::HOST_SMS_USER_CAPITRON_PASSWORD;
                $bankId = BaseSms::BANK_CAPITRON;
                break;
            default:
                return false;
                break;
        }

        $params = array();
        $params['outcomeDate'] = $outcomeDate ? $outcomeDate : date('Y-m-d');
        $params['category'] = BaseSms::CATEGORY_MTOPUP;
        $params['product'] = $productId;
        $params['productPrice'] = $productPrice;
        $params['totalPrice'] = $totalPrice;
        $params['paid'] = $paid;
        $params['customerName'] = $customerName;
        $params['customerPhone'] = $customerPhone;
        $params['customerContract'] = "-";
        $params['outcomeGroupId'] = HrmCore::GROUP_DEALER;
        $params['outcomeUserId'] = $outcomeUserId;
        $params['quantity'] = $quantity;
        $params['payment'] = BaseSms::PAYMENT_BANK;
        $params['bank'] = $bankId;
        $params['vaucher'] = $vaucher;
        $params['comment'] = $comment;
        $params['discount'] = $discount;
        $params['block'] = 1;
        $params['relationId'] = $relationId;
        $params['serialType'] = 1;
        $params['bankgw'] = 1;

        $b = new sfWebBrowser(array('User' => $user, 'Password' => $password), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/smsapi/outcomeCreatApi' . date("Ymd") . '.log'));
        $logger->log('outcome-create--request--=' . json_encode($params), sfFileLogger::INFO);
        try {
            if (!$b->post(self::HOST_SMS_API . "outcome-create.json", json_encode($params))->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                $logger->log('outcome-create--response (eg. 200, 201, etc)--=' . print_r($b->getResponseText(), true), sfFileLogger::INFO);
            } else {
                $logger->log('-response--=' . print_r($b->getResponseText(), true), sfFileLogger::ERR);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . print_r($params, true), sfFileLogger::ERR);
            return FALSE;
        }
        $response = json_decode($b->getResponseText(), TRUE);
        if (isset($response['code']) && $response['code'] == 0) {
            return $response['outcome_order_id'];
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function checkOutcome($banksavings)
    {
        $b = new sfWebBrowser(array(), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));

        try {
            if (!$b->post(self::HOST_SMS . "frontend_dev.php/gateway/doOutcomeOrder", $params)->responseIsError()) {
                // successful response (eg. 200, 201, etc)
            } else {
                return FALSE;
            }
        } catch (Exception $e) {
            return FALSE;
        }

        $xml = simplexml_load_string($b->getResponseXML()->asXML());
        $json = json_encode($xml);
        $response = json_decode($json, TRUE);
        if (isset($response['result']) && $response['result'] == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function deleteOutcome($params)
    {
        $b = new sfWebBrowser(array(), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));

        try {
            if (!$b->post(self::HOST_SMS . "frontend_dev.php/gateway/doDeleteOrder", $params)->responseIsError()) {
                // successful response (eg. 200, 201, etc)
            } else {
                return FALSE;
            }
        } catch (Exception $e) {
            return FALSE;
        }

        $xml = simplexml_load_string($b->getResponseXML()->asXML());
        $json = json_encode($xml);
        $response = json_decode($json, TRUE);
        if (isset($response['result']) && $response['result'] == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * chargeFranchise 
     * @param array $params
     * @return boolean
     */
    public static function chargeAdDealer($code, $amount, $type)
    {
        $chargerUserId = self::getChargeIdByVendor($type);
        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::HOST_SMS . '/account-charger-bank?code=' . $code . '&amount=' . $amount . '&comment=AD' . $type . '&chargerUserId=' . $chargerUserId,
            'transferred' => $amount,
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );

        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);

        $responseText = (string) $b->getResponseText();

        $xml = simplexml_load_string($responseText);

        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }
        // үр дүн
        $responseArr = json_decode(json_encode($xml), TRUE);

        if ($responseArr['Code'] == '0') {
            $return['success'] = TRUE;
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        #
        $return['log_response'] = $responseText;
        return $return;
    }

    /**
     * charge SD dealer 
     * @param array $params
     * @return boolean
     */
    public static function chargeSdDealer($number, $amount, $type)
    {
        $chargerUserId = self::getChargeIdByVendor($type);
        // буцаах утга
        $return = array(
            'success' => FALSE,
            'log_request' => self::HOST_SMS . 'mobile.php/account-charger-bank?number=' . $number . '&amount=' . $amount . '&comment=SD' . $type . '&chargerUserId=' . $chargerUserId,
            'transferred' => $amount,
            'percent' => '0',
            'log_response' => '',
            'error_code' => 0
        );


        // цэнэглэх
        $b = new sfWebBrowser();
        $b->get($return['log_request']);
        $responseText = (string) $b->getResponseText();

        $xml = simplexml_load_string($responseText);

        if (!$responseText) {
            $return['success'] = FALSE;
            $return['log_response'] = print_r(libxml_get_errors(), TRUE);

            return $return;
        }
        // үр дүн
        $responseArr = json_decode(json_encode($xml), TRUE);

        if ($responseArr['Code'] == '0') {
            $return['success'] = TRUE;
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = $responseText;
            return $return;
        }
        #
        $return['log_response'] = $responseText;
        return $return;
    }

    /**
     * AD SHOP 
     *
     * @param int $number
     * @return boolean
     */
    public static function isAdShop($value)
    {
        $matches = array();
        preg_match(self::AD_SHOP, $value, $matches);
        if (isset($matches[0]) && $matches[0]) {
            return $matches[0];
        }
        return false;
    }

    /**
     * is SDDEALER
     *
     * @param int $number
     * @return boolean
     */
    public static function isSdDealer($value)
    {
        $matches = array();
        preg_match(self::SD_DEALER, $value, $matches);
        if (isset($matches[0]) && $matches[0]) {
            return true;
        }
        return false;
    }

    /**
     * is Chain Dealer
     *
     * @param int $value
     * @return boolean
     */
    public static function isChainDealer($value)
    {
        $matches = array();
        preg_match(self::CHAIN_DEALER, $value, $matches);
        if (isset($matches[0]) && $matches[0]) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function insertNoVatpayerApi($bank, $productId, $accountNo, $amount, $quantity, $outcomeUserId, $outcomeGroupId, $outcomeDate = null)
    {
        switch ($bank) {
            case VendorTable::BANK_KHAAN:
                $user = self::HOST_SMS_USER_KHAAN;
                $password = self::HOST_SMS_USER_KHAAN_PASSWORD;
                break;
            case VendorTable::BANK_SAVINGS:
                $user = self::HOST_SMS_USER_SAVINGS;
                $password = self::HOST_SMS_USER_SAVINGS_PASSWORD;
                break;
            case VendorTable::BANK_XAC:
                $user = self::HOST_SMS_USER_XAC;
                $password = self::HOST_SMS_USER_XAC_PASSWORD;
                break;
            case VendorTable::BANK_TDB:
                $user = self::HOST_SMS_USER_TDB;
                $password = self::HOST_SMS_USER_TDB_PASSWORD;
                break;
            case VendorTable::BANK_CAPITAL:
                $user = self::HOST_SMS_USER_CAPITAL;
                $password = self::HOST_SMS_USER_CAPITAL_PASSWORD;
                break;
            case VendorTable::GOLOMT:
                $user = self::HOST_SMS_USER_GOLOMT;
                $password = self::HOST_SMS_USER_GOLOMT_PASSWORD;
                break;
            case VendorTable::BANK_CAPITRON:
                $user = self::HOST_SMS_USER_CAPITRON;
                $password = self::HOST_SMS_USER_CAPITRON_PASSWORD;
                break;
            default:
                return false;
                break;
        }

        $params = array();
        $params['productId'] = $productId;
        $params['accountNo'] = $accountNo;
        $params['amount'] = $amount;
        $params['quantity'] = $quantity;
        $params['outcomeUserId'] = $outcomeUserId;
        $params['outcomeGroupId'] = $outcomeGroupId;
        $params['outcomeDate'] = $outcomeDate ? $outcomeDate : date('Y-m-d');

        $b = new sfWebBrowser(array('User' => $user, 'Password' => $password), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/smsapi/outcomeApi' . date("Ymd") . '.log'));
        $logger->log('--request--=' . json_encode($params), sfFileLogger::INFO);
        try {
            if (!$b->post(self::HOST_SMS_API . "outcome/createVatNopayer.json", json_encode($params))->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                $logger->log('--response (eg. 200, 201, etc)--=' . print_r($b->getResponseText(), true), sfFileLogger::INFO);
            } else {
                $logger->log('-response--=' . print_r($b->getResponseText(), true), sfFileLogger::ERR);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . print_r($params, true), sfFileLogger::ERR);
            return FALSE;
        }
        $response = json_decode($b->getResponseText(), TRUE);
        if (isset($response['code']) && $response['code'] == 0) {
            return $response['outcome_order_id'];
        } else {
            return FALSE;
        }
    }

    /**
     *  Билл циклээр нь SALES -ийн бараатай MAP
     * @param array $params
     * @return boolean
     */
    public static function getProductIdByCycle($cycle)
    {
        $product = 0;
        if (in_array($cycle, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 17, 18, 19, 21, 23, 24, 25, 39, 26, 27, 28, 29, 51, 53, 52))) {
            #Бусад орлого Ярианы төлбөр
            $product = self::SALES_PRODUCT_CALLPAYMENT;
        } elseif (in_array($cycle, array(22))) {
            #Бусад орлого MobileOffice төлбөр / MO /
            $product = self::SALES_PRODUCT_MOBILEOFFICE;
        } elseif (in_array($cycle, array(31))) {
            #Бусад орлого Иридиумын төлбөр / IR /
            $product = self::SALES_PRODUCT_IR;
        } elseif (in_array($cycle, array(30))) {
            #Бусад орлого MP төлөлт
            $product = self::SALES_PRODUCT_MP;
        } elseif (in_array($cycle, array(33))) {
            #Бусад орлого Leased line төлбөр / NS / 
            $product = self::SALES_PRODUCT_LEASEDLINE;
        }
        return $product;
    }

    /**
     *  Банкны BOT user 
     * @param array $vendor
     * @return Integer
     */
    public static function getChargeIdByVendor($vendor)
    {
        switch ($vendor) {
            case 'KHAAN':
                $chargerUserId = HrmCore::BOT_USER_KHAAN;
                break;
            case 'STATE':
                $chargerUserId = HrmCore::BOT_USER;
                break;
            case 'GOLOMT':
                $chargerUserId = HrmCore::BOT_USER_GOLOMT;
                break;
            case 'XAC':
                $chargerUserId = HrmCore::BOT_USER_XAC;
                break;
            case 'TDB':
                $chargerUserId = HrmCore::BOT_USER_TDB;
                break;
            default :
                $chargerUserId = 0;
                break;
        }

        return $chargerUserId;
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function checkBillPayment($date, $accountNo = 0, $number = 0)
    {
        $user = self::HOST_SMS_USER_KHAAN;
        $password = self::HOST_SMS_USER_KHAAN_PASSWORD;
        $b = new sfWebBrowser(array('User' => $user, 'Password' => $password), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/smsapi/outcomeApi' . date("Ymd") . '.log'));
        $logger->log('--request--=' . $date . '--accountNo--=' . $accountNo . '--number--=' . $number, sfFileLogger::INFO);
        try {
            if (!$b->get(self::HOST_SMS_API . "bill/check/payment.json?isdn=$number&contract=$accountNo&date=$date")->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                $logger->log('--response (eg. 200, 201, etc)--=' . print_r($b->getResponseText(), true), sfFileLogger::INFO);
            } else {
                $logger->log('-response--=' . print_r($b->getResponseText(), true), sfFileLogger::ERR);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . $e->getMessage(), sfFileLogger::ERR);
            return FALSE;
        }
        $response = json_decode($b->getResponseText(), TRUE);
        if (isset($response['code']) && $response['code'] == 0) {
            return $response['result'];
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function vatBillPayment($billId)
    {
        $user = self::HOST_SMS_USER_KHAAN;
        $password = self::HOST_SMS_USER_KHAAN_PASSWORD;
        $b = new sfWebBrowser(array('User' => $user, 'Password' => $password), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/smsapi/outcomeApi' . date("Ymd") . '.log'));
        $logger->log('--request--=' . $billId, sfFileLogger::INFO);
        try {
            if (!$b->get(self::HOST_SMS_API . "bill/vat/payment.json?billId=$billId")->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                $logger->log('--response (eg. 200, 201, etc)--=' . print_r($b->getResponseText(), true), sfFileLogger::INFO);
            } else {
                $logger->log('-response--=' . print_r($b->getResponseText(), true), sfFileLogger::ERR);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . $e->getMessage(), sfFileLogger::ERR);
            return FALSE;
        }
        $response = json_decode($b->getResponseText(), TRUE);
        if (isset($response['code']) && $response['code'] == 0) {
            return $response['result'];
        } else {
            return FALSE;
        }
    }
    
    /**
     * 
     * @param array $params
     * @return boolean
     */
    public static function getTopupProductName($bank, $productCode, $type)
    {
        switch ($bank) {
            case VendorTable::BANK_KHAAN:
                $user = self::HOST_SMS_USER_KHAAN;
                $password = self::HOST_SMS_USER_KHAAN_PASSWORD;
                break;
            case VendorTable::BANK_SAVINGS:
                $user = self::HOST_SMS_USER_SAVINGS;
                $password = self::HOST_SMS_USER_SAVINGS_PASSWORD;
                break;
            case VendorTable::BANK_XAC:
                $user = self::HOST_SMS_USER_XAC;
                $password = self::HOST_SMS_USER_XAC_PASSWORD;
                break;
            case VendorTable::BANK_TDB:
                $user = self::HOST_SMS_USER_TDB;
                $password = self::HOST_SMS_USER_TDB_PASSWORD;
                break;
            case VendorTable::BANK_CAPITAL:
                $user = self::HOST_SMS_USER_CAPITAL;
                $password = self::HOST_SMS_USER_CAPITAL_PASSWORD;
                break;
            case VendorTable::GOLOMT:
                $user = self::HOST_SMS_USER_GOLOMT;
                $password = self::HOST_SMS_USER_GOLOMT_PASSWORD;
                break;
            case VendorTable::BANK_CAPITRON:
                $user = self::HOST_SMS_USER_CAPITRON;
                $password = self::HOST_SMS_USER_CAPITRON_PASSWORD;
                break;
            default:
                return false;
                break;
        }

        $params = array();
        if($type == BaseSms::TYPE_TOPUP){
            $params['type'] = 'topup';
        } else if($type == BaseSms::TYPE_SAPC){
            $params['type'] = 'data';
        }

        $b = new sfWebBrowser(array('User' => $user, 'Password' => $password), 'sfCurlAdapter', array('cookie' => true, 'ssl_verify' => false));
        $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/smsapi/topupinfo' . date("Ymd") . '.log'));
        $logger->log('--request--=' . json_encode($params), sfFileLogger::INFO);
        try {
            if (!$b->post(self::HOST_SMS_API . "topup/info.json", json_encode($params))->responseIsError()) {
                // successful response (eg. 200, 201, etc)
                $logger->log('--response (eg. 200, 201, etc)--=' . print_r($b->getResponseText(), true), sfFileLogger::INFO);
            } else {
                $logger->log('-response--=' . print_r($b->getResponseText(), true), sfFileLogger::ERR);
                return FALSE;
            }
        } catch (Exception $e) {
            $logger->log('-catch--=' . print_r($params, true), sfFileLogger::ERR);
            return FALSE;
        }
        $response = json_decode($b->getResponseText(), TRUE);
        if (isset($response['code']) && $response['code'] == "0") {
            if(isset($response['result']) && count($response['result']) > 0){
                foreach ($response['result'] as $product) {
                    if($product['code'] == $productCode){
                        return $product['name'];
                    }
                }
            }
            return 'Задгай нэгж';
        } else {
            return FALSE;
        }
    }
    
}
    
?>
