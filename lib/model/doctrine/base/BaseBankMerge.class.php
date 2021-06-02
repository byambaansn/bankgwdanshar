<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankMerge', 'doctrine');

/**
 * BaseBankMerge
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $type
 * @property string $created_user
 * @property timestamp $created_at
 * @property Doctrine_Collection $BankMergeOrder
 * 
 * @method integer             getId()             Returns the current record's "id" value
 * @method integer             getType()           Returns the current record's "type" value
 * @method string              getCreatedUser()    Returns the current record's "created_user" value
 * @method timestamp           getCreatedAt()      Returns the current record's "created_at" value
 * @method Doctrine_Collection getBankMergeOrder() Returns the current record's "BankMergeOrder" collection
 * @method BankMerge           setId()             Sets the current record's "id" value
 * @method BankMerge           setType()           Sets the current record's "type" value
 * @method BankMerge           setCreatedUser()    Sets the current record's "created_user" value
 * @method BankMerge           setCreatedAt()      Sets the current record's "created_at" value
 * @method BankMerge           setBankMergeOrder() Sets the current record's "BankMergeOrder" collection
 * 
 * @package    BANKGW
 * @subpackage model
 * @author     Belbayar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankMerge extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bank_merge');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('type', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('created_user', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 255,
             ));
        $this->hasColumn('created_at', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 25,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('BankMergeOrder', array(
             'local' => 'id',
             'foreign' => 'merge_id'));
    }
}