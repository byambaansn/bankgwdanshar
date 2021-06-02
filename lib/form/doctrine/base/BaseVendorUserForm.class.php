<?php

/**
 * VendorUser form base class.
 *
 * @method VendorUser getObject() Returns the current form's model object
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVendorUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'vendor_id' => new sfWidgetFormInputHidden(),
      'username'  => new sfWidgetFormInputText(),
      'password'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'vendor_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('vendor_id')), 'empty_value' => $this->getObject()->get('vendor_id'), 'required' => false)),
      'username'  => new sfValidatorString(array('max_length' => 50)),
      'password'  => new sfValidatorString(array('max_length' => 50)),
    ));

    $this->widgetSchema->setNameFormat('vendor_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VendorUser';
  }

}
