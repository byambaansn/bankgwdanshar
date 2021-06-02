<?php

/**
 * BankpaymentMobinet form base class.
 *
 * @method BankpaymentMobinet getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankpaymentMobinetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'bankpayment_id' => new sfWidgetFormInputHidden(),
      'bundle'         => new sfWidgetFormInputText(),
      'bundle_name'    => new sfWidgetFormInputText(),
      'speed'          => new sfWidgetFormInputText(),
      'extent_month'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'bankpayment_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('bankpayment_id')), 'empty_value' => $this->getObject()->get('bankpayment_id'), 'required' => false)),
      'bundle'         => new sfValidatorInteger(),
      'bundle_name'    => new sfValidatorString(array('max_length' => 50)),
      'speed'          => new sfValidatorInteger(),
      'extent_month'   => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('bankpayment_mobinet[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankpaymentMobinet';
  }

}
