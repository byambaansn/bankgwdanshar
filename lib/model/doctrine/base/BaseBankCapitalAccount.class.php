<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankCapitalAccount', 'doctrine');

/**
 * BaseBankCapitalAccount
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $account
 * @property integer $is_active
 * @property timestamp $created_at
 * 
 * @method integer            getId()         Returns the current record's "id" value
 * @method string             getName()       Returns the current record's "name" value
 * @method string             getAccount()    Returns the current record's "account" value
 * @method integer            getIsActive()   Returns the current record's "is_active" value
 * @method timestamp          getCreatedAt()  Returns the current record's "created_at" value
 * @method BankCapitalAccount setId()         Sets the current record's "id" value
 * @method BankCapitalAccount setName()       Sets the current record's "name" value
 * @method BankCapitalAccount setAccount()    Sets the current record's "account" value
 * @method BankCapitalAccount setIsActive()   Sets the current record's "is_active" value
 * @method BankCapitalAccount setCreatedAt()  Sets the current record's "created_at" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankCapitalAccount extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_capital_account');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 255,
             ));
        $this->hasColumn('account', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
             ));
        $this->hasColumn('is_active', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
             ));
        $this->hasColumn('created_at', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 25,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}