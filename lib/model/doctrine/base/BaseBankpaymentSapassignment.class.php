<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('BankpaymentSapassignment', 'doctrine');

/**
 * BaseBankpaymentSapassignment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $billcyclecode
 * @property string $assignment
 * 
 * @method string                   getBillcyclecode() Returns the current record's "billcyclecode" value
 * @method string                   getAssignment()    Returns the current record's "assignment" value
 * @method BankpaymentSapassignment setBillcyclecode() Sets the current record's "billcyclecode" value
 * @method BankpaymentSapassignment setAssignment()    Sets the current record's "assignment" value
 * 
 * @package    BANKGW
 * @subpackage model
 * @author     Belbayar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBankpaymentSapassignment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('bankpayment_sapassignment');
        $this->hasColumn('billcyclecode', 'string', 45, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             'length' => 45,
             ));
        $this->hasColumn('assignment', 'string', 16, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => 16,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}