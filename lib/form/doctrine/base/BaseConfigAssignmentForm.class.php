<?php

/**
 * ConfigAssignment form base class.
 *
 * @method ConfigAssignment getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseConfigAssignmentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'filter'          => new sfWidgetFormInputText(),
      'type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentType'), 'add_empty' => false)),
      'acc_type'        => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormInputText(),
      'priority'        => new sfWidgetFormInputText(),
      'filter_type'     => new sfWidgetFormInputText(),
      'filter_day'      => new sfWidgetFormInputText(),
      'updated_user_id' => new sfWidgetFormInputText(),
      'username'        => new sfWidgetFormInputText(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'filter'          => new sfValidatorString(array('max_length' => 100)),
      'type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentType'))),
      'acc_type'        => new sfValidatorInteger(array('required' => false)),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'priority'        => new sfValidatorInteger(),
      'filter_type'     => new sfValidatorInteger(array('required' => false)),
      'filter_day'      => new sfValidatorInteger(array('required' => false)),
      'updated_user_id' => new sfValidatorInteger(),
      'username'        => new sfValidatorString(array('max_length' => 50)),
      'updated_at'      => new sfValidatorDateTime(),
      'created_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('config_assignment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ConfigAssignment';
  }

}
