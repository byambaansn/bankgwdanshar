<?php

/**
 * BankpaymentTransferrule form base class.
 *
 * @method BankpaymentTransferrule getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentTransferruleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'c_rule'        => new sfWidgetFormInputHidden(),
      'c_sequence'    => new sfWidgetFormInputHidden(),
      'c_action'      => new sfWidgetFormInputText(),
      'c_param'       => new sfWidgetFormInputText(),
      'c_description' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'c_rule'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('c_rule')), 'empty_value' => $this->getObject()->get('c_rule'), 'required' => false)),
      'c_sequence'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('c_sequence')), 'empty_value' => $this->getObject()->get('c_sequence'), 'required' => false)),
      'c_action'      => new sfValidatorString(array('max_length' => 150)),
      'c_param'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'c_description' => new sfValidatorString(array('max_length' => 450, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_transferrule[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentTransferrule';
  }

}
