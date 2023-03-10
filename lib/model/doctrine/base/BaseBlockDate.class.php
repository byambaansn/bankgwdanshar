<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BlockDate', 'doctrine');

/**
 * BaseBlockDate
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $block_type
 * @property date $block_date
 * @property date $is_active
 * @property integer $created_user_id
 * @property timestamp $created_at
 * 
 * @method integer   getId()              Returns the current record's "id" value
 * @method string    getBlockType()       Returns the current record's "block_type" value
 * @method date      getBlockDate()       Returns the current record's "block_date" value
 * @method date      getIsActive()        Returns the current record's "is_active" value
 * @method integer   getCreatedUserId()   Returns the current record's "created_user_id" value
 * @method timestamp getCreatedAt()       Returns the current record's "created_at" value
 * @method BlockDate setId()              Sets the current record's "id" value
 * @method BlockDate setBlockType()       Sets the current record's "block_type" value
 * @method BlockDate setBlockDate()       Sets the current record's "block_date" value
 * @method BlockDate setIsActive()        Sets the current record's "is_active" value
 * @method BlockDate setCreatedUserId()   Sets the current record's "created_user_id" value
 * @method BlockDate setCreatedAt()       Sets the current record's "created_at" value
 * 
 * @package    BANKGW
 * @subpackage model
 * @author     Belbayar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBlockDate extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('block_date');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('block_type', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 20,
             ));
        $this->hasColumn('block_date', 'date', 25, array(
             'type' => 'date',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 25,
             ));
        $this->hasColumn('is_active', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 4,
             ));
        $this->hasColumn('created_user_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => 4,
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
        
    }
}