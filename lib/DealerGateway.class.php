<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DealerGateway
 *
 * @author Belbayar
 */
class DealerGateway
{

    /**
     * Дилер дугаараар олох 
     * @param String $number
     * @return array Дилерийн мэдээлэл
     */
    public static function findDealerByMobile($number, $logger)
    {
        if (!$number) {
            return false;
        }

        $caller = new AgentByPhone();
        $caller->setUserId($number);
        $caller->setXmlRequest($number);
        $caller->call();
        $caller->setAttr();

        $arrRes = json_decode($caller->getXmlResponseRaw(), TRUE);
        if ($arrRes['code'] == '200') {
            $logger->log($number . '--findDealerByMobile-- :' . $caller->getXmlResponseRaw(), sfFileLogger::INFO);
            return $arrRes['result'][0];
        } else {
            $logger->log('--HTTP response failed :getProductOptions-- :' . $caller->getXmlResponseRaw(), sfFileLogger::ERR);
        }
        return null;
    }

    /**
     *  Татан авалтын бараа олох 
     * @param String $dealerId 
     * @return array Барааны жагсаалт
     */
    public static function getMtopupProduct($dealerId, $logger)
    {
        $list = self::getProductOptions($dealerId, $logger);
        $resArr = array();

        $yml = sfYaml::load(sfConfig::get('sf_config_dir') . '/app_dealer.yml');

        $provCode = $yml['all']['dealer']['provCode'];
        if ($provCode) {
            if ($logger) {
                $logger->log('--Read dealer provCode-- :' . $provCode, sfFileLogger::INFO);
            }
        } else {
            $logger->log('--Read dealer provCode-- : Not found provCode from ' . sfConfig::get('sf_config_dir') . '/app_dealer.yml', sfFileLogger::ERR);
        }

        foreach ($list as $row) {
            if (isset($row['productOpt']['provOpt']) && isset($row['productOpt']['provOpt']['provision']) && isset($row['productOpt']['provOpt']['provision']['provCode']) && $row['productOpt']['provOpt']['provision']['provCode'] == $provCode) {
                $resArr['productId'] = $row['productOpt']['productId'];
                $resArr['prodOptId'] = $row['productOpt']['prodOptId'];
                $resArr['provCode'] = $row['productOpt']['provOpt']['provision']['provCode'];
                $resArr['calcType'] = $row['commission']['calcType'];
            }
        }
        return $resArr;
    }

    /**
     *  Барааны сонголтууд авах
     * @param String $dealerId 
     * @return array Барааны жагсаалт
     */
    public static function getProductOptions($dealerId, $logger)
    {

        $caller = new AgentProductOptions();
        $caller->setUserId($dealerId);
        $caller->setXmlRequest($dealerId);
        $caller->call();
        $caller->setAttr();

        $arrRes = json_decode($caller->getXmlResponseRaw(), TRUE);
        if ($arrRes['code'] == '200') {
            return $arrRes['result'];
        } else {
            $logger->log('--HTTP response failed :getProductOptions-- :' . $caller->getXmlResponseRaw(), sfFileLogger::ERR);
        }
        return null;
    }

    /**
     * Дилер цэнэглэлт
     * @param String $number утасны дугаар
     * @param integer $amount төлөх дүн
     * @return array Цэнэгэлэлт орсон тухай
     */
    public static function charge($dealerId, $amount, $bank, $logger)
    {
        $return['log_request'] = 'logTable:log_gateway_dealer,type:AgentConfirm';
        if (!$dealerId || !$amount) {
            $result = "-charge function-- not found params  dealerId:$dealerId,amount:$amount";
            $logger->log($result, sfFileLogger::WARNING);
            $return['success'] = FALSE;
            $return['log_response'] = $result;
            return $return;
        }


        // цэнэглэх
        $product = DealerGateway::getMtopupProduct($dealerId, $logger);

        if ($product['prodOptId']) {
            $result = self::chargeAgent($dealerId, $product['prodOptId'], $product['calcType'], $amount, $bank, $logger);
        } else {
            $logger->log("--chargeDealer-- prodOptId not found  dealerId:$dealerId", sfFileLogger::WARNING);
        }

        if ($result['code'] == '200') {
            $return['success'] = TRUE;
            #
            $responseText = $result['info'];
            $result = $result['result'];
            $totalAmount = (double) $result['transaction']['totalAmount'];
            $percent = 100 * (1 - $amount / $totalAmount);
            $return['transferred'] = $totalAmount;
            $return['percent'] = $percent;
            $return['log_response'] = $responseText;
            return $return;
        } else {
            $return['success'] = FALSE;
            $return['log_response'] = print_r($result, TRUE);
            if ($result['code'] == '400') {
                $return['error_code'] = BankKhaanTable::STAT_FAILED_MIN_AMOUNT;
            }
            return $return;
        }
    }

    /**
     * Дилер цэнэглэлт
     * @param String $number утасны дугаар
     * @param String $card profile code
     * @param integer $userId төлөх дүн
     * @param integer $chargerId төлөлт хийж буй огноо
     * @return array Цэнэгэлэлт орсон тухай
     */
    public static function chargeAgent($dealerId, $productOptId, $calcType, $amount, $bank, $logger)
    {
        if (!$dealerId || !$productOptId || !$amount) {
            $logger->log("--not found parameters --  dealerId:$dealerId,productOptId:$productOptId,amount:$amount", sfFileLogger::ERR);
            return null;
        }
        $caller = new AgentConfirm();
        $caller->setUserId($dealerId);
        $caller->setXmlRequest($dealerId, $calcType, $productOptId, $amount, $bank);
        $caller->call();
        $caller->setAttr();
        $arrRes = json_decode($caller->getXmlResponseRaw(), TRUE);
        if ($arrRes['code'] != '200') {
            $logger->log('--HTTP response failed :charge-- :' . $caller->getXmlResponseRaw(), sfFileLogger::ERR);
        }
        return $arrRes;
    }

    /**
     * CheckTopup
     * @param String $number утасны дугаар
     * @param String $card  profile code
     * @return array дугаарын мэдээлэл ирнэ
     */
    public static function checkTopup($number, $userId = 0)
    {
        if (!$number) {
            return null;
        }
        $caller = new StgwFunc2();
        $caller->setUserId($userId);
        $caller->setXmlRequest($number);
        $caller->call();
        $caller->setAttr();
        $arrRes = AppTools::xmlToArray($caller->getXmlResponseRaw());

        return $arrRes;
    }

}
