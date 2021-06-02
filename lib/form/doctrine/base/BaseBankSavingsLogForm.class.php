<?php

/**
 * BankSavingsLog form base class.
 *
 * @method BankSavingsLog getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankSavingsLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'action'        => new sfWidgetFormInputText(),
      'order_id'      => new sfWidgetFormInputText(),
      'charge_mobile' => new sfWidgetFormInputText(),
      'order_mobile'  => new sfWidgetFormInputText(),
      'status'        => new sfWidgetFormInputText(),
      'db_user'       => new sfWidgetFormInputText(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'action'        => new sfValidatorString(array('max_length' => 20)),
      'order_id'      => new sfValidatorString(array('max_length' => 30)),
      'charge_mobile' => new sfValidatorString(array('max_length' => 12)),
      'order_mobile'  => new sfValidatorString(array('max_length' => 12)),
      'status'        => new sfValidatorInteger(array('required' => false)),
      'db_user'       => new sfValidatorString(array('max_length' => 30)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('bank_savings_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankSavingsLog';
  }

}
