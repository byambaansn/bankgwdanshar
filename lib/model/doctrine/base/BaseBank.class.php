<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Bank', 'transaction');

/**
 * BaseBank
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property Doctrine_Collection $BankAccount
 * @property Doctrine_Collection $Transaction
 * 
 * @method integer             getId()          Returns the current record's "id" value
 * @method string              getName()        Returns the current record's "name" value
 * @method integer             getStatus()      Returns the current record's "status" value
 * @method Doctrine_Collection getBankAccount() Returns the current record's "BankAccount" collection
 * @method Doctrine_Collection getTransaction() Returns the current record's "Transaction" collection
 * @method Bank                setId()          Sets the current record's "id" value
 * @method Bank                setName()        Sets the current record's "name" value
 * @method Bank                setStatus()      Sets the current record's "status" value
 * @method Bank                setBankAccount() Sets the current record's "BankAccount" collection
 * @method Bank                setTransaction() Sets the current record's "Transaction" collection
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBank extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 100,
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('BankAccount', array(
             'local' => 'id',
             'foreign' => 'bank_id'));

        $this->hasMany('Transaction', array(
             'local' => 'id',
             'foreign' => 'bank_id'));
    }
}