<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Payment', 'transaction');

/**
 * BasePayment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $type_id
 * @property string $assignment
 * @property float $amount
 * @property string $description
 * @property integer $status
 * @property integer $created_user_id
 * @property string $username
 * @property timestamp $created_at
 * @property PaymentType $PaymentType
 * @property Doctrine_Collection $TransactionPayment
 * @property Doctrine_Collection $TransactionPayment_4
 * @property Doctrine_Collection $TransactionPayment_6
 * @property Doctrine_Collection $TransactionPayment_8
 * @property Doctrine_Collection $TransactionPayment_9
 * @property Doctrine_Collection $TransactionPayment_11
 * @property Doctrine_Collection $TransactionPayment_13
 * @property Doctrine_Collection $TransactionPayment_15
 * 
 * @method integer             getId()                    Returns the current record's "id" value
 * @method integer             getTypeId()                Returns the current record's "type_id" value
 * @method string              getAssignment()            Returns the current record's "assignment" value
 * @method float               getAmount()                Returns the current record's "amount" value
 * @method string              getDescription()           Returns the current record's "description" value
 * @method integer             getStatus()                Returns the current record's "status" value
 * @method integer             getCreatedUserId()         Returns the current record's "created_user_id" value
 * @method string              getUsername()              Returns the current record's "username" value
 * @method timestamp           getCreatedAt()             Returns the current record's "created_at" value
 * @method PaymentType         getPaymentType()           Returns the current record's "PaymentType" value
 * @method Doctrine_Collection getTransactionPayment()    Returns the current record's "TransactionPayment" collection
 * @method Doctrine_Collection getTransactionPayment4()   Returns the current record's "TransactionPayment_4" collection
 * @method Doctrine_Collection getTransactionPayment6()   Returns the current record's "TransactionPayment_6" collection
 * @method Doctrine_Collection getTransactionPayment8()   Returns the current record's "TransactionPayment_8" collection
 * @method Doctrine_Collection getTransactionPayment9()   Returns the current record's "TransactionPayment_9" collection
 * @method Doctrine_Collection getTransactionPayment11()  Returns the current record's "TransactionPayment_11" collection
 * @method Doctrine_Collection getTransactionPayment13()  Returns the current record's "TransactionPayment_13" collection
 * @method Doctrine_Collection getTransactionPayment15()  Returns the current record's "TransactionPayment_15" collection
 * @method Payment             setId()                    Sets the current record's "id" value
 * @method Payment             setTypeId()                Sets the current record's "type_id" value
 * @method Payment             setAssignment()            Sets the current record's "assignment" value
 * @method Payment             setAmount()                Sets the current record's "amount" value
 * @method Payment             setDescription()           Sets the current record's "description" value
 * @method Payment             setStatus()                Sets the current record's "status" value
 * @method Payment             setCreatedUserId()         Sets the current record's "created_user_id" value
 * @method Payment             setUsername()              Sets the current record's "username" value
 * @method Payment             setCreatedAt()             Sets the current record's "created_at" value
 * @method Payment             setPaymentType()           Sets the current record's "PaymentType" value
 * @method Payment             setTransactionPayment()    Sets the current record's "TransactionPayment" collection
 * @method Payment             setTransactionPayment4()   Sets the current record's "TransactionPayment_4" collection
 * @method Payment             setTransactionPayment6()   Sets the current record's "TransactionPayment_6" collection
 * @method Payment             setTransactionPayment8()   Sets the current record's "TransactionPayment_8" collection
 * @method Payment             setTransactionPayment9()   Sets the current record's "TransactionPayment_9" collection
 * @method Payment             setTransactionPayment11()  Sets the current record's "TransactionPayment_11" collection
 * @method Payment             setTransactionPayment13()  Sets the current record's "TransactionPayment_13" collection
 * @method Payment             setTransactionPayment15()  Sets the current record's "TransactionPayment_15" collection
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePayment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('payment');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 8,
             ));
        $this->hasColumn('type_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('assignment', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
             ));
        $this->hasColumn('amount', 'float', null, array(
             'type' => 'float',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '',
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 255,
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
        $this->hasColumn('created_user_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('username', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 50,
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
        $this->hasOne('PaymentType', array(
             'local' => 'type_id',
             'foreign' => 'id'));

        $this->hasMany('TransactionPayment', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_4', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_6', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_8', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_9', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_11', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_13', array(
             'local' => 'id',
             'foreign' => 'payment_id'));

        $this->hasMany('TransactionPayment as TransactionPayment_15', array(
             'local' => 'id',
             'foreign' => 'payment_id'));
    }
}