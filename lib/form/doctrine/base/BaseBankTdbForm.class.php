<?php

/**
 * BankTdb form base class.
 *
 * @method BankTdb getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankTdbForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'vendor_id'      => new sfWidgetFormInputText(),
      'charge_mobile'  => new sfWidgetFormInputText(),
      'charge_amount'  => new sfWidgetFormInputText(),
      'percent'        => new sfWidgetFormInputText(),
      'bank_account'   => new sfWidgetFormInputText(),
      'order_id'       => new sfWidgetFormInputText(),
      'order_id_sub'   => new sfWidgetFormInputText(),
      'sales_order_id' => new sfWidgetFormInputText(),
      'order_p'        => new sfWidgetFormTextarea(),
      'order_mobile'   => new sfWidgetFormInputText(),
      'order_date'     => new sfWidgetFormDateTime(),
      'order_type'     => new sfWidgetFormInputText(),
      'order_amount'   => new sfWidgetFormInputText(),
      'order_channel'  => new sfWidgetFormInputText(),
      'order_teller'   => new sfWidgetFormInputText(),
      'order_s'        => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'transfer_sap'   => new sfWidgetFormInputText(),
      'try_count'      => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'related_account'=> new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'vendor_id'      => new sfValidatorInteger(),
      'charge_mobile'  => new sfValidatorString(array('max_length' => 12)),
      'charge_amount'  => new sfValidatorInteger(),
      'percent'        => new sfValidatorInteger(),
      'bank_account'   => new sfValidatorString(array('max_length' => 50)),
      'order_id'       => new sfValidatorInteger(),
      'order_id_sub'   => new sfValidatorInteger(),
      'sales_order_id' => new sfValidatorInteger(array('required' => false)),
      'order_p'        => new sfValidatorString(array('max_length' => 500)),
      'order_mobile'   => new sfValidatorString(array('max_length' => 12)),
      'order_date'     => new sfValidatorDateTime(),
      'order_type'     => new sfValidatorString(array('max_length' => 10)),
      'order_amount'   => new sfValidatorNumber(),
      'order_channel'  => new sfValidatorString(array('max_length' => 50)),
      'order_teller'   => new sfValidatorInteger(),
      'order_s'        => new sfValidatorString(array('max_length' => 150)),
      'status'         => new sfValidatorInteger(array('required' => false)),
      'transfer_sap'   => new sfValidatorInteger(array('required' => false)),
      'try_count'      => new sfValidatorInteger(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
      'related_account'=> new sfValidatorString(array('max_length' => 50)),
    ));

    $this->widgetSchema->setNameFormat('bank_tdb[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankTdb';
  }

}
