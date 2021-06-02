<?php

/**
 * Bankpayment form base class.
 *
 * @method Bankpayment getObject() Returns the current form's model object
 *
 * @package    BANKGW
 * @subpackage form
 * @author     Belbayar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'parent_id'         => new sfWidgetFormInputText(),
      'child_num'         => new sfWidgetFormInputText(),
      'vendor_id'         => new sfWidgetFormInputText(),
      'bank_order_id'     => new sfWidgetFormInputText(),
      'type'              => new sfWidgetFormInputText(),
      'bank_payment_code' => new sfWidgetFormInputText(),
      'number'            => new sfWidgetFormInputText(),
      'contract_number'   => new sfWidgetFormInputText(),
      'contract_name'     => new sfWidgetFormInputText(),
      'bill_cycle'        => new sfWidgetFormInputText(),
      'paid_amount'       => new sfWidgetFormInputText(),
      'contract_amount'   => new sfWidgetFormInputText(),
      'credit_control'    => new sfWidgetFormInputText(),
      'insurance_date'    => new sfWidgetFormDate(),
      'insurance_amount'  => new sfWidgetFormInputText(),
      'username'          => new sfWidgetFormInputText(),
      'status'            => new sfWidgetFormInputText(),
      'status_comment'    => new sfWidgetFormInputText(),
      'try_count'         => new sfWidgetFormInputText(),
      'updated_user_id'   => new sfWidgetFormInputText(),
      'updated_at'        => new sfWidgetFormDateTime(),
      'created_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parent_id'         => new sfValidatorInteger(),
      'child_num'         => new sfValidatorInteger(),
      'vendor_id'         => new sfValidatorInteger(),
      'bank_order_id'     => new sfValidatorInteger(),
      'type'              => new sfValidatorInteger(),
      'bank_payment_code' => new sfValidatorString(array('max_length' => 20)),
      'number'            => new sfValidatorString(array('max_length' => 50)),
      'contract_number'   => new sfValidatorString(array('max_length' => 20)),
      'contract_name'     => new sfValidatorString(array('max_length' => 255)),
      'bill_cycle'        => new sfValidatorInteger(),
      'paid_amount'       => new sfValidatorNumber(),
      'contract_amount'   => new sfValidatorNumber(),
      'credit_control'    => new sfValidatorNumber(),
      'insurance_date'    => new sfValidatorDate(),
      'insurance_amount'  => new sfValidatorNumber(),
      'username'          => new sfValidatorString(array('max_length' => 20)),
      'status'            => new sfValidatorInteger(),
      'status_comment'    => new sfValidatorString(array('max_length' => 50)),
      'try_count'         => new sfValidatorInteger(array('required' => false)),
      'updated_user_id'   => new sfValidatorInteger(),
      'updated_at'        => new sfValidatorDateTime(),
      'created_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('bankpayment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Bankpayment';
  }

}
