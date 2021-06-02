<?php

/**
 * CompanyTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CompanyTable extends Doctrine_Table
{

    const ACTIVE = 1;

    /**
     * Returns an instance of this class.
     *
     * @return object CompanyTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Company');
    }

    public static function getForSelect()
    {
        $q = Doctrine_Query::create()
                ->from('Company')
                ->where('status = ?', self::ACTIVE);
        $rows = $q->execute();
        $arr = array();
        foreach ($rows as $row) {
            $arr[$row['id']] = $row['name'];
        }
        return $arr;
    }

}
