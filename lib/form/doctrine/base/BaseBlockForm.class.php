<?php

/**
 * Block form base class.
 *
 * @method Block getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBlockForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputText(),
      'bank_id'         => new sfWidgetFormInputText(),
      'block'           => new sfWidgetFormInputText(),
      'updated_user_id' => new sfWidgetFormInputText(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorInteger(),
      'bank_id'         => new sfValidatorInteger(),
      'block'           => new sfValidatorInteger(array('required' => false)),
      'updated_user_id' => new sfValidatorInteger(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('block[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Block';
  }

}
