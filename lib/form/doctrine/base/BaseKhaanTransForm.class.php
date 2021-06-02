<?php

/**
 * KhaanTrans form base class.
 *
 * @method KhaanTrans getObject() Returns the current form's model object
 *
 * @package    BANKGW
 * @subpackage form
 * @author     Belbayar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseKhaanTransForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'transition' => new sfWidgetFormTextarea(),
      'status'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'transition' => new sfValidatorString(),
      'status'     => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('khaan_trans[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'KhaanTrans';
  }

}
