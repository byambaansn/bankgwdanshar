<?php

/**
 * BankpaymentVat form base class.
 *
 * @method BankpaymentVat getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentVatForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'bankpayment_id'   => new sfWidgetFormInputHidden(),
      'status'           => new sfWidgetFormInputText(),
      'type'             => new sfWidgetFormInputText(),
      'outcome_order_id' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'bankpayment_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('bankpayment_id')), 'empty_value' => $this->getObject()->get('bankpayment_id'), 'required' => false)),
      'status'           => new sfValidatorInteger(),
      'type'             => new sfValidatorInteger(),
      'outcome_order_id' => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_vat[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentVat';
  }

}
