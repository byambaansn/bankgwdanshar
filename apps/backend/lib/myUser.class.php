<?php

class myUser extends sfBasicSecurityUser
{

    public function signIn($user, $permissions)
    {
        $this->setAuthenticated(true);

        $this->setAttribute('user_id', $user->get('id'));
        $this->setAttribute('username', $user->get('username'));
        $this->setAttribute('name', AppTools::utf8_substr($user->get('lastname'), 0, 1) . '.' . $user->get('firstname'));
        $this->setAttribute('firstname', $user->get('firstname'));
        $this->setAttribute('lastname', $user->get('lastname'));
        
        $perm = array();
        
        foreach ($permissions as $permission) {
            $this->addCredential($permission['view']);
            $perm[] = $permission['view_name'];
        }
        
        $this->setAttribute('permissions', $perm);
    }

    public function signOut()
    {
        $this->getAttributeHolder()->removeNamespace();

        $this->setAuthenticated(false);
        $this->clearCredentials();
    }

    public function getId()
    {
        return $this->getAttribute('user_id', 0);
    }

    public function getName()
    {
        return $this->getAttribute('name', '');
    }

    public function getFirstname()
    {
        return $this->getAttribute('firstname', '');
    }

    public function getLastname()
    {
        return $this->getAttribute('lastname', '');
    }

    public function getUsername()
    {
        return $this->getAttribute('username', '');
    }

}
