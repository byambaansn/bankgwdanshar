<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostGateway
 *
 * @author Belbayar
 */
class PostGateway
{

    /**
     * Payment төлбөр төлөлт
     * @param String $number утасны дугаар
     * @param String $contract гэрээний дугаар
     * @param Double $amount төлөх дүн
     * @param DateTime $paidDate төлөлт хийж буй огноо
     * @param Strnig $paymentCode төлөлтийн код
     * @return array Төлөлт орсон тухай
     */
    public static function doPayment($number, $contract, $amount, $paidDate, $paymentCode)
    {
        if (!$contract || !$amount || !$paidDate || !$paymentCode) {
            return null;
        }
        $caller = new BillPayment();
        $caller->setXmlRequest($number, $contract, $amount, $paidDate, $paymentCode);
        $caller->call();
        $caller->setAttr();
        return $caller->getResponse();
    }

    /**
     * Account Info
     * @param String $account гэрээний дугаар
     * @return array Дугаар дээрх хэрэглэгчийн мэдээлэл ирнэ
     */
    public static function getAccountInfo($account)
    {
        if (!$account) {
            return null;
        }
        $caller = new AccountInfo();
        $caller->setXmlRequest($account);
        $caller->call();
        $caller->setAttr();
        return $caller->getResponse();
    }

    /**
     * Phone Info
     * @param String $number утасны дугаар
     * @return array Дугаар дээрх хэрэглэгчийн мэдээлэл ирнэ
     */
    public static function getPostPhoneInfo($number)
    {
        if (!$number) {
            return null;
        }
        $caller = new PostPhoneInfo();
        $caller->setXmlRequest($number);
        $caller->call();
        $caller->setAttr();
        return $caller->getResponse();
    }

    /**
     * Bill Info
     * @param String $number утасны дугаар
     * @param String $contract гэрээний дугаар
     * @return array Төлбөрийн мэдээлэл ирнэ
     */
    public static function getBillInfo($number = 0, $contract = 0)
    {
        if ($number == 0 && $contract == 0) {
            return null;
        }
        $caller = new BillInfo();
        $caller->setXmlRequest($number, $contract);
        $caller->call();
        $caller->setAttr();
        return $caller->getResponse();
    }

}
