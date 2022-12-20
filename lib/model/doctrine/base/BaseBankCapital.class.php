<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankCapital', 'doctrine');

/**
 * BaseBankCapital
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $vendor_id
 * @property string $charge_mobile
 * @property integer $charge_amount
 * @property integer $percent
 * @property string $bank_account
 * @property string $order_id
 * @property integer $sales_order_id
 * @property string $order_p
 * @property string $order_mobile
 * @property timestamp $order_date
 * @property string $order_type
 * @property float $order_amount
 * @property string $order_channel
 * @property string $order_s
 * @property integer $order_sequence_no
 * @property integer $status
 * @property integer $transfer_sap
 * @property integer $try_count
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property string $related_account
 * 
 * @method integer     getId()                Returns the current record's "id" value
 * @method integer     getVendorId()          Returns the current record's "vendor_id" value
 * @method string      getChargeMobile()      Returns the current record's "charge_mobile" value
 * @method integer     getChargeAmount()      Returns the current record's "charge_amount" value
 * @method integer     getPercent()           Returns the current record's "percent" value
 * @method string      getBankAccount()       Returns the current record's "bank_account" value
 * @method string      getOrderId()           Returns the current record's "order_id" value
 * @method integer     getSalesOrderId()      Returns the current record's "sales_order_id" value
 * @method string      getOrderP()            Returns the current record's "order_p" value
 * @method string      getOrderMobile()       Returns the current record's "order_mobile" value
 * @method timestamp   getOrderDate()         Returns the current record's "order_date" value
 * @method string      getOrderType()         Returns the current record's "order_type" value
 * @method float       getOrderAmount()       Returns the current record's "order_amount" value
 * @method string      getOrderChannel()      Returns the current record's "order_channel" value
 * @method string      getOrderS()            Returns the current record's "order_s" value
 * @method integer     getOrderSequenceNo()   Returns the current record's "order_sequence_no" value
 * @method integer     getStatus()            Returns the current record's "status" value
 * @method integer     getTransferSap()       Returns the current record's "transfer_sap" value
 * @method integer     getTryCount()          Returns the current record's "try_count" value
 * @method timestamp   getCreatedAt()         Returns the current record's "created_at" value
 * @method timestamp   getUpdatedAt()         Returns the current record's "updated_at" value
 * @method string      getRelatedAccount()    Returns the current record's "related_account" value
 * @method BankCapital setId()                Sets the current record's "id" value
 * @method BankCapital setVendorId()          Sets the current record's "vendor_id" value
 * @method BankCapital setChargeMobile()      Sets the current record's "charge_mobile" value
 * @method BankCapital setChargeAmount()      Sets the current record's "charge_amount" value
 * @method BankCapital setPercent()           Sets the current record's "percent" value
 * @method BankCapital setBankAccount()       Sets the current record's "bank_account" value
 * @method BankCapital setOrderId()           Sets the current record's "order_id" value
 * @method BankCapital setSalesOrderId()      Sets the current record's "sales_order_id" value
 * @method BankCapital setOrderP()            Sets the current record's "order_p" value
 * @method BankCapital setOrderMobile()       Sets the current record's "order_mobile" value
 * @method BankCapital setOrderDate()         Sets the current record's "order_date" value
 * @method BankCapital setOrderType()         Sets the current record's "order_type" value
 * @method BankCapital setOrderAmount()       Sets the current record's "order_amount" value
 * @method BankCapital setOrderChannel()      Sets the current record's "order_channel" value
 * @method BankCapital setOrderS()            Sets the current record's "order_s" value
 * @method BankCapital setOrderSequenceNo()   Sets the current record's "order_sequence_no" value
 * @method BankCapital setStatus()            Sets the current record's "status" value
 * @method BankCapital setTransferSap()       Sets the current record's "transfer_sap" value
 * @method BankCapital setTryCount()          Sets the current record's "try_count" value
 * @method BankCapital setCreatedAt()         Sets the current record's "created_at" value
 * @method BankCapital setUpdatedAt()         Sets the current record's "updated_at" value
 * @method BankCapital setRelatedAccount()    Sets the current record's "related_account" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankCapital extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_capital');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('vendor_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
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
        $this->hasColumn('charge_amount', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('percent', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
             ));
        $this->hasColumn('bank_account', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
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
        $this->hasColumn('sales_order_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('order_p', 'string', 500, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 500,
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
        $this->hasColumn('order_date', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 25,
             ));
        $this->hasColumn('order_type', 'string', 10, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 10,
             ));
        $this->hasColumn('order_amount', 'float', 12, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 12,
             ));
        $this->hasColumn('order_channel', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
             ));
        $this->hasColumn('order_s', 'string', 150, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 150,
             ));
        $this->hasColumn('order_sequence_no', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 8,
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
        $this->hasColumn('transfer_sap', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 1,
             ));
        $this->hasColumn('try_count', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
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
        $this->hasColumn('updated_at', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 25,
             ));
        $this->hasColumn('related_account', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}