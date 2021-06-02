<?php

/**
 * BankpaymentVatRefund form base class.
 *
 * @method BankpaymentVatRefund getObject() Returns the current form's model object
 *
 * @package    BANKGW
 * @subpackage form
 * @author     Belbayar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentVatRefundForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'bankpayment_id' => new sfWidgetFormInputText(),
      'refund_type'    => new sfWidgetFormInputText(),
      'old_value'      => new sfWidgetFormInputText(),
      'new_value'      => new sfWidgetFormInputText(),
      'refund_date'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'bankpayment_id' => new sfValidatorInteger(),
      'refund_type'    => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'old_value'      => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'new_value'      => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'refund_date'    => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_vat_refund[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentVatRefund';
  }

}
