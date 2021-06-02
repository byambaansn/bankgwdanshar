<?php

/**
 * BankAccount form base class.
 *
 * @method BankAccount getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBankAccountForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'bank_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bank'), 'add_empty' => false)),
      'company_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Company'), 'add_empty' => false)),
      'type'           => new sfWidgetFormInputText(),
      'account'        => new sfWidgetFormInputText(),
      'account_alias'  => new sfWidgetFormInputText(),
      'sap_account'    => new sfWidgetFormInputText(),
      'sap_gl_account' => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'bank_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bank'))),
      'company_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Company'))),
      'type'           => new sfValidatorInteger(),
      'account'        => new sfValidatorString(array('max_length' => 100)),
      'account_alias'  => new sfValidatorString(array('max_length' => 20)),
      'sap_account'    => new sfValidatorString(array('max_length' => 50)),
      'sap_gl_account' => new sfValidatorString(array('max_length' => 50)),
      'status'         => new sfValidatorInteger(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('bank_account[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BankAccount';
  }

}
