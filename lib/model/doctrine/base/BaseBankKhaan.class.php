<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankKhaan', 'doctrine');

/**
 * BaseBankKhaan
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $vendor_id
 * @property string $charge_mobile
 * @property integer $charge_amount
 * @property integer $percent
 * @property string $bank_account
 * @property string $related_account
 * @property string $order_id
 * @property date $order_id_date
 * @property integer $sales_order_id
 * @property string $order_p
 * @property string $order_mobile
 * @property timestamp $order_date
 * @property string $order_type
 * @property float $order_amount
 * @property string $order_s
 * @property integer $status
 * @property integer $transfer_sap
 * @property integer $try_count
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * 
 * @method integer   getId()             Returns the current record's "id" value
 * @method integer   getVendorId()       Returns the current record's "vendor_id" value
 * @method string    getChargeMobile()   Returns the current record's "charge_mobile" value
 * @method integer   getChargeAmount()   Returns the current record's "charge_amount" value
 * @method integer   getPercent()        Returns the current record's "percent" value
 * @method string    getBankAccount()    Returns the current record's "bank_account" value
 * @method string    getRelatedAccount()    Returns the current record's "related_account" value
 * @method string    getOrderId()        Returns the current record's "order_id" value
 * @method date      getOrderIdDate()    Returns the current record's "order_id_date" value
 * @method integer   getSalesOrderId()   Returns the current record's "sales_order_id" value
 * @method string    getOrderP()         Returns the current record's "order_p" value
 * @method string    getOrderMobile()    Returns the current record's "order_mobile" value
 * @method timestamp getOrderDate()      Returns the current record's "order_date" value
 * @method string    getOrderType()      Returns the current record's "order_type" value
 * @method float     getOrderAmount()    Returns the current record's "order_amount" value
 * @method string    getOrderS()         Returns the current record's "order_s" value
 * @method integer   getStatus()         Returns the current record's "status" value
 * @method integer   getTransferSap()    Returns the current record's "transfer_sap" value
 * @method integer   getTryCount()       Returns the current record's "try_count" value
 * @method timestamp getCreatedAt()      Returns the current record's "created_at" value
 * @method timestamp getUpdatedAt()      Returns the current record's "updated_at" value
 * @method BankKhaan setId()             Sets the current record's "id" value
 * @method BankKhaan setVendorId()       Sets the current record's "vendor_id" value
 * @method BankKhaan setChargeMobile()   Sets the current record's "charge_mobile" value
 * @method BankKhaan setChargeAmount()   Sets the current record's "charge_amount" value
 * @method BankKhaan setPercent()        Sets the current record's "percent" value
 * @method BankKhaan setBankAccount()    Sets the current record's "bank_account" value
 * @method BankKhaan setRelatedAccount()    Sets the current record's "related_account" value
 * @method BankKhaan setOrderId()        Sets the current record's "order_id" value
 * @method BankKhaan setOrderIdDate()    Sets the current record's "order_id_date" value
 * @method BankKhaan setSalesOrderId()   Sets the current record's "sales_order_id" value
 * @method BankKhaan setOrderP()         Sets the current record's "order_p" value
 * @method BankKhaan setOrderMobile()    Sets the current record's "order_mobile" value
 * @method BankKhaan setOrderDate()      Sets the current record's "order_date" value
 * @method BankKhaan setOrderType()      Sets the current record's "order_type" value
 * @method BankKhaan setOrderAmount()    Sets the current record's "order_amount" value
 * @method BankKhaan setOrderS()         Sets the current record's "order_s" value
 * @method BankKhaan setStatus()         Sets the current record's "status" value
 * @method BankKhaan setTransferSap()    Sets the current record's "transfer_sap" value
 * @method BankKhaan setTryCount()       Sets the current record's "try_count" value
 * @method BankKhaan setCreatedAt()      Sets the current record's "created_at" value
 * @method BankKhaan setUpdatedAt()      Sets the current record's "updated_at" value
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankKhaan extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_khaan');
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
        $this->hasColumn('related_account', 'string', 50, array(
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
        $this->hasColumn('order_id_date', 'date', 25, array(
             'type' => 'date',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 25,
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
        $this->hasColumn('order_s', 'string', 150, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 150,
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
             'default' => '0',
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
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}