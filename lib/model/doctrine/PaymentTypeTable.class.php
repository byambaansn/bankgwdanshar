<?php

/**
 * PaymentTypeTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PaymentTypeTable extends Doctrine_Table
{

    const ACTIVE = 1;
    const AUTO = 1;
    const UO = 3;
    #
    const DEALER = 74;
    const DEALER_SD = 1000;
    const DEALER_AD = 1001;
    #
    CONST PREPAID = 1054;
    CONST AUTO_PREPAID = 1811;
    #
    const PAYMENT_GSM = 2;
    const PAYMENT_HOMENET = 75;
    const PAYMENT_ULUSNET_CHARGE = 1006;
    const PAYMENT_IR = 1041;
    const PAYMENT_NSL = 1043;
    const PAYMENT_MNP = 1042;

    /**
     * Returns an instance of this class.
     *
     * @return object PaymentTypeTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PaymentType');
    }

    /**
     * Нэрээр нь хайгаад байхгүй бол нэмнэ.
     * 
     * @param $name
     * @return $paymentType->id;
     */
    public function getIdByName($name)
    {
        //if($name=='УОО') return self::UO;
        $paymentType = $this->findOneByName($name);
        if (!$paymentType) {
            $paymentType = new PaymentType();
            if ($name == 'Auto')
                $paymentType->setId(self::AUTO);
            $paymentType->setCreatedAt(date('Y-m-d H:i:s'));
            $paymentType->setName($name);
            $paymentType->setStatus(self::ACTIVE);
            $paymentType->save();
        }
        return $paymentType->id;
    }

    /**
     * 
     * @param int $id
     * @param int $status
     * @return BankKhaan
     */
    public static function retrieveByPK($id)
    {
        $q = Doctrine_Query::create()
                ->from('PaymentType')
                ->where('id = ?', $id);

        return $q->fetchOne();
    }

    public static function getSecretIds()
    {
        return array(self::AUTO, self::UO);
    }

    public static function getForSelect()
    {
        $q = Doctrine_Query::create()
                ->from('PaymentType')
                ->where('status = ?', self::ACTIVE);
        $banks = $q->execute();
        $arr = array();
        foreach ($banks as $bank) {
            $arr[$bank['description']][$bank['id']] = $bank['name'];
        }
        return $arr;
    }

    public static function getForSelectJTable()
    {
        $q = Doctrine_Query::create()
                ->from('PaymentType')
                ->where('status = ?', self::ACTIVE);
        $banks = $q->execute();
        $arr = array();
        foreach ($banks as $bank) {
            $arr[] = array('Value' => $bank['id'], 'DisplayText' => $bank['name']);
        }
        return $arr;
    }

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function getForSelectStatus()
    {
        $status = array(
            '1' => self::getStatusName(1),
            '2' => self::getStatusName(2),
        );
        return $status;
    }

    /**
     * Төлөв
     * 
     * @return array
     */
    public static function getStatusName($id)
    {
        $status = array(
            '1' => '<b class="green">Идвэхтэй</b>',
            '2' => '<b class="red">Идвэхгүй</b>',
        );
        return $status[$id];
    }

    public static function getList()
    {
        $q = Doctrine_Query::create()
                ->from('PaymentType');


        return $q->execute();
    }

    /**
     * 
     * Билл циклээр нь Ямар төлөлт болгохыг олох
     * 
     * * */
    public static function getTypeByBillCycle($billCycle)
    {
        $maps = array();
        $maps[0] = 1004;
        $maps[15] = 1053;
        $maps[16] = 1053;
        $maps[20] = 1054;
        $maps[22] = 1055;
        $maps[30] = 1042;
        $maps[31] = 1041;
        $maps[32] = 1047;
        $maps[33] = 1043;
        $maps[34] = 1060;
        $maps[35] = 77;
        $maps[36] = 1043;
        $maps[37] = 1043;
        $maps[38] = 1058;
        $maps[40] = 1061;
        $maps[41] = 1061;
        $maps[42] = 1061;
        $maps[43] = 78;
        $maps[44] = 1056;
        $maps[45] = 79;
        $maps[46] = 1057;
        $maps[47] = 1061;
        $maps[48] = 1059;
        $maps[49] = 1017;
        $maps[50] = 1057;
        $maps[91] = 1053;
        # Бусад
        $paymentType = $maps[0];
        if (in_array($billCycle, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 17, 18, 19, 21, 23, 24, 25, 26, 27, 28, 29, 39, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 83))) {
            $paymentType = self::PAYMENT_GSM;
        } else {
            if (isset($maps[$billCycle])) {
                $paymentType = $maps[$billCycle];
            }
        }
        return $paymentType;
    }

}
