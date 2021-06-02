<?php

/**
 * LogPayment form base class.
 *
 * @method LogPayment getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogPaymentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'payment_id'      => new sfWidgetFormInputText(),
      'action'          => new sfWidgetFormInputText(),
      'type_id'         => new sfWidgetFormInputText(),
      'amount'          => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormInputText(),
      'created_user_id' => new sfWidgetFormInputText(),
      'username'        => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'db_user'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'payment_id'      => new sfValidatorInteger(),
      'action'          => new sfValidatorString(array('max_length' => 50)),
      'type_id'         => new sfValidatorInteger(),
      'amount'          => new sfValidatorNumber(),
      'description'     => new sfValidatorInteger(),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'created_user_id' => new sfValidatorInteger(),
      'username'        => new sfValidatorString(array('max_length' => 50)),
      'created_at'      => new sfValidatorDateTime(),
      'db_user'         => new sfValidatorString(array('max_length' => 20)),
    ));

    $this->widgetSchema->setNameFormat('log_payment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogPayment';
  }

}
