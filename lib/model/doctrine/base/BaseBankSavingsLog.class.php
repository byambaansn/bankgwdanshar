<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankSavingsLog', 'doctrine');

/**
 * BaseBankSavingsLog
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $action
 * @property string $order_id
 * @property string $charge_mobile
 * @property string $order_mobile
 * @property integer $status
 * @property string $db_user
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * 
 * @method integer        getId()            Returns the current record's "id" value
 * @method string         getAction()        Returns the current record's "action" value
 * @method string         getOrderId()       Returns the current record's "order_id" value
 * @method string         getChargeMobile()  Returns the current record's "charge_mobile" value
 * @method string         getOrderMobile()   Returns the current record's "order_mobile" value
 * @method integer        getStatus()        Returns the current record's "status" value
 * @method string         getDbUser()        Returns the current record's "db_user" value
 * @method timestamp      getCreatedAt()     Returns the current record's "created_at" value
 * @method timestamp      getUpdatedAt()     Returns the current record's "updated_at" value
 * @method BankSavingsLog setId()            Sets the current record's "id" value
 * @method BankSavingsLog setAction()        Sets the current record's "action" value
 * @method BankSavingsLog setOrderId()       Sets the current record's "order_id" value
 * @method BankSavingsLog setChargeMobile()  Sets the current record's "charge_mobile" value
 * @method BankSavingsLog setOrderMobile()   Sets the current record's "order_mobile" value
 * @method BankSavingsLog setStatus()        Sets the current record's "status" value
 * @method BankSavingsLog setDbUser()        Sets the current record's "db_user" value
 * @method BankSavingsLog setCreatedAt()     Sets the current record's "created_at" value
 * @method BankSavingsLog setUpdatedAt()     Sets the current record's "updated_at" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankSavingsLog extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_savings_log');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('action', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 20,
             ));
        $this->hasColumn('order_id', 'string', 30, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 30,
             ));
        $this->hasColumn('charge_mobile', 'string', 12, array(
             'type' => 'string',
             'fixed' => 1,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 12,
             ));
        $this->hasColumn('order_mobile', 'string', 12, array(
             'type' => 'string',
             'fixed' => 1,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 12,
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'default' => '1',
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
             ));
        $this->hasColumn('db_user', 'string', 30, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 30,
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
        $this->hasColumn('updated_at', 'timestamp', 25, array(
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