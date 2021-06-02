<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NTCGateway
 *
 * @author Belbayar
 */
class NTCGateway
{

    const QUICKPAY = '/Quickpay/';

    /**
     * Сүлжээт Дилер цэнэглэлт
     * @param String $vendor chain dealer
     * @param String $invoiceId хүсэлтийн дугаар
     * @param Date $dater огноо
     * @param Integer $chargerId цэнэглэж буй хүн
     * @param Double $amount цэнэглэлтийн дүн
     * @return array Данс цэнэглэлт хийх тухай
     */
    public static function charge($vendor, $transactionId, $dater, $type, $amount, $transValue, $bankOrderId)
    {
        if (!$vendor || !$transactionId || !$dater || !$amount) {
            return null;
        }
        $invoiceId = $transactionId . "" . $bankOrderId;
        $vendorUser = self::getVendorUsername($transValue);
        $chargerId = BaseSms::getChargeIdByVendor($type);
        $caller = new ChainDealerCharge();
        $caller->setNumber($vendorUser);
        $caller->setOrderId($bankOrderId);
        $caller->setUserId($vendor);
        $caller->setXmlRequest($vendorUser, $invoiceId, $dater, $chargerId, $amount);
        $caller->call();
        $caller->setAttr();
        return $caller->getResponse();
    }

    public static function getVendorUsername($transValue)
    {
        if (self::isQuickpay($transValue)) {
            return 'quickpay';
        }

        return null;
    }

    /**
     * is Quickpay
     *
     * @param int $value
     * @return boolean
     */
    public static function isQuickpay($value)
    {
        $matches = array();
        preg_match(self::QUICKPAY, $value, $matches);
        if (isset($matches[0]) && $matches[0]) {
            return true;
        }
        return false;
    }

}
