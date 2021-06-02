<?php

/**
 * BlockTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class BlockTable extends Doctrine_Table
{

    const BLOCK = 0;
    const UNBLOCK = 0;

    /**
     * Returns an instance of this class.
     *
     * @return object BlockTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Block');
    }

    /**
     * Returns an instance of this class.
     *
     * @return object BlockTable
     */
    public static function retrieveByBank($id)
    {
        $q = Doctrine_Query::create()
                ->from('Block')
                ->where('id = ?', $id);

        return $q->fetchOne();
    }

}
