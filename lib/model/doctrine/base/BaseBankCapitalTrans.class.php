<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankCapitalTrans', 'doctrine');

/**
 * BaseBankCapitalTrans
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $transition
 * @property integer $status
 * 
 * @method integer          getId()         Returns the current record's "id" value
 * @method string           getTransition() Returns the current record's "transition" value
 * @method integer          getStatus()     Returns the current record's "status" value
 * @method BankCapitalTrans setId()         Sets the current record's "id" value
 * @method BankCapitalTrans setTransition() Sets the current record's "transition" value
 * @method BankCapitalTrans setStatus()     Sets the current record's "status" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankCapitalTrans extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_capital_trans');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('transition', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}