<?php

/**
 * Vendor form base class.
 *
 * @method Vendor getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVendorForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'parent_id'   => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'source_code' => new sfWidgetFormInputText(),
      'status'      => new sfWidgetFormInputText(),
      'updated_at'  => new sfWidgetFormDateTime(),
      'created_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parent_id'   => new sfValidatorInteger(array('required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 250)),
      'source_code' => new sfValidatorInteger(),
      'status'      => new sfValidatorInteger(array('required' => false)),
      'updated_at'  => new sfValidatorDateTime(),
      'created_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vendor[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Vendor';
  }

}
