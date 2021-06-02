<?php

/**
 * BankSavingsAccount form base class.
 *
 * @method BankSavingsAccount getObject() Returns the current form's model object
 *
 * @package    BANKGW
 * @subpackage form
 * @author     Belbayar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankSavingsAccountForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'name'       => new sfWidgetFormInputText(),
      'account'    => new sfWidgetFormInputText(),
      'pin'        => new sfWidgetFormInputText(),
      'start_date' => new sfWidgetFormDate(),
      'end_date'   => new sfWidgetFormDate(),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'       => new sfValidatorString(array('max_length' => 255)),
      'account'    => new sfValidatorString(array('max_length' => 50)),
      'pin'        => new sfValidatorString(array('max_length' => 100)),
      'start_date' => new sfValidatorDate(),
      'end_date'   => new sfValidatorDate(),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('bank_savings_account[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankSavingsAccount';
  }

}
