<?php

/**
 * BankpaymentTransfertype form base class.
 *
 * @method BankpaymentTransfertype getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentTransfertypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'c_type'        => new sfWidgetFormInputHidden(),
      'c_bank'        => new sfWidgetFormInputHidden(),
      'c_account'     => new sfWidgetFormInputHidden(),
      'c_regex'       => new sfWidgetFormInputText(),
      'c_mustmatch'   => new sfWidgetFormInputText(),
      'c_matchindex'  => new sfWidgetFormInputText(),
      'c_active'      => new sfWidgetFormInputText(),
      'c_priority'    => new sfWidgetFormInputText(),
      'c_description' => new sfWidgetFormTextarea(),
      'c_rule'        => new sfWidgetFormInputText(),
      'payment_code'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'c_type'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('c_type')), 'empty_value' => $this->getObject()->get('c_type'), 'required' => false)),
      'c_bank'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('c_bank')), 'empty_value' => $this->getObject()->get('c_bank'), 'required' => false)),
      'c_account'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('c_account')), 'empty_value' => $this->getObject()->get('c_account'), 'required' => false)),
      'c_regex'       => new sfValidatorString(array('max_length' => 250)),
      'c_mustmatch'   => new sfValidatorPass(array('required' => false)),
      'c_matchindex'  => new sfValidatorInteger(array('required' => false)),
      'c_active'      => new sfValidatorPass(array('required' => false)),
      'c_priority'    => new sfValidatorInteger(array('required' => false)),
      'c_description' => new sfValidatorString(array('max_length' => 450, 'required' => false)),
      'c_rule'        => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'payment_code'  => new sfValidatorString(array('max_length' => 10)),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_transfertype[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentTransfertype';
  }

}
