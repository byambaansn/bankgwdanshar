<?php

/**
 * TransactionPayment form base class.
 *
 * @method TransactionPayment getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTransactionPaymentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'transaction_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Transaction'), 'add_empty' => false)),
      'payment_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Payment'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'transaction_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Transaction'))),
      'payment_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Payment'))),
    ));

    $this->widgetSchema->setNameFormat('transaction_payment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TransactionPayment';
  }

}
