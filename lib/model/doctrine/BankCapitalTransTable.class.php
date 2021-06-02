<?php

/**
 * BankCapitalTransTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class BankCapitalTransTable extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return object BankCapitalTransTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('BankCapitalTrans');
    }

    /**
     * 
     * @param int $orderId
     * @return BankCapital
     */
    public static function findOneByOrderId($orderId, $status = 0)
    {
        $q = Doctrine_Query::create()
                ->from('BankCapitalTrans')
                ->where('transition = ?', $orderId);
        if ($status) {
            $q->addWhere('status=?', $status);
        }
        return $q->fetchOne();
    }

    /**
     *
     *
     * @return object BankSavingsTransTable
     */
    public static function insert($id, $transIds)
    {
        $transition = 'T' . join('T', $transIds);

        $trans = new BankCapitalTrans();
        $trans->setTransition($transition);
        $trans->save();
        return $transition;
    }

    /**
     *
     *
     * @return object BankSavingsTransTable
     */
    public static function failedTransaction($txns)
    {
        $transitions = explode('T', $txns);
        foreach ($transitions as $trans) {
            $object = self::findOneByOrderId($trans);
            if ($object) {
                $trans->setStatus(0);
                $trans->save();
            }
        }
    }

    /**
     *
     * getTransiction
     * @return object BankSavingsTransTable
     */
    public static function getTransiction()
    {
        $transitions = Doctrine_Query::create()
                ->from('BankCapitalTrans')
                ->where('status = ?', 0)
                ->execute();
        $transition = '';
        foreach ($transitions as $trans) {
            $transition.=$trans['transition'];
            $trans->setStatus(1);
            $trans->save();
        }

        return $transition;
    }

}
