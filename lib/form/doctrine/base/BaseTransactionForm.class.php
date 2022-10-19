<?php

/**
 * Transaction form base class.
 *
 * @method Transaction getObject() Returns the current form's model object
 *
 * @package    BANKGW
 * @subpackage form
 * @author     Belbayar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTransactionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'bank_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bank'), 'add_empty' => false)),
      'bank_account'    => new sfWidgetFormInputText(),
      'related_account' => new sfWidgetFormInputText(),
      'order_id'        => new sfWidgetFormInputText(),
      'order_date'      => new sfWidgetFormDateTime(),
      'order_p'         => new sfWidgetFormTextarea(),
      'order_type'      => new sfWidgetFormInputText(),
      'order_amount'    => new sfWidgetFormInputText(),
      'order_branch'    => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormInputText(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'updated_user_id' => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'bank_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bank'))),
      'bank_account'    => new sfValidatorString(array('max_length' => 50)),
      'related_account' => new sfValidatorString(array('max_length' => 50)),
      'order_id'        => new sfValidatorString(array('max_length' => 30)),
      'order_date'      => new sfValidatorDateTime(),
      'order_p'         => new sfValidatorString(array('max_length' => 500)),
      'order_type'      => new sfValidatorString(array('max_length' => 10)),
      'order_amount'    => new sfValidatorNumber(),
      'order_branch'    => new sfValidatorString(array('max_length' => 50)),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'updated_at'      => new sfValidatorDateTime(),
      'updated_user_id' => new sfValidatorInteger(),
      'created_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('transaction[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transaction';
  }

}
