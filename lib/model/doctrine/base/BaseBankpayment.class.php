<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Bankpayment', 'doctrine');

/**
 * BaseBankpayment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $parent_id
 * @property integer $child_num
 * @property integer $vendor_id
 * @property integer $bank_order_id
 * @property integer $type
 * @property string $bank_payment_code
 * @property string $number
 * @property string $contract_number
 * @property string $contract_name
 * @property integer $bill_cycle
 * @property float $paid_amount
 * @property float $contract_amount
 * @property float $credit_control
 * @property date $insurance_date
 * @property float $insurance_amount
 * @property string $username
 * @property integer $status
 * @property string $status_comment
 * @property integer $try_count
 * @property integer $updated_user_id
 * @property timestamp $updated_at
 * @property timestamp $created_at
 * 
 * @method integer     getId()                Returns the current record's "id" value
 * @method integer     getParentId()          Returns the current record's "parent_id" value
 * @method integer     getChildNum()          Returns the current record's "child_num" value
 * @method integer     getVendorId()          Returns the current record's "vendor_id" value
 * @method integer     getBankOrderId()       Returns the current record's "bank_order_id" value
 * @method integer     getType()              Returns the current record's "type" value
 * @method string      getBankPaymentCode()   Returns the current record's "bank_payment_code" value
 * @method string      getNumber()            Returns the current record's "number" value
 * @method string      getContractNumber()    Returns the current record's "contract_number" value
 * @method string      getContractName()      Returns the current record's "contract_name" value
 * @method integer     getBillCycle()         Returns the current record's "bill_cycle" value
 * @method float       getPaidAmount()        Returns the current record's "paid_amount" value
 * @method float       getContractAmount()    Returns the current record's "contract_amount" value
 * @method float       getCreditControl()     Returns the current record's "credit_control" value
 * @method date        getInsuranceDate()     Returns the current record's "insurance_date" value
 * @method float       getInsuranceAmount()   Returns the current record's "insurance_amount" value
 * @method string      getUsername()          Returns the current record's "username" value
 * @method integer     getStatus()            Returns the current record's "status" value
 * @method string      getStatusComment()     Returns the current record's "status_comment" value
 * @method integer     getTryCount()          Returns the current record's "try_count" value
 * @method integer     getUpdatedUserId()     Returns the current record's "updated_user_id" value
 * @method timestamp   getUpdatedAt()         Returns the current record's "updated_at" value
 * @method timestamp   getCreatedAt()         Returns the current record's "created_at" value
 * @method Bankpayment setId()                Sets the current record's "id" value
 * @method Bankpayment setParentId()          Sets the current record's "parent_id" value
 * @method Bankpayment setChildNum()          Sets the current record's "child_num" value
 * @method Bankpayment setVendorId()          Sets the current record's "vendor_id" value
 * @method Bankpayment setBankOrderId()       Sets the current record's "bank_order_id" value
 * @method Bankpayment setType()              Sets the current record's "type" value
 * @method Bankpayment setBankPaymentCode()   Sets the current record's "bank_payment_code" value
 * @method Bankpayment setNumber()            Sets the current record's "number" value
 * @method Bankpayment setContractNumber()    Sets the current record's "contract_number" value
 * @method Bankpayment setContractName()      Sets the current record's "contract_name" value
 * @method Bankpayment setBillCycle()         Sets the current record's "bill_cycle" value
 * @method Bankpayment setPaidAmount()        Sets the current record's "paid_amount" value
 * @method Bankpayment setContractAmount()    Sets the current record's "contract_amount" value
 * @method Bankpayment setCreditControl()     Sets the current record's "credit_control" value
 * @method Bankpayment setInsuranceDate()     Sets the current record's "insurance_date" value
 * @method Bankpayment setInsuranceAmount()   Sets the current record's "insurance_amount" value
 * @method Bankpayment setUsername()          Sets the current record's "username" value
 * @method Bankpayment setStatus()            Sets the current record's "status" value
 * @method Bankpayment setStatusComment()     Sets the current record's "status_comment" value
 * @method Bankpayment setTryCount()          Sets the current record's "try_count" value
 * @method Bankpayment setUpdatedUserId()     Sets the current record's "updated_user_id" value
 * @method Bankpayment setUpdatedAt()         Sets the current record's "updated_at" value
 * @method Bankpayment setCreatedAt()         Sets the current record's "created_at" value
 * 
 * @package    BANKGW
 * @subpackage model
 * @author     Belbayar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankpayment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bankpayment');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 8,
             ));
        $this->hasColumn('parent_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('child_num', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
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
        $this->hasColumn('bank_order_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('type', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 1,
             ));
        $this->hasColumn('bank_payment_code', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 20,
             ));
        $this->hasColumn('number', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
             ));
        $this->hasColumn('contract_number', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 20,
             ));
        $this->hasColumn('contract_name', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 255,
             ));
        $this->hasColumn('bill_cycle', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('paid_amount', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('contract_amount', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('credit_control', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('insurance_date', 'date', 25, array(
             'type' => 'date',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 25,
             ));
        $this->hasColumn('insurance_amount', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('username', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 20,
             ));
        $this->hasColumn('status', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('status_comment', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
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
        $this->hasColumn('updated_user_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
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