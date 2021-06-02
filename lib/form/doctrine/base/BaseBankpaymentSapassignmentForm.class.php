<?php

/**
 * BankpaymentSapassignment form base class.
 *
 * @method BankpaymentSapassignment getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentSapassignmentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'billcyclecode' => new sfWidgetFormInputHidden(),
      'assignment'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'billcyclecode' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('billcyclecode')), 'empty_value' => $this->getObject()->get('billcyclecode'), 'required' => false)),
      'assignment'    => new sfValidatorString(array('max_length' => 16)),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_sapassignment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentSapassignment';
  }

}
