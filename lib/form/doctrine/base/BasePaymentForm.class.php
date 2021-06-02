<?php

/**
 * Payment form base class.
 *
 * @method Payment getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePaymentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentType'), 'add_empty' => false)),
      'amount'          => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormInputText(),
      'created_user_id' => new sfWidgetFormInputText(),
      'username'        => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentType'))),
      'amount'          => new sfValidatorNumber(),
      'description'     => new sfValidatorString(array('max_length' => 255)),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'created_user_id' => new sfValidatorInteger(),
      'username'        => new sfValidatorString(array('max_length' => 50)),
      'created_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('payment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Payment';
  }

}
